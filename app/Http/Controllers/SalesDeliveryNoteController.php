<?php

namespace App\Http\Controllers;

use App\Models\SalesDeliveryNote;
use App\Models\SalesDeliveryNoteLine;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\ItemWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesDeliveryNoteController extends TenantAwareController
{
    public function index()
    {
        $deliveryNotes = SalesDeliveryNote::where('tenant_id', $this->getTenantId())
            ->with('salesOrder', 'customer', 'user')
            ->orderByDesc('id')
            ->paginate(20);

        return view('sales-delivery-notes.index', compact('deliveryNotes'));
    }

    public function create()
    {
        $salesOrders = SalesOrder::where('tenant_id', $this->getTenantId())
            ->whereIn('status', ['confirmed', 'delivered'])
            ->with('customer')
            ->orderByDesc('id')
            ->get();

        return view('sales-delivery-notes.create', compact('salesOrders'));
    }

    public function store(Request $request)
    {
        $lines = $request->input('lines');
        if (is_string($lines)) {
            $lines = json_decode($lines, true);
            $request->merge(['lines' => $lines]);
        }

        $validated = $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.sales_order_line_id' => 'required|exists:sales_order_lines,id',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.total' => 'required|numeric|min:0',
        ]);

        $tenantId = $this->getTenantId();

        $salesOrder = SalesOrder::where('tenant_id', $tenantId)->with('customer', 'warehouse')->findOrFail($validated['sales_order_id']);

        return DB::transaction(function () use ($validated, $salesOrder, $tenantId) {
            $deliveryNote = SalesDeliveryNote::create([
                'tenant_id' => $tenantId,
                'delivery_number' => 'DN-' . now()->format('Ymd') . '-' . str_pad(SalesDeliveryNote::where('tenant_id', $tenantId)->count() + 1, 4, '0', STR_PAD_LEFT),
                'date' => $validated['date'],
                'sales_order_id' => $salesOrder->id,
                'customer_id' => $salesOrder->customer_id,
                'warehouse_id' => $salesOrder->warehouse_id,
                'user_id' => Auth::id(),
                'status' => 'confirmed',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['lines'] as $line) {
                SalesDeliveryNoteLine::create([
                    'tenant_id' => $tenantId,
                    'sales_delivery_note_id' => $deliveryNote->id,
                    'sales_order_line_id' => $line['sales_order_line_id'],
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'total' => $line['total'],
                ]);

                $orderLine = $salesOrder->lines()->findOrFail($line['sales_order_line_id']);
                $orderLine->increment('delivered_qty', $line['quantity']);

                $itemWarehouse = ItemWarehouse::firstOrCreate(
                    ['item_id' => $line['item_id'], 'warehouse_id' => $salesOrder->warehouse_id],
                    ['quantity' => 0, 'reserved_quantity' => 0, 'available_quantity' => 0, 'average_cost' => 0]
                );

                $itemWarehouse->decrement('quantity', $line['quantity']);
                $itemWarehouse->decrement('available_quantity', $line['quantity']);

                StockMovement::create([
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $salesOrder->warehouse_id,
                    'stockable_type' => SalesDeliveryNote::class,
                    'stockable_id' => $deliveryNote->id,
                    'type' => 'out',
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_price'],
                    'total_cost' => $line['total'],
                    'reference_number' => $deliveryNote->delivery_number,
                    'date' => $validated['date'],
                    'notes' => 'تسليم مبيعات - ' . $deliveryNote->delivery_number,
                    'created_by' => Auth::id(),
                ]);
            }

            $salesOrder->update([
                'delivery_status' => 'delivered',
                'status' => $salesOrder->invoice_status === 'fully_invoiced' ? 'invoiced' : 'delivered',
            ]);

            return redirect()->route('sales-delivery-notes.show', $deliveryNote)
                ->with('success', 'تم إنشاء إذن التسليم بنجاح');
        });
    }

    public function show(SalesDeliveryNote $salesDeliveryNote)
    {
        if ($salesDeliveryNote->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $salesDeliveryNote->load('salesOrder', 'customer', 'warehouse', 'user', 'lines.item', 'lines.salesOrderLine');

        return view('sales-delivery-notes.show', compact('salesDeliveryNote'));
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
