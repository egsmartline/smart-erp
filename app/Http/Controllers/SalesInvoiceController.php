<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\Currency;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends TenantAwareController
{
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
                $q->where('invoice_number', 'like', '%' . $request->search . '%');
            });
        }

        $invoices = $query->latest()->paginate(15);
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();

        return view('sales-invoices.index', compact('invoices', 'customers'));
    }

    public function create()
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        $invoiceNumber = $this->generateInvoiceNumber();

        return view('sales-invoices.create', compact('customers', 'warehouses', 'items', 'currencies', 'invoiceNumber'));
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

            $invoice = SalesInvoice::create([
                'tenant_id' => $this->getTenantId(),
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'cashier_id' => auth()->id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'date' => $validated['date'],
                'due_date' => $validated['due_date'],
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
        $salesInvoice->load(['customer', 'warehouse', 'lines.item', 'cashier', 'returns', 'currency']);
        return view('sales-invoices.show', compact('salesInvoice'));
    }

    public function edit(SalesInvoice $salesInvoice)
    {
        if (!in_array($salesInvoice->status, ['draft', 'posted'])) {
            return back()->with('error', 'لا يمكن تعديل هذه الفاتورة');
        }

        $salesInvoice->load('lines.item');
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();

        return view('sales-invoices.edit', compact('salesInvoice', 'customers', 'warehouses', 'items', 'currencies'));
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

            $salesInvoice->update([
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'currency_id' => $validated['currency_id'] ?? null,
                'date' => $validated['date'],
                'due_date' => $validated['due_date'],
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

            if ($salesInvoice->status === 'posted') {
                $salesInvoice->fresh()->load('lines.item');

                foreach ($salesInvoice->lines as $line) {
                    $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                        ->where('warehouse_id', $line->warehouse_id)
                        ->first();

                    $available = $itemWarehouse ? $itemWarehouse->quantity : 0;
                    if ($available < $line->quantity) {
                        DB::rollBack();
                        $itemName = $line->item->name ?? '#' . $line->item_id;
                        return back()->withInput()->with('error', "الرصيد غير كافٍ للصنف {$itemName} (المتوفر: {$available}، المطلوب: {$line->quantity})");
                    }

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
                $lines = $journalService->buildSalesInvoiceLines($salesInvoice->toArray(), $this->getTenantId());
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
        if ($salesInvoice->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف فاتورة غير مسودة');
        }

        DB::beginTransaction();

        try {
            $salesInvoice->lines()->delete();
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

        foreach ($salesInvoice->lines as $line) {
            $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                ->where('warehouse_id', $line->warehouse_id)
                ->first();

            $available = $itemWarehouse ? $itemWarehouse->quantity : 0;
            if ($available < $line->quantity) {
                $itemName = $line->item->name ?? '#' . $line->item_id;
                return back()->with('error', "الرصيد غير كافٍ للصنف {$itemName} (المتوفر: {$available}، المطلوب: {$line->quantity})");
            }
        }

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
            $lines = $journalService->buildSalesInvoiceLines($salesInvoice->toArray(), $this->getTenantId());
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
}
