<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceLine;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use App\Models\JournalEntry;
use App\Models\Currency;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(PurchaseInvoice::class)
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
        $items = Item::where('is_active', true)->get();
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->get();
        $invoiceNumber = $this->generateInvoiceNumber();

        return view('purchase-invoices.create', compact('suppliers', 'warehouses', 'items', 'currencies', 'invoiceNumber'));
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

            $overallDiscount = $validated['discount_amount'] ?? 0;
            $grandTotal = $subtotal - $totalDiscount - $overallDiscount + $totalTax + ($validated['shipping_amount'] ?? 0);

            $invoice = PurchaseInvoice::create([
                'tenant_id' => $this->getTenantId(),
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'currency_id' => $validated['currency_id'] ?? null,
                'received_by' => auth()->id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'date' => $validated['date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount + $overallDiscount,
                'discount_percent' => 0,
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
        $purchaseInvoice->load(['supplier', 'warehouse', 'lines.item', 'user', 'returns', 'currency']);
        return view('purchase-invoices.show', compact('purchaseInvoice'));
    }

    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        if (!in_array($purchaseInvoice->status, ['draft', 'posted'])) {
            return back()->with('error', 'لا يمكن تعديل هذه الفاتورة');
        }

        $purchaseInvoice->load('lines.item');
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->get();

        return view('purchase-invoices.edit', compact('purchaseInvoice', 'suppliers', 'warehouses', 'items', 'currencies'));
    }

    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل فاتورة غير مسودة');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
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
            'lines.*.unit_cost' => 'nullable|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'lines.*.expiry_date' => 'nullable|date',
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

            $overallDiscount = $validated['discount_amount'] ?? 0;
            $grandTotal = $subtotal - $totalDiscount - $overallDiscount + $totalTax + ($validated['shipping_amount'] ?? 0);

            $purchaseInvoice->update([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'currency_id' => $validated['currency_id'] ?? null,
                'date' => $validated['date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount + $overallDiscount,
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
