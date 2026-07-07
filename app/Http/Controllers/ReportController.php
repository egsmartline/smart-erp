<?php

namespace App\Http\Controllers;

use App\Models\JournalEntryLine;
use App\Models\Account;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Http\Request;

class ReportController extends TenantAwareController
{
    public function trialBalance(Request $request)
    {
        $dateTo = $request->date_to ?? now()->toDateString();

        $accounts = $this->tenantQuery(Account::class)
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function ($account) use ($dateTo) {
                $lines = JournalEntryLine::where('tenant_id', $this->getTenantId())
                    ->where('account_id', $account->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('is_posted', true)->where('date', '<=', $dateTo))
                    ->get();

                $account->total_debit = $lines->sum('debit');
                $account->total_credit = $lines->sum('credit');
                $account->balance = $account->total_debit - $account->total_credit;

                return $account;
            })
            ->filter(fn($a) => $a->total_debit > 0 || $a->total_credit > 0);

        $totalDebit = $accounts->sum('total_debit');
        $totalCredit = $accounts->sum('total_credit');

        return view('reports.trial-balance', compact('accounts', 'totalDebit', 'totalCredit', 'dateTo'));
    }

    public function generalLedger(Request $request)
    {
        $accountId = $request->account_id;
        $dateFrom = $request->date_from ?? now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $query = JournalEntryLine::where('tenant_id', $this->getTenantId())
            ->whereHas('journalEntry', fn($q) => $q->where('is_posted', true));

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        $lines = $query->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['journalEntry', 'account'])
            ->orderBy('created_at')
            ->get();

        $accounts = $this->tenantQuery(Account::class)->where('is_active', true)->orderBy('code')->get();

        return view('reports.general-ledger', compact('lines', 'accounts', 'accountId', 'dateFrom', 'dateTo'));
    }

    public function incomeStatement(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $revenueAccounts = $this->getAccountBalances('revenue', $dateFrom, $dateTo);
        $expenseAccounts = $this->getAccountBalances('expense', $dateFrom, $dateTo);

        $totalRevenue = $revenueAccounts->sum('balance');
        $totalExpenses = $expenseAccounts->sum('balance');
        $netIncome = $totalRevenue - $totalExpenses;

        return view('reports.income-statement', compact('revenueAccounts', 'expenseAccounts', 'totalRevenue', 'totalExpenses', 'netIncome', 'dateFrom', 'dateTo'));
    }

    public function balanceSheet(Request $request)
    {
        $dateTo = $request->date_to ?? now()->toDateString();

        $assetAccounts = $this->getAccountBalances('asset', null, $dateTo);
        $liabilityAccounts = $this->getAccountBalances('liability', null, $dateTo);
        $equityAccounts = $this->getAccountBalances('equity', null, $dateTo);

        $totalAssets = $assetAccounts->sum('balance');
        $totalLiabilities = $liabilityAccounts->sum('balance');
        $totalEquity = $equityAccounts->sum('balance');

        return view('reports.balance-sheet', compact('assetAccounts', 'liabilityAccounts', 'equityAccounts', 'totalAssets', 'totalLiabilities', 'totalEquity', 'dateTo'));
    }

    public function customerStatement(Request $request)
    {
        $customerId = $request->customer_id;
        $dateFrom = $request->date_from ?? now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();

        $customer = null;
        $transactions = collect();
        $openingBalance = 0;

        if ($customerId) {
            $customer = Customer::find($customerId);
            $openingBal = (float) ($customer->opening_balance ?? 0);
            $openingBalance = $customer->opening_balance_type === 'credit' ? -$openingBal : $openingBal;

            $invoices = SalesInvoice::where('tenant_id', $this->getTenantId())
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->with('customer')
                ->get();

            foreach ($invoices as $inv) {
                $transactions->push([
                    'date' => $inv->date,
                    'type' => 'فاتورة بيع',
                    'badge' => 'bg-blue-100 text-blue-800',
                    'reference' => $inv->invoice_number,
                    'amount' => (float) $inv->total,
                    'paid' => (float) $inv->paid_amount,
                    'due' => (float) $inv->due_amount,
                    'payment_status' => $inv->payment_status,
                ]);
            }

            $payments = Payment::where('tenant_id', $this->getTenantId())
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('type', 'receipt')
                ->get();

            foreach ($payments as $pay) {
                $transactions->push([
                    'date' => $pay->date,
                    'type' => 'سند قبض',
                    'badge' => 'bg-emerald-100 text-emerald-800',
                    'reference' => $pay->payment_number,
                    'amount' => -(float) $pay->amount,
                    'paid' => 0,
                    'due' => 0,
                    'payment_status' => null,
                ]);
            }

            $transactions = $transactions->sortBy(function ($t) {
                return ($t['date'] ? $t['date']->format('Y-m-d') : '0000-00-00');
            })->values();
        }

        return view('reports.customer-statement', compact('customers', 'customer', 'transactions', 'openingBalance', 'customerId', 'dateFrom', 'dateTo'));
    }

    public function supplierStatement(Request $request)
    {
        $supplierId = $request->supplier_id;
        $dateFrom = $request->date_from ?? now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();

        $supplier = null;
        $transactions = collect();
        $openingBalance = 0;

        if ($supplierId) {
            $supplier = Supplier::find($supplierId);
            $openingBal = (float) ($supplier->opening_balance ?? 0);
            $openingBalance = $supplier->opening_balance_type === 'credit' ? $openingBal : -$openingBal;

            $invoices = PurchaseInvoice::where('tenant_id', $this->getTenantId())
                ->where('supplier_id', $supplierId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->with('supplier')
                ->get();

            foreach ($invoices as $inv) {
                $transactions->push([
                    'date' => $inv->invoice_date ?? $inv->created_at,
                    'type' => 'فاتورة شراء',
                    'badge' => 'bg-orange-100 text-orange-800',
                    'reference' => $inv->invoice_number,
                    'amount' => (float) $inv->total,
                    'paid' => (float) $inv->paid_amount,
                    'due' => (float) $inv->due_amount,
                    'payment_status' => $inv->payment_status,
                ]);
            }

            $payments = Payment::where('tenant_id', $this->getTenantId())
                ->where('supplier_id', $supplierId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('type', 'payment')
                ->get();

            foreach ($payments as $pay) {
                $transactions->push([
                    'date' => $pay->date,
                    'type' => 'سند صرف',
                    'badge' => 'bg-emerald-100 text-emerald-800',
                    'reference' => $pay->payment_number,
                    'amount' => -(float) $pay->amount,
                    'paid' => 0,
                    'due' => 0,
                    'payment_status' => null,
                ]);
            }

            $transactions = $transactions->sortBy(function ($t) {
                return ($t['date'] ? $t['date']->format('Y-m-d') : '0000-00-00');
            })->values();
        }

        return view('reports.supplier-statement', compact('suppliers', 'supplier', 'transactions', 'openingBalance', 'supplierId', 'dateFrom', 'dateTo'));
    }

    public function vatReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $salesTax = SalesInvoice::where('tenant_id', $this->getTenantId())
            ->where('status', 'posted')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->sum('tax_amount');

        $purchaseTax = PurchaseInvoice::where('tenant_id', $this->getTenantId())
            ->where('status', 'posted')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->sum('tax_amount');

        $netVat = $salesTax - $purchaseTax;

        return view('reports.vat', compact('salesTax', 'purchaseTax', 'netVat', 'dateFrom', 'dateTo'));
    }

    public function salesReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $invoices = SalesInvoice::where('tenant_id', $this->getTenantId())
            ->where('status', 'posted')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->with('customer')
            ->get();

        $totalSales = $invoices->sum('total');
        $totalTax = $invoices->sum('tax_amount');

        return view('reports.sales', compact('invoices', 'totalSales', 'totalTax', 'dateFrom', 'dateTo'));
    }

    public function purchasesReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $invoices = PurchaseInvoice::where('tenant_id', $this->getTenantId())
            ->where('status', 'posted')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->with('supplier')
            ->get();

        $totalPurchases = $invoices->sum('total');
        $totalTax = $invoices->sum('tax_amount');

        return view('reports.purchases', compact('invoices', 'totalPurchases', 'totalTax', 'dateFrom', 'dateTo'));
    }

    public function inventoryReport(Request $request)
    {
        $items = $this->tenantQuery(Item::class)
            ->with(['category', 'unit'])
            ->orderBy('name')
            ->get();

        $totalValue = $items->sum(fn($item) => $item->current_stock * $item->purchase_price);

        return view('reports.inventory', compact('items', 'totalValue'));
    }

    public function cashFlow(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $receipts = $this->tenantQuery(\App\Models\Payment::class)
            ->where('type', 'receipt')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->sum('amount_in_currency');

        $payments = $this->tenantQuery(\App\Models\Payment::class)
            ->where('type', 'payment')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->sum('amount_in_currency');

        $netCashFlow = $receipts - $payments;

        return view('reports.cash-flow', compact('receipts', 'payments', 'netCashFlow', 'dateFrom', 'dateTo'));
    }

    public function accountStatement(Request $request)
    {
        $accountId = $request->account_id;
        $dateFrom = $request->date_from ?? now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $accounts = $this->tenantQuery(Account::class)->where('is_active', true)->orderBy('code')->get();

        $lines = collect();
        $openingBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $closingBalance = 0;

        if ($accountId) {
            $account = $accounts->firstWhere('id', $accountId);

            $query = JournalEntryLine::where('tenant_id', $this->getTenantId())
                ->where('account_id', $accountId)
                ->whereHas('journalEntry', fn($q) => $q->where('is_posted', true))
                ->with(['journalEntry', 'account']);

            $allLines = $query->orderBy('id')->get();

            $beforePeriod = $allLines->filter(fn($l) => $l->journalEntry->date < $dateFrom);
            $isDebit = in_array($account?->type, ['asset', 'assets', 'expense', 'expenses']);
            $netBefore = $beforePeriod->sum('debit') - $beforePeriod->sum('credit');
            $openingBalance = ($isDebit ? $netBefore : -$netBefore) + ($account->opening_balance ?? 0);

            $lines = $allLines->filter(fn($l) => $l->journalEntry->date >= $dateFrom && $l->journalEntry->date <= $dateTo)
                ->sortBy(fn($l) => $l->journalEntry->date . sprintf('%010d', $l->id))
                ->values();

            $running = $openingBalance;
            $lines = $lines->map(function ($l) use ($isDebit, &$running) {
                $l->running_balance = $isDebit
                    ? $running + $l->debit - $l->credit
                    : $running - $l->debit + $l->credit;
                $running = $l->running_balance;
                return $l;
            });

            $totalDebit = $lines->sum('debit');
            $totalCredit = $lines->sum('credit');
            $netChange = $totalDebit - $totalCredit;
            $closingBalance = $isDebit ? $openingBalance + $netChange : $openingBalance - $netChange;
        }

        return view('reports.account-statement', compact(
            'accounts', 'accountId', 'dateFrom', 'dateTo',
            'lines', 'openingBalance', 'totalDebit', 'totalCredit', 'closingBalance'
        ));
    }

    public function dashboard(Request $request)
    {
        $totalItems = $this->tenantQuery(Item::class)->count();
        $totalCustomers = $this->tenantQuery(Customer::class)->where('is_active', true)->count();
        $totalSuppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->count();

        $totalSales = SalesInvoice::where('tenant_id', $this->getTenantId())
            ->where('status', 'posted')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('total');

        $totalPurchases = PurchaseInvoice::where('tenant_id', $this->getTenantId())
            ->where('status', 'posted')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('total');

        $totalReceivable = SalesInvoice::where('tenant_id', $this->getTenantId())
            ->where('status', 'posted')
            ->sum('total');

        $totalPayable = PurchaseInvoice::where('tenant_id', $this->getTenantId())
            ->where('status', 'posted')
            ->sum('total');

        return view('reports.dashboard', compact(
            'totalItems', 'totalCustomers', 'totalSuppliers',
            'totalSales', 'totalPurchases', 'totalReceivable', 'totalPayable'
        ));
    }

    private function getAccountBalances($type, $dateFrom = null, $dateTo)
    {
        $accounts = $this->tenantQuery(Account::class)
            ->where('type', $type)
            ->where('is_header', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function ($account) use ($dateFrom, $dateTo) {
                $query = JournalEntryLine::where('tenant_id', $this->getTenantId())
                    ->where('account_id', $account->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('is_posted', true));

                if ($dateFrom) {
                    $query->whereHas('journalEntry', fn($q) => $q->where('date', '>=', $dateFrom));
                }
                if ($dateTo) {
                    $query->whereHas('journalEntry', fn($q) => $q->where('date', '<=', $dateTo));
                }

                $lines = $query->get();
                $account->total_debit = $lines->sum('debit');
                $account->total_credit = $lines->sum('credit');
                $account->balance = abs($account->total_debit - $account->total_credit);

                return $account;
            })
            ->filter(fn($a) => $a->balance > 0);

        return $accounts;
    }
}
