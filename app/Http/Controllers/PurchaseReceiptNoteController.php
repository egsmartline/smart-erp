<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReceiptNote;
use App\Models\PurchaseReceiptNoteLine;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\ItemWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReceiptNoteController extends TenantAwareController
{
    public function index()
    {
        $receiptNotes = PurchaseReceiptNote::where('tenant_id', $this->getTenantId())
            ->with('purchaseOrder', 'supplier', 'user')
            ->orderByDesc('id')
            ->paginate(20);

        return view('purchase-receipt-notes.index', compact('receiptNotes'));
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::where('tenant_id', $this->getTenantId())
            ->whereIn('status', ['confirmed', 'received'])
            ->with('supplier')
            ->orderByDesc('id')
            ->get();

        return view('purchase-receipt-notes.create', compact('purchaseOrders'));
    }

    public function store(Request $request)
    {
        $lines = $request->input('lines');
        if (is_string($lines)) {
            $lines = json_decode($lines, true);
            $request->merge(['lines' => $lines]);
        }

        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.purchase_order_line_id' => 'required|exists:purchase_order_lines,id',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.total' => 'required|numeric|min:0',
        ]);

        $tenantId = $this->getTenantId();

        $purchaseOrder = PurchaseOrder::where('tenant_id', $tenantId)->with('supplier', 'warehouse')->findOrFail($validated['purchase_order_id']);

        return DB::transaction(function () use ($validated, $purchaseOrder, $tenantId) {
            $receiptNote = PurchaseReceiptNote::create([
                'tenant_id' => $tenantId,
                'receipt_number' => 'RN-' . now()->format('Ymd') . '-' . str_pad(PurchaseReceiptNote::where('tenant_id', $tenantId)->count() + 1, 4, '0', STR_PAD_LEFT),
                'date' => $validated['date'],
                'purchase_order_id' => $purchaseOrder->id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'warehouse_id' => $purchaseOrder->warehouse_id,
                'user_id' => Auth::id(),
                'status' => 'confirmed',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['lines'] as $line) {
                PurchaseReceiptNoteLine::create([
                    'tenant_id' => $tenantId,
                    'purchase_receipt_note_id' => $receiptNote->id,
                    'purchase_order_line_id' => $line['purchase_order_line_id'],
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'total' => $line['total'],
                ]);

                $orderLine = $purchaseOrder->lines()->findOrFail($line['purchase_order_line_id']);
                $orderLine->increment('received_qty', $line['quantity']);

                $itemWarehouse = ItemWarehouse::firstOrCreate(
                    ['item_id' => $line['item_id'], 'warehouse_id' => $purchaseOrder->warehouse_id],
                    ['quantity' => 0, 'reserved_quantity' => 0, 'average_cost' => 0]
                );

                $itemWarehouse->increment('quantity', $line['quantity']);

                StockMovement::create([
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                    'stockable_type' => PurchaseReceiptNote::class,
                    'stockable_id' => $receiptNote->id,
                    'type' => 'in',
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_price'],
                    'total_cost' => $line['total'],
                    'reference_number' => $receiptNote->receipt_number,
                    'date' => $validated['date'],
                    'notes' => 'استلام مشتريات - ' . $receiptNote->receipt_number,
                    'created_by' => Auth::id(),
                ]);
            }

            $purchaseOrder->update([
                'receipt_status' => 'received',
                'status' => $purchaseOrder->invoice_status === 'fully_invoiced' ? 'invoiced' : 'received',
            ]);

            return redirect()->route('purchase-receipt-notes.show', $receiptNote)
                ->with('success', 'تم إنشاء إذن الاستلام بنجاح');
        });
    }

    public function show(PurchaseReceiptNote $purchaseReceiptNote)
    {
        if ($purchaseReceiptNote->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $purchaseReceiptNote->load('purchaseOrder', 'supplier', 'warehouse', 'user', 'lines.item', 'lines.purchaseOrderLine');

        return view('purchase-receipt-notes.show', compact('purchaseReceiptNote'));
    }

    public function destroy(PurchaseReceiptNote $purchaseReceiptNote)
    {
        if ($purchaseReceiptNote->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        if ($purchaseReceiptNote->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف إذن استلام غير مسودة');
        }

        $purchaseReceiptNote->delete();

        return redirect()->route('purchase-receipt-notes.index')
            ->with('success', 'تم حذف إذن الاستلام بنجاح');
    }
}
