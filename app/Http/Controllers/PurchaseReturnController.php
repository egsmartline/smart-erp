<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnLine;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(PurchaseReturn::class)
            ->with(['supplier', 'purchaseInvoice']);

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
            $query->where('return_number', 'like', '%' . $request->search . '%');
        }

        $returns = $query->latest()->paginate(15);
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();

        return view('purchase-returns.index', compact('returns', 'suppliers'));
    }

    public function create(Request $request)
    {
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $invoices = $this->tenantQuery(PurchaseInvoice::class)
            ->where('status', 'posted')
            ->with('supplier')
            ->latest()
            ->get();
        $returnNumber = $this->generateReturnNumber();

        $selectedInvoice = null;
        if ($request->filled('invoice_id')) {
            $selectedInvoice = PurchaseInvoice::where('id', $request->invoice_id)
                ->where('tenant_id', $this->getTenantId())
                ->with('lines.item')
                ->first();
        }

        return view('purchase-returns.create', compact('suppliers', 'warehouses', 'items', 'invoices', 'returnNumber', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_cost' => 'nullable|numeric|min:0',
            'lines.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.reason' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $totalTax = 0;

            $lineData = [];
            foreach ($validated['lines'] as $line) {
                $unitPrice = $line['unit_cost'] ?? 0;
                $lineSubtotal = $line['quantity'] * $unitPrice;
                $lineTax = $lineSubtotal * (($line['tax_percent'] ?? 0) / 100);

                $subtotal += $lineSubtotal;
                $totalTax += $lineTax;

                $lineData[] = [
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $unitPrice,
                    'tax_percent' => $line['tax_percent'] ?? 0,
                    'tax_amount' => $lineTax,
                    'total' => $lineSubtotal + $lineTax,
                    'reason' => $line['reason'] ?? null,
                ];
            }

            $grandTotal = $subtotal + $totalTax;

            $return = PurchaseReturn::create([
                'tenant_id' => $this->getTenantId(),
                'supplier_id' => $validated['supplier_id'],
                'original_invoice_id' => $validated['purchase_invoice_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'return_number' => $this->generateReturnNumber(),
                'date' => $validated['date'],
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'total' => $grandTotal,
                'reason' => $validated['reason'] ?? null,
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            foreach ($lineData as $data) {
                $data['purchase_return_id'] = $return->id;
                PurchaseReturnLine::create($data);
            }

            DB::commit();

            return redirect()->route('purchase-returns.show', $return)
                ->with('success', 'تم إنشاء مرتجع المشتريات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المرتجع: ' . $e->getMessage());
        }
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['supplier', 'purchaseInvoice', 'warehouse', 'lines.item', 'creator']);
        return view('purchase-returns.show', compact('purchaseReturn'));
    }

    public function post(PurchaseReturn $purchaseReturn)
    {
        if ($purchaseReturn->status !== 'draft') {
            return back()->with('error', 'تم ترحيل هذا المرتجع بالفعل');
        }

        DB::beginTransaction();

        try {
            $purchaseReturn->update(['status' => 'posted']);

            foreach ($purchaseReturn->lines as $line) {
                $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                    ->where('warehouse_id', $purchaseReturn->warehouse_id)
                    ->first();

                if ($itemWarehouse) {
                    $itemWarehouse->decrement('quantity', $line->quantity);
                    $itemWarehouse->decrement('available_quantity', $line->quantity);
                }

                StockMovement::create([
                    'item_id' => $line->item_id,
                    'warehouse_id' => $purchaseReturn->warehouse_id,
                    'stockable_type' => PurchaseReturn::class,
                    'stockable_id' => $purchaseReturn->id,
                    'type' => 'out',
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->unit_cost,
                    'total_cost' => $line->total,
                    'reference_number' => $purchaseReturn->return_number,
                    'date' => now()->toDateString(),
                    'notes' => 'خروج مخزون - مرتجع مشتريات',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'تم ترحيل المرتجع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الترحيل: ' . $e->getMessage());
        }
    }

    protected function generateReturnNumber(): string
    {
        $year = date('Y');
        $lastReturn = $this->tenantQuery(PurchaseReturn::class)
            ->withTrashed()
            ->whereYear('date', $year)
            ->max('return_number');

        if ($lastReturn) {
            $lastSequence = (int) substr($lastReturn, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return 'PR-' . $year . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}
