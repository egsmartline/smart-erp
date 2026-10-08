<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Concerns\AuthorizesTenantAccess;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Models\SalesInvoiceInstallment;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\Currency;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use App\Models\JournalEntry;
use App\Models\Tax;
use App\Models\Payment;
use App\Models\CashTreasury;
use App\Models\BankAccount;
use App\Models\TreasuryTransaction;
use App\Models\BankTransaction;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends TenantAwareController
{
    use AuthorizesTenantAccess;

    public function index(Request $request)
    {
        $query = $this->tenantQuery(SalesInvoice::class)
            ->with('customer');

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $invoices = $query->orderBy('date', 'desc')->paginate(15);
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();

        return view('sales-invoices.index', compact('invoices', 'customers'));
    }

    public function create()
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = Item::where('is_active', true)->orderBy('sku')->get();
        $currencies = Currency::where('is_active', true)->get();
        $invoiceNumber = $this->generateInvoiceNumber();
        $defaultTaxRate = $this->getDefaultTaxRate();

        return view('sales-invoices.create', compact('customers', 'warehouses', 'items', 'currencies', 'invoiceNumber', 'defaultTaxRate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percent',
            'discount_percent_inv' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'lines.*.warehouse_id' => 'required|exists:warehouses,id',
            'installments' => 'nullable|array',
            'installments.*.amount' => 'nullable|numeric|min:0',
            'installments.*.due_date' => 'nullable|date|after_or_equal:date',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            $lineData = [];
            foreach ($validated['lines'] as $line) {
                $lineSubtotal = $line['quantity'] * $line['unit_price'];
                $lineDiscount = $lineSubtotal * (($line['discount_percent'] ?? 0) / 100);
                $lineAfterDiscount = $lineSubtotal - $lineDiscount;
                $lineTax = $lineAfterDiscount * (($line['tax_rate'] ?? 0) / 100);
                $lineTotal = $lineAfterDiscount + $lineTax;

                $subtotal += $lineSubtotal;
                $totalDiscount += $lineDiscount;
                $totalTax += $lineTax;

                $lineData[] = [
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $line['item_id'],
                    'description' => $line['description'] ?? null,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_rate' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                    'warehouse_id' => $line['warehouse_id'],
                ];
            }

            $overallDiscount = $validated['discount_amount'] ?? 0;
            $grandTotal = $subtotal - $totalDiscount - $overallDiscount + $totalTax + ($validated['shipping_amount'] ?? 0);

            $installments = [];
            foreach (($validated['installments'] ?? []) as $inst) {
                if (!empty($inst['amount']) && !empty($inst['due_date'])) {
                    $installments[] = [
                        'amount' => $inst['amount'],
                        'due_date' => $inst['due_date'],
                        'paid_amount' => 0,
                    ];
                }
            }

            if (count($installments) > 0) {
                $due_date = max(array_column($installments, 'due_date'));
            } else {
                $due_date = $validated['due_date'];
            }

            $invoice = SalesInvoice::create([
                'tenant_id' => $this->getTenantId(),
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'cashier_id' => auth()->id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'date' => $validated['date'],
                'due_date' => $due_date,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount + $overallDiscount,
                'discount_percent' => 0,
                'tax_amount' => $totalTax,
                'shipping_amount' => $validated['shipping_amount'] ?? 0,
                'total' => $grandTotal,
                'paid_amount' => 0,
                'due_amount' => $grandTotal,
                'currency_id' => $validated['currency_id'] ?? null,
                'exchange_rate' => 1,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($lineData as $data) {
                $data['sales_invoice_id'] = $invoice->id;
                SalesInvoiceLine::create($data);
            }

            foreach ($installments as $inst) {
                $inst['sales_invoice_id'] = $invoice->id;
                SalesInvoiceInstallment::create($inst);
            }

            DB::commit();

            return redirect()->route('sales-invoices.show', $invoice)
                ->with('success', 'تم إنشاء فاتورة المبيعات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    public function show(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['customer', 'warehouse', 'lines.item', 'cashier', 'returns', 'currency', 'installments']);
        $treasuries = $this->tenantQuery(CashTreasury::class)->where('is_active', true)->orderBy('name')->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->where('is_active', true)->orderBy('account_name')->get();
        return view('sales-invoices.show', compact('salesInvoice', 'treasuries', 'bankAccounts'));
    }

    public function edit(SalesInvoice $salesInvoice)
    {
        if (!in_array($salesInvoice->status, ['draft', 'posted'])) {
            return back()->with('error', 'لا يمكن تعديل هذه الفاتورة');
        }

        $salesInvoice->load(['lines.item', 'installments']);
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = Item::where('is_active', true)->orderBy('sku')->get();
        $currencies = Currency::where('is_active', true)->get();

        $defaultTaxRate = $this->getDefaultTaxRate();

        return view('sales-invoices.edit', compact('salesInvoice', 'customers', 'warehouses', 'items', 'currencies', 'defaultTaxRate'));
    }

    public function update(Request $request, SalesInvoice $salesInvoice)
    {
        if (!in_array($salesInvoice->status, ['draft', 'posted'])) {
            return back()->with('error', 'لا يمكن تعديل هذه الفاتورة');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percent',
            'discount_percent_inv' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'lines.*.warehouse_id' => 'required|exists:warehouses,id',
            'installments' => 'nullable|array',
            'installments.*.amount' => 'nullable|numeric|min:0',
            'installments.*.due_date' => 'nullable|date|after_or_equal:date',
        ]);

        DB::beginTransaction();

        try {
            if ($salesInvoice->status === 'posted') {
                foreach ($salesInvoice->lines as $line) {
                    $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                        ->where('warehouse_id', $line->warehouse_id)
                        ->first();
                    if ($itemWarehouse) {
                        $itemWarehouse->increment('quantity', $line->quantity);
                    }

                    StockMovement::where('reference_type', SalesInvoice::class)
                        ->where('reference_id', $salesInvoice->id)
                        ->where('item_id', $line->item_id)
                        ->delete();
                }

                app(JournalService::class)->reverseEntryByReference($salesInvoice->invoice_number, 'sales');
            }

            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            $lineData = [];
            foreach ($validated['lines'] as $line) {
                $lineSubtotal = $line['quantity'] * $line['unit_price'];
                $lineDiscount = $lineSubtotal * (($line['discount_percent'] ?? 0) / 100);
                $lineAfterDiscount = $lineSubtotal - $lineDiscount;
                $lineTax = $lineAfterDiscount * (($line['tax_rate'] ?? 0) / 100);
                $lineTotal = $lineAfterDiscount + $lineTax;

                $subtotal += $lineSubtotal;
                $totalDiscount += $lineDiscount;
                $totalTax += $lineTax;

                $lineData[] = [
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $line['item_id'],
                    'description' => $line['description'] ?? null,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_rate' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                    'warehouse_id' => $line['warehouse_id'],
                ];
            }

            $overallDiscount = $validated['discount_amount'] ?? 0;
            $grandTotal = $subtotal - $totalDiscount - $overallDiscount + $totalTax + ($validated['shipping_amount'] ?? 0);

            $installments = [];
            foreach (($validated['installments'] ?? []) as $inst) {
                if (!empty($inst['amount']) && !empty($inst['due_date'])) {
                    $installments[] = [
                        'amount' => $inst['amount'],
                        'due_date' => $inst['due_date'],
                        'paid_amount' => 0,
                    ];
                }
            }

            if (count($installments) > 0) {
                $due_date = max(array_column($installments, 'due_date'));
            } else {
                $due_date = $validated['due_date'];
            }

            $salesInvoice->update([
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'currency_id' => $validated['currency_id'] ?? null,
                'date' => $validated['date'],
                'due_date' => $due_date,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount + $overallDiscount,
                'tax_amount' => $totalTax,
                'shipping_amount' => $validated['shipping_amount'] ?? 0,
                'total' => $grandTotal,
                'due_amount' => $grandTotal - $salesInvoice->paid_amount,
                'notes' => $validated['notes'] ?? null,
            ]);

            $salesInvoice->lines()->delete();

            foreach ($lineData as $data) {
                $data['sales_invoice_id'] = $salesInvoice->id;
                SalesInvoiceLine::create($data);
            }

            $salesInvoice->installments()->delete();

            foreach ($installments as $inst) {
                $inst['sales_invoice_id'] = $salesInvoice->id;
                SalesInvoiceInstallment::create($inst);
            }

            if ($salesInvoice->status === 'posted') {
                $salesInvoice->fresh()->load('lines.item');

                foreach ($salesInvoice->lines as $line) {
                    $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                        ->where('warehouse_id', $line->warehouse_id)
                        ->first();

                    $available = $itemWarehouse ? $itemWarehouse->quantity : 0;
                    if ($available < $line->quantity) {
                        $itemName = $line->item->name ?? '#' . $line->item_id;
                        return back()->withInput()->with('error', "الرصيد غير كافٍ للصنف {$itemName} (المتوفر: {$available}، المطلوب: {$line->quantity})");
                    }
                }

                foreach ($salesInvoice->lines as $line) {
                    $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                        ->where('warehouse_id', $line->warehouse_id)
                        ->first();

                    if ($itemWarehouse) {
                        $itemWarehouse->decrement('quantity', $line->quantity);
                    }

                    StockMovement::create([
                        'tenant_id' => $this->getTenantId(),
                        'item_id' => $line->item_id,
                        'warehouse_id' => $line->warehouse_id,
                        'type' => 'sale',
                        'quantity' => $line->quantity,
                        'unit_cost' => $line->unit_price,
                        'total_cost' => $line->total,
                        'reference_type' => SalesInvoice::class,
                        'reference_id' => $salesInvoice->id,
                        'description' => 'خروج مخزون - فاتورة مبيعات',
                        'user_id' => auth()->id(),
                    ]);
                }
                $journalService = app(JournalService::class);
                $totalCost = 0;
                foreach ($salesInvoice->lines as $line) {
                    $costPrice = $line->item?->cost_price ?? 0;
                    $totalCost += $costPrice * $line->quantity;
                }
                $lines = $journalService->buildSalesInvoiceLines($salesInvoice->toArray(), $this->getTenantId(), $totalCost);
                if (count($lines) >= 2) {
                    $journalService->createEntry([
                        'tenant_id' => $this->getTenantId(),
                        'date' => $salesInvoice->date->format('Y-m-d'),
                        'description' => 'فاتورة مبيعات - ' . $salesInvoice->invoice_number,
                        'reference' => $salesInvoice->invoice_number,
                        'type' => 'sales',
                        'lines' => $lines,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('sales-invoices.show', $salesInvoice)
                ->with('success', 'تم تحديث فاتورة المبيعات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الفاتورة: ' . $e->getMessage());
        }
    }

    public function destroy(SalesInvoice $salesInvoice)
    {
        $this->authorizeTenant($salesInvoice);
        DB::beginTransaction();

        try {
            $salesInvoice->lines()->delete();
            $salesInvoice->installments()->delete();
            $salesInvoice->delete();

            DB::commit();

            return redirect()->route('sales-invoices.index')
                ->with('success', 'تم حذف فاتورة المبيعات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    public function post(SalesInvoice $salesInvoice)
    {
        if ($salesInvoice->status !== 'draft') {
            return back()->with('error', 'الفاتورة مرحلة بالفعل');
        }

        $salesInvoice->load('lines');

        DB::beginTransaction();

        try {
            $salesInvoice->update([
                'status' => 'posted',
                'payment_status' => $salesInvoice->paid_amount > 0 ? 'partial' : 'unpaid',
            ]);

            foreach ($salesInvoice->lines as $line) {
                $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                    ->where('warehouse_id', $line->warehouse_id)
                    ->first();

                $alreadyDelivered = StockMovement::where('item_id', $line->item_id)
                    ->where('reference_type', 'App\\Models\\SalesDeliveryNote')
                    ->whereNull('deleted_at')
                    ->sum('quantity');

                if ($alreadyDelivered <= 0 && $itemWarehouse) {
                    $itemWarehouse->decrement('quantity', $line->quantity);
                }

                StockMovement::create([
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $line->item_id,
                    'warehouse_id' => $line->warehouse_id,
                    'type' => 'sale',
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->unit_price,
                    'total_cost' => $line->total,
                    'reference_type' => SalesInvoice::class,
                    'reference_id' => $salesInvoice->id,
                    'description' => 'خروج مخزون - فاتورة مبيعات',
                    'user_id' => auth()->id(),
                ]);
            }

            $journalService = app(JournalService::class);
            $totalCost = 0;
            foreach ($salesInvoice->lines as $line) {
                $costPrice = $line->item?->cost_price ?? 0;
                $totalCost += $costPrice * $line->quantity;
            }
            $lines = $journalService->buildSalesInvoiceLines($salesInvoice->toArray(), $this->getTenantId(), $totalCost);
            if (count($lines) >= 2) {
                $journalService->createEntry([
                    'tenant_id' => $this->getTenantId(),
                    'date' => $salesInvoice->date->format('Y-m-d'),
                    'description' => 'فاتورة مبيعات - ' . $salesInvoice->invoice_number,
                    'reference' => $salesInvoice->invoice_number,
                    'type' => 'sales',
                    'lines' => $lines,
                ]);
            }

            DB::commit();

            return back()->with('success', 'تم ترحيل الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الترحيل: ' . $e->getMessage());
        }
    }

    public function void(SalesInvoice $salesInvoice)
    {
        if ($salesInvoice->status !== 'posted') {
            return back()->with('error', 'لا يمكن إلغاء فاتورة غير مرحلة');
        }

        DB::beginTransaction();

        try {
            foreach ($salesInvoice->lines as $line) {
                $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                    ->where('warehouse_id', $line->warehouse_id)
                    ->first();

                if ($itemWarehouse) {
                    $itemWarehouse->increment('quantity', $line->quantity);
                }

                StockMovement::create([
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $line->item_id,
                    'warehouse_id' => $line->warehouse_id,
                    'type' => 'return_in',
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->unit_price,
                    'total_cost' => $line->total,
                    'reference_type' => SalesInvoice::class,
                    'reference_id' => $salesInvoice->id,
                    'description' => 'إدخال مخزون - إلغاء فاتورة مبيعات',
                    'user_id' => auth()->id(),
                ]);
            }

            $salesInvoice->update(['status' => 'voided']);

            app(JournalService::class)->reverseEntryByReference($salesInvoice->invoice_number, 'sales');

            DB::commit();

            return back()->with('success', 'تم إلغاء الفاتورة وإعادة المخزون بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الإلغاء: ' . $e->getMessage());
        }
    }

    public function searchCustomers(Request $request)
    {
        $term = $request->input('search', '');
        $customers = $this->tenantQuery(Customer::class)
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('name_ar', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('mobile', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'name_ar', 'phone', 'mobile', 'balance']);

        return response()->json($customers);
    }

    public function searchItems(Request $request)
    {
        $term = $request->input('search', '');
        $items = $this->tenantQuery(Item::class)
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('name_ar', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'name_ar', 'sku', 'barcode', 'selling_price', 'cost_price', 'tax_rate']);

        return response()->json($items);
    }

    private function getDefaultTaxRate(): float
    {
        $tax = Tax::where('is_default', true)->orWhere('is_active', true)->first();

        return $tax ? (float) $tax->rate : 15;
    }

    protected function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = $this->tenantQuery(SalesInvoice::class)
            ->withTrashed()
            ->whereYear('date', $year)
            ->max('invoice_number');

        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return 'INV-S-' . $year . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    public function settleInstallment(Request $request, SalesInvoice $salesInvoice)
    {
        $validated = $request->validate([
            'installment_id' => 'nullable|exists:sales_invoice_installments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'treasury_id' => 'nullable|exists:cash_treasuries,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $tenantId = $this->getTenantId();
        $userId = auth()->id();

        if ($validated['payment_method'] === 'cash' && empty($validated['treasury_id'])) {
            return back()->with('error', 'الخزينة مطلوبة لطريقة الدفع نقداً');
        }
        if ($validated['payment_method'] === 'bank_transfer' && empty($validated['bank_account_id'])) {
            return back()->with('error', 'الحساب البنكي مطلوب لطريقة الدفع تحويل بنكي');
        }
        if ($validated['payment_method'] === 'check' && empty($request->check_number)) {
            return back()->with('error', 'رقم الشيك مطلوب لطريقة الدفع شيك');
        }

        $installment = null;
        if (!empty($validated['installment_id'])) {
            $installment = SalesInvoiceInstallment::find($validated['installment_id']);
            if (!$installment || $installment->sales_invoice_id !== $salesInvoice->id) {
                return back()->with('error', 'القسط غير صالح لهذه الفاتورة');
            }
        }

        $currency = $salesInvoice->currency ?? $this->tenantQuery(Currency::class)->where('code', 'default')->first();
        $currencyId = $currency?->id;

        DB::beginTransaction();

        try {
            $paymentNumber = $this->generatePaymentNumber();

            $payment = Payment::create([
                'tenant_id' => $tenantId,
                'payment_number' => $paymentNumber,
                'date' => $validated['date'],
                'type' => 'receipt',
                'customer_id' => $salesInvoice->customer_id,
                'invoice_id' => $salesInvoice->id,
                'treasury_id' => $validated['treasury_id'] ?? null,
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'currency_id' => $currencyId,
                'exchange_rate' => 1,
                'amount_in_currency' => $validated['amount'],
                'reference' => $validated['notes'] ?? $paymentNumber,
                'check_number' => $request->check_number ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
                'user_id' => $userId,
            ]);

            if ($validated['payment_method'] === 'cash' && !empty($validated['treasury_id'])) {
                $treasury = CashTreasury::findOrFail($validated['treasury_id']);
                $treasury->increment('current_balance', $validated['amount']);
                TreasuryTransaction::create([
                    'tenant_id' => $tenantId,
                    'treasury_id' => $validated['treasury_id'],
                    'type' => 'in',
                    'amount' => $validated['amount'],
                    'reference_type' => 'payment',
                    'reference_id' => $payment->id,
                    'reference_number' => $paymentNumber,
                    'description' => $validated['notes'] ?? 'تسوية قسط - ' . $salesInvoice->invoice_number,
                    'user_id' => $userId,
                ]);
            } elseif ($validated['payment_method'] === 'bank_transfer' && !empty($validated['bank_account_id'])) {
                $bank = BankAccount::findOrFail($validated['bank_account_id']);
                $bank->increment('current_balance', $validated['amount']);
                BankTransaction::create([
                    'tenant_id' => $tenantId,
                    'bank_account_id' => $validated['bank_account_id'],
                    'type' => 'in',
                    'amount' => $validated['amount'],
                    'reference_type' => 'payment',
                    'reference_id' => $payment->id,
                    'reference_number' => $paymentNumber,
                    'description' => $validated['notes'] ?? 'تسوية قسط - ' . $salesInvoice->invoice_number,
                    'user_id' => $userId,
                ]);
            }

            if ($installment) {
                $instDue = (float) $installment->amount - (float) $installment->paid_amount;
                $apply = min($validated['amount'], $instDue);
                $installment->increment('paid_amount', $apply);
                app(PaymentController::class)->syncInvoicePaidAmount($salesInvoice->id);
            } else {
                app(PaymentController::class)->allocatePaymentToSalesInstallments($payment);
            }

            DB::commit();

            return back()->with('success', 'تم تسجيل التسوية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء التسوية: ' . $e->getMessage());
        }
    }

    protected function generatePaymentNumber(): string
    {
        $year = date('Y');
        $last = $this->tenantQuery(Payment::class)
            ->withTrashed()
            ->where('payment_number', 'like', 'PAY-' . $year . '-%')
            ->max('payment_number');

        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return 'PAY-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}