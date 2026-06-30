<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Currency;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceLine;
use App\Models\SalesReturn;
use App\Models\SalesReturnLine;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\SalesDeliveryNote;
use App\Models\SalesDeliveryNoteLine;
use App\Models\PurchaseReceiptNote;
use App\Models\PurchaseReceiptNoteLine;
use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferLine;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\BankStatement;
use App\Models\BankStatementLine;
use App\Models\BankTransaction;
use App\Models\TreasuryTransaction;
use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentLine;
use App\Models\Custody;
use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\Loan;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\Contract;

class SettingController extends TenantAwareController
{
    public function index()
    {
        $currencies = Currency::where('tenant_id', $this->getTenantId())->get();
        $companies = $this->tenantQuery(Company::class)->get();

        $company = $this->tenantQuery(Company::class)->first();
        if (!$company) {
            $company = new Company();
            $company->tenant_id = $this->getTenantId();
        }

        return view('settings.index', compact('company', 'currencies', 'companies'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'currency_id' => 'required|exists:currencies,id',
            'secondary_currency_id' => 'nullable|exists:currencies,id',
        ]);

        $company = $this->tenantQuery(Company::class)->first();
        if (!$company) {
            $company = new Company();
            $company->tenant_id = $this->getTenantId();
        }

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $currency = Currency::find($validated['currency_id']);
        $validated['currency_code'] = $currency ? $currency->code : 'SAR';
        unset($validated['currency_id']);

        if (!empty($validated['secondary_currency_id'])) {
            $secCurrency = Currency::find($validated['secondary_currency_id']);
            $validated['secondary_currency_code'] = $secCurrency ? $secCurrency->code : null;
            unset($validated['secondary_currency_id']);
        } else {
            $validated['secondary_currency_id'] = null;
            $validated['secondary_currency_code'] = null;
        }

        $validated['name'] = $validated['company_name'];
        unset($validated['company_name']);

        $company->update($validated);

        return redirect()->route('settings.index')->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|max:2048',
        ]);

        $company = $this->tenantQuery(Company::class)->first();
        if (!$company) {
            return back()->with('error', 'يجب إنشاء الشركة أولاً');
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $company->update(['logo' => $path]);
        }

        return back()->with('success', 'تم تحديث شعار الشركة بنجاح');
    }

    public function reset(Request $request)
    {
        $request->validate(['confirm' => 'required|accepted']);

        $tenantId = $this->getTenantId();

        $tables = [
            // Phase 1: detail/line tables (children, deleted first)
            'bank_statement_lines',
            'budget_lines',
            'inventory_adjustment_lines',
            'journal_entry_lines',
            'payslip',
            'purchase_invoice_lines',
            'purchase_receipt_note_lines',
            'purchase_order_lines',
            'purchase_return_lines',
            'quotation_lines',
            'sales_delivery_note_lines',
            'sales_invoice_lines',
            'sales_return_lines',
            'stock_transfer_lines',

            // Phase 2: leaf tables (no FKs to other transaction tables)
            'attendance',
            'bank_transactions',
            'expenses',
            'leaves',
            'loans',
            'payments',
            'product_lots',
            'product_variants',
            'reordering_rules',
            'stock_movements',
            'treasury_transactions',

            // Phase 3: headers that reference other headers
            'purchase_receipt_notes',
            'purchase_returns',
            'quotations',
            'sales_returns',

            // Phase 4: independent headers (no FKs to other transaction tables)
            'bank_statements',
            'budgets',
            'contracts',
            'custodies',
            'inventory_adjustments',
            'journal_entries',
            'payroll',
            'purchase_invoices',
            'purchase_orders',
            'sales_delivery_notes',
            'sales_invoices',
            'stock_transfers',
        ];

        $totalDeleted = 0;

        DB::beginTransaction();
        try {
            foreach ($tables as $table) {
                $deleted = DB::table($table)->where('tenant_id', $tenantId)->delete();
                $totalDeleted += $deleted;
            }

            $accountsUpdated = Account::where('tenant_id', $tenantId)->update([
                'opening_balance' => 0,
                'current_balance' => 0,
                'balance' => 0,
            ]);

            DB::commit();
            return redirect()->route('settings.index')->with('success', "تم تصفير الحسابات والبيانات بنجاح (معرف المستأجر: {$tenantId}، عدد السجلات المحذوفة: {$totalDeleted}، الحسابات المحدثة: {$accountsUpdated})");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء التصفير: ' . $e->getMessage());
        }
    }
}
