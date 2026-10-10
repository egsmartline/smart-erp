<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferLine;
use App\Models\Item;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockTransferController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = StockTransfer::where('tenant_id', $this->getTenantId())
            ->with(['sourceWarehouse', 'destinationWarehouse', 'creator']);

        if ($request->filled('warehouse_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('source_warehouse_id', $request->warehouse_id)
                  ->orWhere('destination_warehouse_id', $request->warehouse_id);
            });
        }

        $transfers = $query->latest('transfer_date')->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('tenant_id', $this->getTenantId())->orderBy('name')->get();

        return view('stock-transfers.index', compact('transfers', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('tenant_id', $this->getTenantId())->orderBy('name')->get();
        $items = Item::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('stock-transfers.create', compact('warehouses', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.notes' => 'nullable|string|max:500',
        ]);

        $transfer = StockTransfer::create([
            'tenant_id' => $this->getTenantId(),
            'reference' => $this->nextSequentialNumber('stock_transfers', 'reference', null, 'ST', 'Y'),
            'source_warehouse_id' => $validated['source_warehouse_id'],
            'destination_warehouse_id' => $validated['destination_warehouse_id'],
            'date' => $validated['date'],
            'state' => 'draft',
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        foreach ($validated['lines'] as $line) {
            StockTransferLine::create([
                'tenant_id' => $this->getTenantId(),
                'stock_transfer_id' => $transfer->id,
                'item_id' => $line['item_id'],
                'quantity' => $line['quantity'],
            ]);
        }

        return redirect()->route('stock-transfers.show', $transfer)->with('success', 'تم إنشاء التحويل بنجاح');
    }

    public function show(StockTransfer $transfer)
    {
        if ($transfer->tenant_id !== $this->getTenantId()) abort(403);
        $transfer->load(['sourceWarehouse', 'destinationWarehouse', 'creator', 'lines.item']);

        return view('stock-transfers.show', compact('transfer'));
    }

    public function confirm(StockTransfer $transfer)
    {
        if ($transfer->tenant_id !== $this->getTenantId()) abort(403);
        $transfer->update(['state' => 'confirmed']);

        return redirect()->route('stock-transfers.show', $transfer)->with('success', 'تم تأكيد التحويل');
    }

    public function done(StockTransfer $transfer)
    {
        if ($transfer->tenant_id !== $this->getTenantId()) abort(403);

        $tenantId = $this->getTenantId();
        $lines = $transfer->lines()->with('item')->get();

        // Validate everything first: a transfer that only partly applies would
        // create stock at the destination out of thin air.
        $sources = [];
        foreach ($lines as $line) {
            $sourceIw = ItemWarehouse::where('tenant_id', $tenantId)
                ->where('item_id', $line->item_id)
                ->where('warehouse_id', $transfer->source_warehouse_id)
                ->first();

            if (!$sourceIw || (float) $sourceIw->quantity < (float) $line->quantity) {
                return redirect()->route('stock-transfers.show', $transfer)
                    ->with('error', 'الرصيد غير كافٍ في المستودع المصدر للصنف: ' . $line->item->name);
            }
            $sources[$line->id] = $sourceIw;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($lines as $line) {
                $sourceIw = $sources[$line->id];
                $unitCost = (float) $sourceIw->average_cost;

                $sourceIw->quantity = (float) $sourceIw->quantity - (float) $line->quantity;
                $sourceIw->save();

                StockMovement::create([
                    'tenant_id' => $tenantId,
                    'item_id' => $line->item_id,
                    'warehouse_id' => $transfer->source_warehouse_id,
                    'type' => 'transfer_out',
                    'quantity' => $line->quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $unitCost * (float) $line->quantity,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'description' => 'تحويل صادر - ' . ($transfer->reference ?? ''),
                    'user_id' => auth()->id(),
                ]);

                $destIw = ItemWarehouse::firstOrCreate([
                    'tenant_id' => $tenantId,
                    'item_id' => $line->item_id,
                    'warehouse_id' => $transfer->destination_warehouse_id,
                ], ['quantity' => 0, 'reserved_quantity' => 0, 'average_cost' => 0]);

                $newQty = (float) $destIw->quantity + (float) $line->quantity;
                if ($newQty > 0) {
                    $destIw->average_cost = ((float) $destIw->quantity * (float) $destIw->average_cost + (float) $line->quantity * $unitCost) / $newQty;
                }
                $destIw->quantity = $newQty;
                $destIw->save();

                StockMovement::create([
                    'tenant_id' => $tenantId,
                    'item_id' => $line->item_id,
                    'warehouse_id' => $transfer->destination_warehouse_id,
                    'type' => 'transfer_in',
                    'quantity' => $line->quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $unitCost * (float) $line->quantity,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'description' => 'تحويل وارد - ' . ($transfer->reference ?? ''),
                    'user_id' => auth()->id(),
                ]);
            }

            $transfer->update(['state' => 'done']);
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('stock-transfers.show', $transfer)
                ->with('error', 'تعذر إتمام التحويل: ' . $e->getMessage());
        }

        return redirect()->route('stock-transfers.show', $transfer)->with('success', 'تم إتمام التحويل بنجاح');
    }

    public function cancel(StockTransfer $transfer)
    {
        if ($transfer->tenant_id !== $this->getTenantId()) abort(403);
        $transfer->update(['state' => 'cancelled']);

        return redirect()->route('stock-transfers.show', $transfer)->with('success', 'تم إلغاء التحويل');
    }

    public function destroy(StockTransfer $transfer)
    {
        if ($transfer->tenant_id !== $this->getTenantId()) abort(403);
        if ($transfer->state !== 'draft') {
            return redirect()->back()->with('error', 'لا يمكن حذف تحويل غير مسودة');
        }
        $transfer->lines()->delete();
        $transfer->delete();

        return redirect()->route('stock-transfers.index')->with('success', 'تم حذف التحويل بنجاح');
    }
}
