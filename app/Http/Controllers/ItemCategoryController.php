<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoryController extends TenantAwareController
{
    public function index()
    {
        $categories = $this->tenantQuery(ItemCategory::class)->with('children', 'items')->orderBy('name')->get();
        return view('item-categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = $this->tenantQuery(ItemCategory::class)->whereNull('parent_id')->where('is_active', true)->orderBy('name')->get();
        return view('item-categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:item_categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $validated['is_active'] ?? true;

        ItemCategory::create($validated);

        return redirect()->route('item-categories.index')->with('success', 'تم إنشاء التصنيف بنجاح');
    }

    public function show(ItemCategory $itemCategory)
    {
        $this->authorizeTenant($itemCategory);
        $itemCategory->load(['children', 'items.category', 'items.unit']);
        return view('item-categories.show', ['category' => $itemCategory]);
    }

    public function edit(ItemCategory $itemCategory)
    {
        $this->authorizeTenant($itemCategory);
        $parentCategories = $this->tenantQuery(ItemCategory::class)->whereNull('parent_id')->where('id', '!=', $itemCategory->id)->where('is_active', true)->orderBy('name')->get();
        return view('item-categories.edit', ['category' => $itemCategory, 'parentCategories' => $parentCategories]);
    }

    public function update(Request $request, ItemCategory $itemCategory)
    {
        $this->authorizeTenant($itemCategory);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:item_categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $itemCategory->update($validated);

        return redirect()->route('item-categories.index')->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(ItemCategory $itemCategory)
    {
        $this->authorizeTenant($itemCategory);
        $itemCategory->delete();
        return redirect()->route('item-categories.index')->with('success', 'تم حذف التصنيف بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
