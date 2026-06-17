<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferLine;
use App\Models\Item;
use App\Models\ItemWarehouse;
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
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.notes' => 'nullable|string|max:500',
        ]);

        $lastTransfer = StockTransfer::where('tenant_id', $this->getTenantId())->latest('id')->first();
        $nextNumber = $lastTransfer ? (int) substr($lastTransfer->reference, -4) + 1 : 1;
        $reference = 'ST-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $transfer = StockTransfer::create([
            'tenant_id' => $this->getTenantId(),
            'reference' => $reference,
            'source_warehouse_id' => $validated['source_warehouse_id'],
            'destination_warehouse_id' => $validated['destination_warehouse_id'],
            'transfer_date' => $validated['transfer_date'],
            'state' => 'draft',
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['lines'] as $line) {
            StockTransferLine::create([
                'tenant_id' => $this->getTenantId(),
                'transfer_id' => $transfer->id,
                'item_id' => $line['item_id'],
                'quantity' => $line['quantity'],
                'notes' => $line['notes'] ?? null,
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

        foreach ($transfer->lines as $line) {
            $sourceIw = ItemWarehouse::where('tenant_id', $this->getTenantId())
                ->where('item_id', $line->item_id)
                ->where('warehouse_id', $transfer->source_warehouse_id)
                ->first();

            if ($sourceIw && $sourceIw->quantity >= $line->quantity) {
                $sourceIw->quantity -= $line->quantity;
                $sourceIw->save();
            }

            $destIw = ItemWarehouse::firstOrCreate([
                'tenant_id' => $this->getTenantId(),
                'item_id' => $line->item_id,
                'warehouse_id' => $transfer->destination_warehouse_id,
            ], ['quantity' => 0, 'reserved_quantity' => 0, 'average_cost' => 0]);

            $destIw->quantity += $line->quantity;
            $destIw->save();
        }

        $transfer->update(['state' => 'done']);

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
