<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockMovementController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = StockMovement::where('tenant_id', $this->getTenantId())
            ->with(['item', 'warehouse', 'creator']);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $stockMovements = $query->latest('created_at')->paginate(20)->withQueryString();
        $items = Item::where('tenant_id', $this->getTenantId())->orderBy('name')->get();
        $warehouses = Warehouse::where('tenant_id', $this->getTenantId())->orderBy('name')->get();

        return view('stock-movements.index', compact('stockMovements', 'items', 'warehouses'));
    }
}
