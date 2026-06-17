<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemUnit;
use Illuminate\Http\Request;

class ItemController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(Item::class)->with('category', 'unit');

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

        return view('items.create', compact('categories', 'units', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:item_categories,id',
            'unit_id' => 'nullable|exists:item_units,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'minimum_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $request->boolean('is_active', true);

        Item::create($validated);

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

        return view('items.edit', compact('item', 'categories', 'units', 'warehouses'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:item_categories,id',
            'unit_id' => 'nullable|exists:item_units,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'minimum_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

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
