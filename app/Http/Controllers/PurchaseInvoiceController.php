<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceLine;
use App\Models\PurchaseInvoiceInstallment;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use App\Models\JournalEntry;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\CashTreasury;
use App\Models\BankAccount;
use App\Models\TreasuryTransaction;
use App\Models\BankTransaction;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(PurchaseInvoice::class)
            ->with(['supplier', 'currency']);

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $invoices = $query->latest()->paginate(15);
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();

        return view('purchase-invoices.index', compact('invoices', 'suppliers'));
    }

    public function create()
    {
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = Item::where('is_active', true)->orderBy('sku')->get();
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->get();
        $invoiceNumber = $this->generateInvoiceNumber();
        $defaultTaxRate = $this->getDefaultTaxRate();

        return view('purchase-invoices.create', compact('suppliers', 'warehouses', 'items', 'currencies', 'invoiceNumber', 'defaultTaxRate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
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
            'lines.*.unit_cost' => 'nullable|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'lines.*.expiry_date' => 'nullable|date',
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
                $unitCost = $line['unit_cost'] ?? 0;
                $lineSubtotal = $line['quantity'] * $unitCost;
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
                    'unit_cost' => $unitCost,
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_rate' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                    'warehouse_id' => $validated['warehouse_id'],
                    'expiry_date' => $line['expiry_date'] ?? null,
                ];
            }

            $discType = $validated['discount_type'] ?? 'amount';
            $overallDiscount = $discType === 'percent'
                ? $subtotal * (($validated['discount_percent_inv'] ?? 0) / 100)
                : ($validated['discount_amount'] ?? 0);
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

            $invoice = PurchaseInvoice::create([
                'tenant_id' => $this->getTenantId(),
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'currency_id' => $validated['currency_id'] ?? null,
                'received_by' => auth()->id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'date' => $validated['date'],
                'due_date' => $due_date,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount + $overallDiscount,
                'discount_percent' => $discType === 'percent' ? ($validated['discount_percent_inv'] ?? 0) : 0,
                'tax_amount' => $totalTax,
                'shipping_cost' => $validated['shipping_amount'] ?? 0,
                'total' => $grandTotal,
                'paid_amount' => 0,
                'due_amount' => $grandTotal,
                'exchange_rate' => 1,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($lineData as $data) {
                $data['purchase_invoice_id'] = $invoice->id;
                PurchaseInvoiceLine::create($data);
            }

            foreach ($installments as $inst) {
                $inst['purchase_invoice_id'] = $invoice->id;
                PurchaseInvoiceInstallment::create($inst);
            }

            DB::commit();

            return redirect()->route('purchase-invoices.show', $invoice)
                ->with('success', 'تم إنشاء فاتورة المشتريات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['supplier', 'warehouse', 'lines.item', 'user', 'returns', 'currency', 'installments']);
        $treasuries = $this->tenantQuery(CashTreasury::class)->where('is_active', true)->orderBy('name')->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->where('is_active', true)->orderBy('account_name')->get();
        return view('purchase-invoices.show', compact('purchaseInvoice', 'treasuries', 'bankAccounts'));
    }

    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        if (!in_array($purchaseInvoice->status, ['draft', 'posted'])) {
            return back()->with('error', 'لا يمكن تعديل هذه الفاتورة');
        }

        $purchaseInvoice->load(['lines.item', 'installments']);
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = Item::where('is_active', true)->orderBy('sku')->get();
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->get();

        $defaultTaxRate = $this->getDefaultTaxRate();

        return view('purchase-invoices.edit', compact('purchaseInvoice', 'suppliers', 'warehouses', 'items', 'currencies', 'defaultTaxRate'));
    }

    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        if (!in_array($purchaseInvoice->status, ['draft', 'posted'])) {
            return back()->with('error', 'لا يمكن تعديل هذه الفاتورة');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
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
            'lines.*.unit_cost' => 'nullable|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'lines.*.expiry_date' => 'nullable|date',
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
                $unitCost = $line['unit_cost'] ?? 0;
                $lineSubtotal = $line['quantity'] * $unitCost;
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
                    'unit_cost' => $unitCost,
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_rate' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                    'warehouse_id' => $validated['warehouse_id'],
                    'expiry_date' => $line['expiry_date'] ?? null,
                ];
            }

            $discType = $validated['discount_type'] ?? 'amount';
            $overallDiscount = $discType === 'percent'
                ? $subtotal * (($validated['discount_percent_inv'] ?? 0) / 100)
                : ($validated['discount_amount'] ?? 0);
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

            $purchaseInvoice->update([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'currency_id' => $validated['currency_id'] ?? null,
                'date' => $validated['date'],
                'due_date' => $due_date,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount + $overallDiscount,
                'discount_percent' => $discType === 'percent' ? ($validated['discount_percent_inv'] ?? 0) : 0,
                'tax_amount' => $totalTax,
                'shipping_cost' => $validated['shipping_amount'] ?? 0,
                'total' => $grandTotal,
                'due_amount' => $grandTotal - $purchaseInvoice->paid_amount,
                'notes' => $validated['notes'] ?? null,
            ]);

            $purchaseInvoice->lines()->delete();

            foreach ($lineData as $data) {
                $data['purchase_invoice_id'] = $purchaseInvoice->id;
                PurchaseInvoiceLine::create($data);
            }

            $purchaseInvoice->installments()->delete();

            foreach ($installments as $inst) {
                $inst['purchase_invoice_id'] = $purchaseInvoice->id;
                PurchaseInvoiceInstallment::create($inst);
            }

            DB::commit();

            return redirect()->route('purchase-invoices.show', $purchaseInvoice)
                ->with('success', 'تم تحديث فاتورة المشتريات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الفاتورة: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف فاتورة غير مسودة');
        }

        DB::beginTransaction();

        try {
            $purchaseInvoice->lines()->delete();
            $purchaseInvoice->installments()->delete();
            $purchaseInvoice->delete();

            DB::commit();

            return redirect()->route('purchase-invoices.index')
                ->with('success', 'تم حذف فاتورة المشتريات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    public function post(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== 'draft') {
            return back()->with('error', 'الفاتورة مرحلة بالفعل');
        }

        DB::beginTransaction();

        try {
            $purchaseInvoice->update([
                'status' => 'posted',
                'payment_status' => $purchaseInvoice->paid_amount > 0 ? 'partial' : 'unpaid',
            ]);

            foreach ($purchaseInvoice->lines as $line) {
                $itemWarehouse = ItemWarehouse::firstOrCreate(
                    ['item_id' => $line->item_id, 'warehouse_id' => $purchaseInvoice->warehouse_id],
                    ['tenant_id' => $this->getTenantId(), 'quantity' => 0, 'reserved_quantity' => 0, 'average_cost' => 0]
                );

                $itemWarehouse->increment('quantity', $line->quantity);

                if ($itemWarehouse->quantity > 0) {
                    $totalCost = ($itemWarehouse->quantity - $line->quantity) * $itemWarehouse->average_cost + $line->quantity * $line->unit_cost;
                    $itemWarehouse->average_cost = $totalCost / $itemWarehouse->quantity;
                }

                StockMovement::create([
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $line->item_id,
                    'warehouse_id' => $purchaseInvoice->warehouse_id,
                    'type' => 'purchase',
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->unit_cost,
                    'total_cost' => $line->total,
                    'reference_type' => PurchaseInvoice::class,
                    'reference_id' => $purchaseInvoice->id,
                    'description' => 'إدخال مخزون - فاتورة مشتريات ' . $purchaseInvoice->invoice_number,
                    'user_id' => auth()->id(),
                ]);
            }

            $journalService = app(JournalService::class);
            $invoiceData = $purchaseInvoice->toArray();
            $invoiceData['shipping_cost'] = $purchaseInvoice->shipping_cost ?? 0;
            $lines = $journalService->buildPurchaseInvoiceLines($invoiceData, $this->getTenantId());
            if (count($lines) >= 2) {
                $journalService->createEntry([
                    'tenant_id' => $this->getTenantId(),
                    'date' => $purchaseInvoice->date->format('Y-m-d'),
                    'description' => 'فاتورة مشتريات - ' . $purchaseInvoice->invoice_number,
                    'reference' => $purchaseInvoice->invoice_number,
                    'type' => 'purchase',
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

    public function void(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== 'posted') {
            return back()->with('error', 'لا يمكن إلغاء فاتورة غير مرحلة');
        }

        DB::beginTransaction();

        try {
            foreach ($purchaseInvoice->lines as $line) {
                $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                    ->where('warehouse_id', $purchaseInvoice->warehouse_id)
                    ->first();

                if ($itemWarehouse) {
                    $itemWarehouse->decrement('quantity', $line->quantity);
                }

                StockMovement::create([
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $line->item_id,
                    'warehouse_id' => $purchaseInvoice->warehouse_id,
                    'type' => 'return_out',
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->unit_cost,
                    'total_cost' => $line->total,
                    'reference_type' => PurchaseInvoice::class,
                    'reference_id' => $purchaseInvoice->id,
                    'description' => 'إلغاء فاتورة مشتريات - ' . $purchaseInvoice->invoice_number,
                    'user_id' => auth()->id(),
                ]);
            }

            $purchaseInvoice->update(['status' => 'voided']);

            app(JournalService::class)->reverseEntryByReference($purchaseInvoice->invoice_number, 'purchase');

            DB::commit();

            return back()->with('success', 'تم إلغاء الفاتورة وخصم المخزون بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الإلغاء: ' . $e->getMessage());
        }
    }

    public function searchSuppliers(Request $request)
    {
        $term = $request->input('search', '');
        $suppliers = $this->tenantQuery(Supplier::class)
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('name_ar', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('mobile', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'name_ar', 'phone', 'mobile', 'balance']);

        return response()->json($suppliers);
    }

    private function getDefaultTaxRate(): float
    {
        $tax = \App\Models\Tax::where('is_default', true)->orWhere('is_active', true)->first();
        return $tax ? (float) $tax->rate : 15;
    }

    public function settleInstallment(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        $validated = $request->validate([
            'installment_id' => 'nullable|exists:purchase_invoice_installments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'treasury_id' => 'nullable|exists:cash_treasuries,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
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
            $installment = PurchaseInvoiceInstallment::find($validated['installment_id']);
            if (!$installment || $installment->purchase_invoice_id !== $purchaseInvoice->id) {
                return back()->with('error', 'القسط غير صالح لهذه الفاتورة');
            }
        }

        $currency = $purchaseInvoice->currency ?? $this->tenantQuery(Currency::class)->where('code', 'default')->first();
        $currencyId = $currency?->id;

        DB::beginTransaction();

        try {
            $paymentNumber = $this->generatePaymentNumber();

            $payment = Payment::create([
                'tenant_id' => $tenantId,
                'payment_number' => $paymentNumber,
                'date' => $validated['date'],
                'type' => 'payment',
                'supplier_id' => $purchaseInvoice->supplier_id,
                'purchase_invoice_id' => $purchaseInvoice->id,
                'account_id' => $validated['account_id'] ?? null,
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
                $treasury->decrement('current_balance', $validated['amount']);
                TreasuryTransaction::create([
                    'tenant_id' => $tenantId,
                    'treasury_id' => $validated['treasury_id'],
                    'type' => 'out',
                    'amount' => $validated['amount'],
                    'reference_type' => 'payment',
                    'reference_id' => $payment->id,
                    'reference_number' => $paymentNumber,
                    'description' => $validated['notes'] ?? 'تسوية قسط - ' . $purchaseInvoice->invoice_number,
                    'user_id' => $userId,
                ]);
            } elseif ($validated['payment_method'] === 'bank_transfer' && !empty($validated['bank_account_id'])) {
                $bank = BankAccount::findOrFail($validated['bank_account_id']);
                $bank->decrement('current_balance', $validated['amount']);
                BankTransaction::create([
                    'tenant_id' => $tenantId,
                    'bank_account_id' => $validated['bank_account_id'],
                    'type' => 'out',
                    'amount' => $validated['amount'],
                    'reference_type' => 'payment',
                    'reference_id' => $payment->id,
                    'reference_number' => $paymentNumber,
                    'description' => $validated['notes'] ?? 'تسوية قسط - ' . $purchaseInvoice->invoice_number,
                    'user_id' => $userId,
                ]);
            }

            if (!empty($validated['account_id'])) {
                $journalService = app(JournalService::class);
                $lines = $journalService->buildPaymentLines([
                    'type' => 'payment',
                    'payment_method' => $validated['payment_method'],
                    'treasury_id' => $validated['treasury_id'] ?? null,
                    'bank_account_id' => $validated['bank_account_id'] ?? null,
                    'account_id' => $validated['account_id'],
                    'amount' => $validated['amount'],
                ]);
                if (count($lines) >= 2) {
                    $journalService->createEntry([
                        'tenant_id' => $tenantId,
                        'date' => $validated['date'],
                        'description' => $validated['notes'] ?? 'تسوية قسط - ' . $purchaseInvoice->invoice_number,
                        'reference' => $paymentNumber,
                        'type' => 'payment',
                        'lines' => $lines,
                    ]);
                }
            }

            if ($installment) {
                $instDue = (float) $installment->amount - (float) $installment->paid_amount;
                $apply = min($validated['amount'], $instDue);
                $installment->increment('paid_amount', $apply);
                app(PaymentController::class)->syncPurchaseInvoicePaidAmount($purchaseInvoice->id);
            } else {
                app(PaymentController::class)->allocatePaymentToPurchaseInstallments($payment);
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

    protected function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = $this->tenantQuery(PurchaseInvoice::class)
            ->withTrashed()
            ->whereYear('date', $year)
            ->max('invoice_number');

        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return 'INV-P-' . $year . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}
