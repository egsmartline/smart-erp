<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use App\Models\ItemWarehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InventoryCountController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = ItemWarehouse::where('item_warehouses.tenant_id', $this->getTenantId())
            ->with(['item', 'warehouse'])
            ->whereHas('warehouse');

        if ($request->filled('warehouse_id')) {
            $query->where('item_warehouses.warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $inventoryItems = $query
            ->select('item_warehouses.*')
            ->join('items', 'items.id', '=', 'item_warehouses.item_id')
            ->orderBy('items.sku')
            ->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('tenant_id', $this->getTenantId())->orderBy('name')->get();
        $categories = ItemCategory::where('tenant_id', $this->getTenantId())->where('is_active', true)->orderBy('name')->get();

        return view('inventory-count.index', compact('inventoryItems', 'warehouses', 'categories'));
    }
}
