<?php

namespace App\Http\Controllers;

use App\Models\SalesDeliveryNote;
use App\Models\SalesDeliveryNoteLine;
use App\Models\SalesInvoice;
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
            ->with('customer', 'user', 'invoice')
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
        $invoices = $this->linkableInvoices($tenantId);

        return view('sales-delivery-notes.create', compact('customers', 'warehouses', 'items', 'invoices'));
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
            'sales_invoice_id' => 'nullable|integer|exists:sales_invoices,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.total' => 'required|numeric|min:0',
        ]);

        $tenantId = $this->getTenantId();
        $invoiceId = $validated['sales_invoice_id'] ?? null;
        $warehouseId = (int) $validated['warehouse_id'];

        if ($invoiceId && !SalesInvoice::where('tenant_id', $tenantId)->where('id', $invoiceId)->exists()) {
            return back()->withInput()->with('error', 'فاتورة المبيعات غير موجودة');
        }

        foreach ($validated['lines'] as $line) {
            if ($this->invoiceHasSold($invoiceId, (int) $line['item_id'], $warehouseId)) {
                continue;
            }

            $itemWarehouse = ItemWarehouse::where('tenant_id', $this->getTenantId())->where('item_id', $line['item_id'])
                ->where('warehouse_id', $warehouseId)
                ->first();
            $available = $itemWarehouse ? $itemWarehouse->quantity : 0;
            if ($available < $line['quantity']) {
                $item = Item::find($line['item_id']);
                $itemName = $item ? $item->name : '#' . $line['item_id'];
                return back()->withInput()->with('error', "الرصيد غير كافٍ للصنف {$itemName} (المتوفر: {$available}، المطلوب: {$line['quantity']})");
            }
        }

        return DB::transaction(function () use ($validated, $tenantId, $invoiceId, $warehouseId) {
            $deliveryNote = SalesDeliveryNote::create([
                'tenant_id' => $tenantId,
                'delivery_number' => $this->nextSequentialNumber('sales_delivery_notes', 'delivery_number', $tenantId, 'DN'),
                'date' => $validated['date'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $warehouseId,
                'sales_order_id' => null,
                'sales_invoice_id' => $invoiceId,
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

                if ($this->invoiceHasSold($invoiceId, (int) $line['item_id'], $warehouseId)) {
                    continue;
                }

                $itemWarehouse = ItemWarehouse::firstOrCreate(
                    ['item_id' => $line['item_id'], 'warehouse_id' => $warehouseId],
                    ['tenant_id' => $tenantId, 'quantity' => 0, 'reserved_quantity' => 0, 'average_cost' => 0]
                );

                $itemWarehouse->decrement('quantity', $line['quantity']);

                $unitCost = $this->effectiveUnitCost((int) $line['item_id'], $warehouseId);

                StockMovement::create([
                    'tenant_id' => $tenantId,
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $warehouseId,
                    'type' => 'sale',
                    'quantity' => $line['quantity'],
                    'unit_cost' => $unitCost,
                    'total_cost' => $unitCost * $line['quantity'],
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

        $salesDeliveryNote->load('customer', 'warehouse', 'user', 'invoice', 'lines.item');

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
        $invoices = $this->linkableInvoices($tenantId);

        return view('sales-delivery-notes.edit', compact('salesDeliveryNote', 'customers', 'warehouses', 'items', 'invoices'));
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
            'sales_invoice_id' => 'nullable|integer|exists:sales_invoices,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.total' => 'required|numeric|min:0',
        ]);

        $tenantId = $this->getTenantId();
        $invoiceId = $validated['sales_invoice_id'] ?? null;
        $warehouseId = (int) $validated['warehouse_id'];

        if ($invoiceId && !SalesInvoice::where('tenant_id', $tenantId)->where('id', $invoiceId)->exists()) {
            return back()->withInput()->with('error', 'فاتورة المبيعات غير موجودة');
        }

        $oldLines = $salesDeliveryNote->lines()->get()->keyBy('item_id');

        foreach ($validated['lines'] as $line) {
            if ($this->invoiceHasSold($invoiceId, (int) $line['item_id'], $warehouseId)) {
                continue;
            }

            $itemWarehouse = ItemWarehouse::where('tenant_id', $this->getTenantId())->where('item_id', $line['item_id'])
                ->where('warehouse_id', $warehouseId)
                ->first();

            $oldQty = isset($oldLines[$line['item_id']]) ? $oldLines[$line['item_id']]->quantity : 0;
            $currentAvailable = $itemWarehouse ? $itemWarehouse->quantity : 0;
            $availableAfterReverse = $currentAvailable + $oldQty;

            if ($availableAfterReverse < $line['quantity']) {
                $item = Item::find($line['item_id']);
                $itemName = $item ? $item->name : '#' . $line['item_id'];
                return back()->withInput()->with('error', "الرصيد غير كافٍ للصنف {$itemName} (المتوفر بعد عكس القديم: {$availableAfterReverse}، المطلوب: {$line['quantity']})");
            }
        }

        DB::transaction(function () use ($salesDeliveryNote, $validated, $tenantId, $request, $invoiceId, $warehouseId) {
            foreach ($salesDeliveryNote->lines as $oldLine) {
                if ($this->noteMovedStock($salesDeliveryNote, (int) $oldLine->item_id)) {
                    $itemWarehouse = ItemWarehouse::where('tenant_id', $this->getTenantId())->where('item_id', $oldLine->item_id)
                        ->where('warehouse_id', $salesDeliveryNote->warehouse_id)
                        ->first();
                    if ($itemWarehouse) {
                        $itemWarehouse->increment('quantity', $oldLine->quantity);
                    }
                }

                StockMovement::where('tenant_id', $tenantId)
                    ->where('reference_type', SalesDeliveryNote::class)
                    ->where('reference_id', $salesDeliveryNote->id)
                    ->where('item_id', $oldLine->item_id)
                    ->delete();
            }

            $salesDeliveryNote->lines()->delete();

            $salesDeliveryNote->update([
                'date' => $validated['date'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $warehouseId,
                'sales_invoice_id' => $invoiceId,
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

                if ($this->invoiceHasSold($invoiceId, (int) $line['item_id'], $warehouseId)) {
                    continue;
                }

                $itemWarehouse = ItemWarehouse::firstOrCreate(
                    ['item_id' => $line['item_id'], 'warehouse_id' => $warehouseId],
                    ['tenant_id' => $tenantId, 'quantity' => 0, 'reserved_quantity' => 0, 'average_cost' => 0]
                );

                $itemWarehouse->decrement('quantity', $line['quantity']);

                $unitCost = $this->effectiveUnitCost((int) $line['item_id'], $warehouseId);

                StockMovement::create([
                    'tenant_id' => $tenantId,
                    'item_id' => $line['item_id'],
                    'warehouse_id' => $warehouseId,
                    'type' => 'sale',
                    'quantity' => $line['quantity'],
                    'unit_cost' => $unitCost,
                    'total_cost' => $unitCost * $line['quantity'],
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

        DB::transaction(function () use ($salesDeliveryNote) {
            if ($salesDeliveryNote->status === 'confirmed') {
                foreach ($salesDeliveryNote->lines as $line) {
                    if ($this->noteMovedStock($salesDeliveryNote, (int) $line->item_id)) {
                        $itemWarehouse = ItemWarehouse::where('tenant_id', $this->getTenantId())->where('item_id', $line->item_id)
                            ->where('warehouse_id', $salesDeliveryNote->warehouse_id)
                            ->first();
                        if ($itemWarehouse) {
                            $itemWarehouse->increment('quantity', $line->quantity);
                        }
                    }

                    StockMovement::where('tenant_id', $salesDeliveryNote->tenant_id)
                        ->where('reference_type', SalesDeliveryNote::class)
                        ->where('reference_id', $salesDeliveryNote->id)
                        ->where('item_id', $line->item_id)
                        ->delete();
                }
            }

            $salesDeliveryNote->lines()->delete();
            $salesDeliveryNote->delete();
        });

        return redirect()->route('sales-delivery-notes.index')
            ->with('success', 'تم حذف إذن التسليم بنجاح');
    }

    /**
     * True only when the linked invoice exists and has already relieved this
     * item from this warehouse, so the delivery note must not do it twice.
     * No link means no claim, and the note moves stock as before.
     */
    private function invoiceHasSold(?int $invoiceId, int $itemId, int $warehouseId): bool
    {
        if (!$invoiceId) {
            return false;
        }

        return StockMovement::where('tenant_id', $this->getTenantId())
            ->where('reference_type', SalesInvoice::class)
            ->where('reference_id', $invoiceId)
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'sale')
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Whether this note itself already took the stock. Used instead of assuming,
     * because a note linked to an invoice that shipped first never moved anything.
     */
    private function noteMovedStock(SalesDeliveryNote $note, int $itemId): bool
    {
        return StockMovement::where('tenant_id', $note->tenant_id)
            ->where('reference_type', SalesDeliveryNote::class)
            ->where('reference_id', $note->id)
            ->where('item_id', $itemId)
            ->whereNull('deleted_at')
            ->exists();
    }

    private function linkableInvoices(int $tenantId)
    {
        return SalesInvoice::where('tenant_id', $tenantId)
            ->whereIn('status', ['draft', 'posted'])
            ->with('customer')
            ->orderByDesc('id')
            ->limit(200)
            ->get(['id', 'invoice_number', 'customer_id', 'total', 'status']);
    }
}
