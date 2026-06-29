<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceLine;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use App\Models\PaymentTerm;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(PurchaseOrder::class)
            ->with('supplier');

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
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(20);
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();

        return view('purchase-orders.index', compact('orders', 'suppliers'));
    }

    public function create()
    {
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = $this->tenantQuery(Item::class)->where('is_active', true)->get();
        $paymentTerms = $this->tenantQuery(PaymentTerm::class)->get();
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->get();

        return view('purchase-orders.create', compact('suppliers', 'warehouses', 'items', 'paymentTerms', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:date',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_cost' => 'required|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            $lineData = [];
            foreach ($validated['lines'] as $line) {
                $lineSubtotal = $line['quantity'] * $line['unit_cost'];
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
                    'received_quantity' => 0,
                    'unit_price' => $line['unit_cost'],
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_percent' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                ];
            }

            $grandTotal = $subtotal - $totalDiscount + $totalTax;

            $order = PurchaseOrder::create([
                'tenant_id' => $this->getTenantId(),
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => auth()->id(),
                'order_number' => $this->generateOrderNumber(),
                'date' => $validated['date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'discount_percent' => 0,
                'tax_amount' => $totalTax,
                'total' => $grandTotal,
                'currency_id' => $validated['currency_id'] ?? null,
                'exchange_rate' => 1,
                'status' => 'draft',
                'receipt_status' => 'pending',
                'received_qty' => 0,
                'invoice_status' => 'not_invoiced',
                'payment_term_id' => $validated['payment_term_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            foreach ($lineData as $data) {
                $data['purchase_order_id'] = $order->id;
                PurchaseOrderLine::create($data);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $order)
                ->with('success', 'تم إنشاء أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء أمر الشراء: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'warehouse', 'user', 'lines.item', 'currency']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل أمر شراء غير مسودة');
        }

        $purchaseOrder->load('lines.item');
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = $this->tenantQuery(Item::class)->where('is_active', true)->get();
        $paymentTerms = $this->tenantQuery(PaymentTerm::class)->get();
        $currencies = collect(['SAR', 'USD', 'EUR']);

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'warehouses', 'items', 'paymentTerms', 'currencies'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل أمر شراء غير مسودة');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:date',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_cost' => 'required|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            $lineData = [];
            foreach ($validated['lines'] as $line) {
                $lineSubtotal = $line['quantity'] * $line['unit_cost'];
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
                    'received_quantity' => 0,
                    'unit_price' => $line['unit_cost'],
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_percent' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                ];
            }

            $grandTotal = $subtotal - $totalDiscount + $totalTax;

            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'date' => $validated['date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'tax_amount' => $totalTax,
                'total' => $grandTotal,
                'currency_code' => $validated['currency_code'] ?? 'SAR',
                'payment_term_id' => $validated['payment_term_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $purchaseOrder->lines()->delete();

            foreach ($lineData as $data) {
                $data['purchase_order_id'] = $purchaseOrder->id;
                PurchaseOrderLine::create($data);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'تم تحديث أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث أمر الشراء: ' . $e->getMessage());
        }
    }

    public function confirm(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن تأكيد أمر شراء غير مسودة');
        }

        $purchaseOrder->update(['status' => 'confirmed']);

        return back()->with('success', 'تم تأكيد أمر الشراء بنجاح');
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'confirmed') {
            return back()->with('error', 'لا يمكن استلام أمر شراء غير مؤكد');
        }

        DB::beginTransaction();

        try {
            foreach ($purchaseOrder->lines as $line) {
                $line->update(['received_qty' => $line->quantity]);

                $itemWarehouse = ItemWarehouse::firstOrCreate(
                    ['item_id' => $line->item_id, 'warehouse_id' => $purchaseOrder->warehouse_id],
                    ['quantity' => 0, 'reserved_quantity' => 0, 'available_quantity' => 0, 'average_cost' => 0]
                );

                $itemWarehouse->increment('quantity', $line->quantity);
                $itemWarehouse->increment('available_quantity', $line->quantity);

                StockMovement::create([
                    'item_id' => $line->item_id,
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                    'stockable_type' => PurchaseOrder::class,
                    'stockable_id' => $purchaseOrder->id,
                    'type' => 'in',
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->unit_cost,
                    'total_cost' => $line->total,
                    'reference_number' => $purchaseOrder->order_number,
                    'date' => now()->toDateString(),
                    'notes' => 'إدخال مخزون - استلام أمر شراء',
                    'created_by' => auth()->id(),
                ]);
            }

            $purchaseOrder->update([
                'receipt_status' => 'received',
                'received_qty' => $purchaseOrder->lines->sum('quantity'),
                'status' => $purchaseOrder->invoice_status === 'fully_invoiced' ? 'invoiced' : 'received',
            ]);

            DB::commit();

            return back()->with('success', 'تم استلام أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الاستلام: ' . $e->getMessage());
        }
    }

    public function invoice(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['confirmed', 'received'])) {
            return back()->with('error', 'لا يمكن إنشاء فاتورة لأمر شراء في هذه الحالة');
        }

        $purchaseOrder->load('lines');

        DB::beginTransaction();

        try {
            $invSubtotal = 0;
            $invDiscount = 0;
            $invTax = 0;
            $invTotal = 0;
            $invoiceLines = [];

            foreach ($purchaseOrder->lines as $orderLine) {
                $receivedQty = ($orderLine->received_qty ?? 0) > 0 ? $orderLine->received_qty : $orderLine->quantity;
                $ratio = $orderLine->quantity > 0 ? $receivedQty / $orderLine->quantity : 0;
                $lineSubtotal = $receivedQty * $orderLine->unit_price;
                $lineDiscount = $orderLine->discount_percent
                    ? $lineSubtotal * $orderLine->discount_percent / 100
                    : ($orderLine->discount_amount * $ratio);
                $lineTax = $orderLine->tax_percent
                    ? ($lineSubtotal - $lineDiscount) * $orderLine->tax_percent / 100
                    : 0;
                $lineTotal = $lineSubtotal - $lineDiscount + $lineTax;

                $invSubtotal += $lineSubtotal;
                $invDiscount += $lineDiscount;
                $invTax += $lineTax;
                $invTotal += $lineTotal;

                $invoiceLines[] = [
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $orderLine->item_id,
                    'description' => $orderLine->description,
                    'quantity' => $receivedQty,
                    'unit_cost' => $orderLine->unit_price,
                    'discount_percent' => $orderLine->discount_percent,
                    'discount_amount' => $lineDiscount,
                    'tax_rate' => $orderLine->tax_percent,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                ];
            }

            $invoice = PurchaseInvoice::create([
                'tenant_id' => $this->getTenantId(),
                'supplier_id' => $purchaseOrder->supplier_id,
                'warehouse_id' => $purchaseOrder->warehouse_id,
                'received_by' => auth()->id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'date' => now()->toDateString(),
                'subtotal' => $invSubtotal,
                'discount_amount' => $invDiscount,
                'discount_percent' => 0,
                'tax_amount' => $invTax,
                'total' => $invTotal,
                'paid_amount' => 0,
                'due_amount' => $invTotal,
                'currency_id' => $purchaseOrder->currency_id,
                'exchange_rate' => 1,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'notes' => 'إنشاء من أمر شراء رقم: ' . $purchaseOrder->order_number,
                'purchase_order_id' => $purchaseOrder->id,
            ]);

            foreach ($invoiceLines as $lineData) {
                $lineData['purchase_invoice_id'] = $invoice->id;
                PurchaseInvoiceLine::create($lineData);
            }

            $purchaseOrder->update([
                'invoice_status' => 'fully_invoiced',
                'status' => 'invoiced',
            ]);

            DB::commit();

            return redirect()->route('purchase-invoices.show', $invoice)
                ->with('success', 'تم إنشاء فاتورة المشتريات من أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'cancelled') {
            return back()->with('error', 'أمر الشراء已经被إلغاء بالفعل');
        }

        $purchaseOrder->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'تم إلغاء أمر الشراء بنجاح');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف أمر شراء غير مسودة');
        }

        DB::beginTransaction();

        try {
            $purchaseOrder->lines()->delete();
            $purchaseOrder->delete();

            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'تم حذف أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    protected function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = $this->tenantQuery(PurchaseOrder::class)
            ->withTrashed()
            ->whereYear('date', $year)
            ->max('order_number');

        if ($lastOrder) {
            $lastSequence = (int) substr($lastOrder, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return 'PO-' . $year . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
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
