<?php

namespace App\Http\Controllers;

use App\Models\SalesDeliveryNote;
use App\Models\SalesDeliveryNoteLine;
use App\Models\StockMovement;
use App\Models\ItemWarehouse;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesDeliveryNoteController extends TenantAwareController
{
    public function index()
    {
        $deliveryNotes = SalesDeliveryNote::where('tenant_id', $this->getTenantId())
            ->with('customer', 'user')
            ->orderByDesc('id')
            ->paginate(20);

        return view('sales-delivery-notes.index', compact('deliveryNotes'));
    }

    public function create()
    {
        $tenantId = $this->getTenantId();
        $customers = Customer::where('tenant_id', $tenantId)->orderBy('name')->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->orderBy('name')->get();
        $items = Item::where('tenant_id', $tenantId)->orderBy('name')->get();
        return view('sales-delivery-notes.create', compact('customers', 'warehouses', 'items'));
    }

    public function store(Request $request)
    {
        $lines = $request->input('lines');
        if (is_string($lines)) {
            $lines = json_decode($lines, true);
            $request->merge(['lines' => $lines]);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.total' => 'required|numeric|min:0',
        ]);

        $tenantId = $this->getTenantId();

        return DB::transaction(function () use ($validated, $tenantId) {
            $deliveryNote = SalesDeliveryNote::create([
                'tenant_id' => $tenantId,
                'delivery_number' => 'DN-' . now()->format('Ymd') . '-' . str_pad(SalesDeliveryNote::where('tenant_id', $tenantId)->count() + 1, 4, '0', STR_PAD_LEFT),
                'date' => $validated['date'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'sales_order_id' => null,
                'user_id' => Auth::id(),
                'status' => 'confirmed',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['lines'] as $line) {
                SalesDeliveryNoteLine::create([
                    'tenant_id' => $tenantId,
                    'sales_delivery_note_id' => $deliveryNote->id,
                    'sales_order_line_id' => null,
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'total' => $line['total'],
                ]);

                $itemWarehouse = ItemWarehouse::firstOrCreate(
                    ['item_id' => $line['item_id'], 'warehouse_id' => $validated['warehouse_id']],
                    ['tenant_id' => $tenantId, 'quantity' => 0, 'reserved_quantity' => 0, 'average_cost' => 0]
                );

                $itemWarehouse->decrement('quantity', $line['quantity']);

                StockMovement::create([
                    'tenant_id' => $tenantId,
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $validated['warehouse_id'],
                    'type' => 'sale',
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_price'],
                    'total_cost' => $line['total'],
                    'reference_type' => SalesDeliveryNote::class,
                    'reference_id' => $deliveryNote->id,
                    'description' => 'تسليم مبيعات - ' . $deliveryNote->delivery_number,
                    'user_id' => Auth::id(),
                ]);
            }

            return redirect()->route('sales-delivery-notes.show', $deliveryNote)
                ->with('success', 'تم إنشاء إذن التسليم بنجاح');
        });
    }

    public function show(SalesDeliveryNote $salesDeliveryNote)
    {
        if ($salesDeliveryNote->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $salesDeliveryNote->load('customer', 'warehouse', 'user', 'lines.item');

        return view('sales-delivery-notes.show', compact('salesDeliveryNote'));
    }

    public function edit(SalesDeliveryNote $salesDeliveryNote)
    {
        if ($salesDeliveryNote->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $salesDeliveryNote->load('lines.item');
        $tenantId = $this->getTenantId();
        $customers = Customer::where('tenant_id', $tenantId)->orderBy('name')->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->orderBy('name')->get();
        $items = Item::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('sales-delivery-notes.edit', compact('salesDeliveryNote', 'customers', 'warehouses', 'items'));
    }

    public function update(Request $request, SalesDeliveryNote $salesDeliveryNote)
    {
        if ($salesDeliveryNote->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $lines = $request->input('lines');
        if (is_string($lines)) {
            $lines = json_decode($lines, true);
            $request->merge(['lines' => $lines]);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.total' => 'required|numeric|min:0',
        ]);

        $tenantId = $this->getTenantId();

        DB::transaction(function () use ($salesDeliveryNote, $validated, $tenantId, $request) {
            foreach ($salesDeliveryNote->lines as $oldLine) {
                $itemWarehouse = ItemWarehouse::where('item_id', $oldLine->item_id)
                    ->where('warehouse_id', $salesDeliveryNote->warehouse_id)
                    ->first();
                if ($itemWarehouse) {
                    $itemWarehouse->increment('quantity', $oldLine->quantity);
                }

                StockMovement::where('reference_type', SalesDeliveryNote::class)
                    ->where('reference_id', $salesDeliveryNote->id)
                    ->where('item_id', $oldLine->item_id)
                    ->delete();
            }

            $salesDeliveryNote->lines()->delete();

            $salesDeliveryNote->update([
                'date' => $validated['date'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['lines'] as $line) {
                SalesDeliveryNoteLine::create([
                    'tenant_id' => $tenantId,
                    'sales_delivery_note_id' => $salesDeliveryNote->id,
                    'sales_order_line_id' => null,
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'total' => $line['total'],
                ]);

                $itemWarehouse = ItemWarehouse::firstOrCreate(
                    ['item_id' => $line['item_id'], 'warehouse_id' => $validated['warehouse_id']],
                    ['tenant_id' => $tenantId, 'quantity' => 0, 'reserved_quantity' => 0, 'average_cost' => 0]
                );

                $itemWarehouse->decrement('quantity', $line['quantity']);

                StockMovement::create([
                    'tenant_id' => $tenantId,
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $validated['warehouse_id'],
                    'type' => 'sale',
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_price'],
                    'total_cost' => $line['total'],
                    'reference_type' => SalesDeliveryNote::class,
                    'reference_id' => $salesDeliveryNote->id,
                    'description' => 'تسليم مبيعات - ' . $salesDeliveryNote->delivery_number,
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('sales-delivery-notes.show', $salesDeliveryNote)
            ->with('success', 'تم تحديث إذن التسليم بنجاح');
    }

    public function destroy(SalesDeliveryNote $salesDeliveryNote)
    {
        if ($salesDeliveryNote->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        if ($salesDeliveryNote->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف إذن تسليم غير مسودة');
        }

        $salesDeliveryNote->delete();

        return redirect()->route('sales-delivery-notes.index')
            ->with('success', 'تم حذف إذن التسليم بنجاح');
    }
}
