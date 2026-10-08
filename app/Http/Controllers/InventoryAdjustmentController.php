<?php

namespace App\Http\Controllers;

use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentLine;
use App\Models\Item;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = InventoryAdjustment::where('tenant_id', $this->getTenantId())
            ->with(['warehouse', 'creator']);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        $adjustments = $query->latest('date')->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('tenant_id', $this->getTenantId())->orderBy('name')->get();

        return view('inventory-adjustments.index', compact('adjustments', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('tenant_id', $this->getTenantId())->orderBy('name')->get();
        $items = Item::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('inventory-adjustments.create', compact('warehouses', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.theoretical_qty' => 'required|numeric|min:0',
            'lines.*.actual_qty' => 'required|numeric|min:0',
            'lines.*.reason' => 'nullable|string|max:255',
        ]);

        $lastAdj = InventoryAdjustment::where('tenant_id', $this->getTenantId())->latest('id')->first();
        $nextNumber = $lastAdj ? (int) substr($lastAdj->reference, -4) + 1 : 1;
        $reference = 'IA-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $adj = InventoryAdjustment::create([
            'tenant_id' => $this->getTenantId(),
            'warehouse_id' => $validated['warehouse_id'],
            'reference' => $reference,
            'date' => $validated['date'],
            'state' => 'draft',
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        foreach ($validated['lines'] as $line) {
            InventoryAdjustmentLine::create([
                'tenant_id' => $this->getTenantId(),
                'inventory_adjustment_id' => $adj->id,
                'item_id' => $line['item_id'],
                'theoretical_qty' => $line['theoretical_qty'],
                'actual_qty' => $line['actual_qty'],
                'difference' => $line['actual_qty'] - $line['theoretical_qty'],
                'unit_cost' => Item::where('tenant_id', $this->getTenantId())
                    ->where('id', $line['item_id'])
                    ->value('cost_price') ?? 0,
                'reason' => $line['reason'] ?? null,
            ]);
        }

        return redirect()->route('inventory-adjustments.show', $adj)->with('success', 'تم إنشاء تسوية المخزون بنجاح');
    }

    public function show(InventoryAdjustment $adj)
    {
        if ($adj->tenant_id !== $this->getTenantId()) abort(403);
        $adj->load(['warehouse', 'creator', 'lines.item']);

        return view('inventory-adjustments.show', compact('adj'));
    }

    public function confirm(InventoryAdjustment $adj)
    {
        if ($adj->tenant_id !== $this->getTenantId()) abort(403);
        if ($adj->state !== 'draft') {
            return redirect()->back()->with('error', 'لا يمكن تأكيد تسوية مؤكدة أو ملغاة');
        }

        DB::transaction(function () use ($adj) {
            foreach ($adj->lines as $line) {
                $difference = (float) $line->difference;
                if ($difference == 0.0) {
                    continue;
                }

                $iw = ItemWarehouse::where('tenant_id', $this->getTenantId())
                    ->where('item_id', $line->item_id)
                    ->where('warehouse_id', $adj->warehouse_id)
                    ->first();

                if (!$iw) {
                    continue;
                }

                $iw->quantity = $line->actual_qty;
                $iw->save();

                $unitCost = (float) ($line->unit_cost ?? 0);

                StockMovement::create([
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $line->item_id,
                    'warehouse_id' => $adj->warehouse_id,
                    'type' => $difference > 0 ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => abs($difference),
                    'unit_cost' => $unitCost,
                    'total_cost' => abs($difference) * $unitCost,
                    'reference_type' => InventoryAdjustment::class,
                    'reference_id' => $adj->id,
                    'description' => 'تسوية مخزنية - ' . $adj->reference,
                    'user_id' => auth()->id(),
                ]);
            }

            $adj->update(['state' => 'done']);
        });

        return redirect()->route('inventory-adjustments.show', $adj)->with('success', 'تم تأكيد التسوية بنجاح');
    }

    public function cancel(InventoryAdjustment $adj)
    {
        if ($adj->tenant_id !== $this->getTenantId()) abort(403);
        if ($adj->state === 'done') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء تسوية مؤكدة');
        }
        $adj->update(['state' => 'cancelled']);

        return redirect()->route('inventory-adjustments.show', $adj)->with('success', 'تم إلغاء التسوية');
    }

    public function destroy(InventoryAdjustment $adj)
    {
        if ($adj->tenant_id !== $this->getTenantId()) abort(403);
        if ($adj->state !== 'draft') {
            return redirect()->back()->with('error', 'لا يمكن حذف تسوية غير مسودة');
        }
        $adj->lines()->delete();
        $adj->delete();

        return redirect()->route('inventory-adjustments.index')->with('success', 'تم حذف التسوية بنجاح');
    }
}
