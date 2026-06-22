<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\PaymentTerm;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(SalesOrder::class)
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
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(20);
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();

        return view('sales-orders.index', compact('orders', 'customers'));
    }

    public function create()
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = $this->tenantQuery(Item::class)->where('is_active', true)->get();
        $paymentTerms = $this->tenantQuery(PaymentTerm::class)->get();
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->get();

        return view('sales-orders.create', compact('customers', 'warehouses', 'items', 'paymentTerms', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'required_date' => 'nullable|date|after_or_equal:date',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
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
                    'delivered_qty' => 0,
                    'unit_price' => $line['unit_price'],
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_percent' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                ];
            }

            $grandTotal = $subtotal - $totalDiscount + $totalTax;

            $order = SalesOrder::create([
                'tenant_id' => $this->getTenantId(),
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => auth()->id(),
                'order_number' => $this->generateOrderNumber(),
                'date' => $validated['date'],
                'required_date' => $validated['required_date'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'discount_percent' => 0,
                'tax_amount' => $totalTax,
                'total' => $grandTotal,
                'currency_id' => $validated['currency_id'] ?? null,
                'exchange_rate' => 1,
                'status' => 'draft',
                'delivery_status' => 'pending',
                'delivered_qty' => 0,
                'invoice_status' => 'not_invoiced',
                'payment_term_id' => $validated['payment_term_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            foreach ($lineData as $data) {
                $data['sales_order_id'] = $order->id;
                SalesOrderLine::create($data);
            }

            DB::commit();

            return redirect()->route('sales-orders.show', $order)
                ->with('success', 'تم إنشاء أمر البيع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء أمر البيع: ' . $e->getMessage());
        }
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'warehouse', 'user', 'lines.item', 'invoices', 'currency']);
        return view('sales-orders.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل أمر بيع غير مسودة');
        }

        $salesOrder->load('lines.item');
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = $this->tenantQuery(Item::class)->where('is_active', true)->get();
        $paymentTerms = $this->tenantQuery(PaymentTerm::class)->get();
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->get();

        return view('sales-orders.edit', compact('salesOrder', 'customers', 'warehouses', 'items', 'paymentTerms', 'currencies'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل أمر بيع غير مسودة');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'required_date' => 'nullable|date|after_or_equal:date',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
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
                    'delivered_qty' => 0,
                    'unit_price' => $line['unit_price'],
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_percent' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                ];
            }

            $grandTotal = $subtotal - $totalDiscount + $totalTax;

            $salesOrder->update([
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'date' => $validated['date'],
                'required_date' => $validated['required_date'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'tax_amount' => $totalTax,
                'total' => $grandTotal,
                'currency_id' => $validated['currency_id'] ?? null,
                'payment_term_id' => $validated['payment_term_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $salesOrder->lines()->delete();

            foreach ($lineData as $data) {
                $data['sales_order_id'] = $salesOrder->id;
                SalesOrderLine::create($data);
            }

            DB::commit();

            return redirect()->route('sales-orders.show', $salesOrder)
                ->with('success', 'تم تحديث أمر البيع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث أمر البيع: ' . $e->getMessage());
        }
    }

    public function confirm(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن تأكيد أمر بيع غير مسودة');
        }

        $salesOrder->update(['status' => 'confirmed']);

        return back()->with('success', 'تم تأكيد أمر البيع بنجاح');
    }

    public function deliver(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'confirmed') {
            return back()->with('error', 'لا يمكن تسليم أمر بيع غير مؤكد');
        }

        DB::beginTransaction();

        try {
            foreach ($salesOrder->lines as $line) {
                $line->update(['delivered_qty' => $line->quantity]);
            }

            $salesOrder->update([
                'delivery_status' => 'delivered',
                'delivered_qty' => $salesOrder->lines->sum('quantity'),
                'status' => $salesOrder->invoice_status === 'fully_invoiced' ? 'invoiced' : 'delivered',
            ]);

            DB::commit();

            return back()->with('success', 'تم تسليم أمر البيع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء التسليم: ' . $e->getMessage());
        }
    }

    public function invoice(SalesOrder $salesOrder)
    {
        if (!in_array($salesOrder->status, ['confirmed', 'delivered'])) {
            return back()->with('error', 'لا يمكن إنشاء فاتورة لأمر بيع في هذه الحالة');
        }

        $salesOrder->load('lines');

        DB::beginTransaction();

        try {
            $invSubtotal = 0;
            $invDiscount = 0;
            $invTax = 0;
            $invTotal = 0;
            $invoiceLines = [];

            foreach ($salesOrder->lines as $orderLine) {
                $deliveredQty = ($orderLine->delivered_qty ?? 0) > 0 ? $orderLine->delivered_qty : $orderLine->quantity;
                $ratio = $orderLine->quantity > 0 ? $deliveredQty / $orderLine->quantity : 0;
                $lineSubtotal = $deliveredQty * $orderLine->unit_price;
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
                    'quantity' => $deliveredQty,
                    'unit_price' => $orderLine->unit_price,
                    'discount_percent' => $orderLine->discount_percent,
                    'discount_amount' => $lineDiscount,
                    'tax_rate' => $orderLine->tax_percent,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                    'warehouse_id' => $salesOrder->warehouse_id,
                ];
            }

            $invoice = SalesInvoice::create([
                'tenant_id' => $this->getTenantId(),
                'customer_id' => $salesOrder->customer_id,
                'warehouse_id' => $salesOrder->warehouse_id,
                'cashier_id' => auth()->id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'date' => now()->toDateString(),
                'subtotal' => $invSubtotal,
                'discount_amount' => $invDiscount,
                'discount_percent' => 0,
                'tax_amount' => $invTax,
                'total' => $invTotal,
                'paid_amount' => 0,
                'due_amount' => $invTotal,
                'currency_id' => $salesOrder->currency_id,
                'exchange_rate' => 1,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'notes' => 'إنشاء من أمر بيع رقم: ' . $salesOrder->order_number,
                'sales_order_id' => $salesOrder->id,
            ]);

            foreach ($invoiceLines as $lineData) {
                $lineData['sales_invoice_id'] = $invoice->id;
                SalesInvoiceLine::create($lineData);
            }

            $salesOrder->update([
                'invoice_status' => 'fully_invoiced',
                'status' => 'invoiced',
            ]);

            DB::commit();

            return redirect()->route('sales-invoices.show', $invoice)
                ->with('success', 'تم إنشاء فاتورة المبيعات من أمر البيع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    public function cancel(SalesOrder $salesOrder)
    {
        if ($salesOrder->status === 'cancelled') {
            return back()->with('error', 'أمر البيع已经被إلغاء بالفعل');
        }

        $salesOrder->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'تم إلغاء أمر البيع بنجاح');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف أمر بيع غير مسودة');
        }

        DB::beginTransaction();

        try {
            $salesOrder->lines()->delete();
            $salesOrder->delete();

            DB::commit();

            return redirect()->route('sales-orders.index')
                ->with('success', 'تم حذف أمر البيع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    protected function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = $this->tenantQuery(SalesOrder::class)
            ->withTrashed()
            ->whereYear('date', $year)
            ->max('order_number');

        if ($lastOrder) {
            $lastSequence = (int) substr($lastOrder, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return 'SO-' . $year . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    protected function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = $this->tenantQuery(SalesInvoice::class)
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
}
