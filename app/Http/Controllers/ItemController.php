<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use App\Models\ItemWarehouse;
use App\Models\Currency;
use Illuminate\Http\Request;

class ItemController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(Item::class)->with('category', 'unit', 'purchaseCurrency', 'salesCurrency', 'warehouses');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('name_ar', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->latest()->paginate(20);
        $categories = $this->tenantQuery(ItemCategory::class)->get();
        $units = $this->tenantQuery(ItemUnit::class)->get();

        return view('items.index', compact('items', 'categories', 'units'));
    }

    public function create()
    {
        $categories = $this->tenantQuery(ItemCategory::class)->get();
        $units = $this->tenantQuery(ItemUnit::class)->get();
        $warehouses = $this->tenantQuery(\App\Models\Warehouse::class)->get();
        $currencies = $this->tenantQuery(Currency::class)->get();

        return view('items.create', compact('categories', 'units', 'warehouses', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255|unique:items,sku,NULL,id,tenant_id,' . $this->getTenantId(),
            'barcode' => 'nullable|string|max:255|unique:items,barcode,NULL,id,tenant_id,' . $this->getTenantId(),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'nullable|exists:item_categories,id',
            'unit_id' => 'nullable|exists:item_units,id',
            'cost_price' => 'required|numeric|min:0',
            'purchase_currency_id' => 'nullable|exists:currencies,id',
            'selling_price' => 'required|numeric|min:0',
            'sales_currency_id' => 'nullable|exists:currencies,id',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'minimum_stock' => 'nullable|numeric|min:0',
            'opening_stock' => 'nullable|numeric|min:0',
            'default_warehouse' => 'nullable|exists:warehouses,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/items'), $filename);
            $validated['image'] = 'uploads/items/' . $filename;
        }

        $item = Item::create($validated);

        if (($validated['opening_stock'] ?? 0) > 0) {
            $warehouseId = $validated['default_warehouse'] ?? $this->tenantQuery(\App\Models\Warehouse::class)->where('is_default', true)->value('id');
            if (!$warehouseId) {
                $warehouseId = $this->tenantQuery(\App\Models\Warehouse::class)->value('id');
            }
            if ($warehouseId) {
                ItemWarehouse::create([
                    'tenant_id' => $this->getTenantId(),
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $validated['opening_stock'],
                ]);
            }
        }

        return redirect()->route('items.index')->with('success', 'تم إضافة الصنف بنجاح');
    }

    public function show(Item $item)
    {
        $item->load('category', 'unit', 'warehouses.warehouse');
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = $this->tenantQuery(ItemCategory::class)->get();
        $units = $this->tenantQuery(ItemUnit::class)->get();
        $warehouses = $this->tenantQuery(\App\Models\Warehouse::class)->get();
        $currencies = $this->tenantQuery(Currency::class)->get();

        return view('items.edit', compact('item', 'categories', 'units', 'warehouses', 'currencies'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255|unique:items,sku,' . $item->id . ',id,tenant_id,' . $this->getTenantId(),
            'barcode' => 'nullable|string|max:255|unique:items,barcode,' . $item->id . ',id,tenant_id,' . $this->getTenantId(),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'nullable|exists:item_categories,id',
            'unit_id' => 'nullable|exists:item_units,id',
            'cost_price' => 'required|numeric|min:0',
            'purchase_currency_id' => 'nullable|exists:currencies,id',
            'selling_price' => 'required|numeric|min:0',
            'sales_currency_id' => 'nullable|exists:currencies,id',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'minimum_stock' => 'nullable|numeric|min:0',
            'opening_stock' => 'nullable|numeric|min:0',
            'default_warehouse' => 'nullable|exists:warehouses,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($item->image && file_exists(public_path($item->image))) {
                unlink(public_path($item->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/items'), $filename);
            $validated['image'] = 'uploads/items/' . $filename;
        }

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'تم تحديث الصنف بنجاح');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'تم حذف الصنف بنجاح');
    }

    public function search(Request $request)
    {
        $term = $request->input('search', '');
        $items = $this->tenantQuery(Item::class)
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('name_ar', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'name_ar', 'sku', 'barcode', 'selling_price', 'cost_price', 'tax_rate']);

        return response()->json($items);
    }
}
