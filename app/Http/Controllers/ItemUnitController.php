<?php

namespace App\Http\Controllers;

use App\Models\ItemUnit;
use Illuminate\Http\Request;

class ItemUnitController extends TenantAwareController
{
    public function index()
    {
        $units = $this->tenantQuery(ItemUnit::class)->with('items')->orderBy('name')->get();
        return view('item-units.index', compact('units'));
    }

    public function create()
    {
        $baseUnits = $this->tenantQuery(ItemUnit::class)->whereNull('base_unit_id')->where('is_active', true)->orderBy('name')->get();
        return view('item-units.create', compact('baseUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'symbol' => 'nullable|string|max:10',
            'base_unit_id' => 'nullable|exists:item_units,id',
            'conversion_factor' => 'nullable|numeric|min:0.000001',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['conversion_factor'] = $validated['conversion_factor'] ?? 1;

        ItemUnit::create($validated);

        return redirect()->route('item-units.index')->with('success', 'تم إنشاء الوحدة بنجاح');
    }

    public function show(ItemUnit $itemUnit)
    {
        $this->authorizeTenant($itemUnit);
        $itemUnit->load('items.category', 'subUnits');
        return view('item-units.show', ['unit' => $itemUnit]);
    }

    public function edit(ItemUnit $itemUnit)
    {
        $this->authorizeTenant($itemUnit);
        $baseUnits = $this->tenantQuery(ItemUnit::class)->whereNull('base_unit_id')->where('id', '!=', $itemUnit->id)->where('is_active', true)->orderBy('name')->get();
        return view('item-units.edit', ['unit' => $itemUnit, 'baseUnits' => $baseUnits]);
    }

    public function update(Request $request, ItemUnit $itemUnit)
    {
        $this->authorizeTenant($itemUnit);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'symbol' => 'nullable|string|max:10',
            'base_unit_id' => 'nullable|exists:item_units,id',
            'conversion_factor' => 'nullable|numeric|min:0.000001',
            'is_active' => 'boolean',
        ]);

        $itemUnit->update($validated);

        return redirect()->route('item-units.index')->with('success', 'تم تحديث الوحدة بنجاح');
    }

    public function destroy(ItemUnit $itemUnit)
    {
        $this->authorizeTenant($itemUnit);
        $itemUnit->delete();
        return redirect()->route('item-units.index')->with('success', 'تم حذف الوحدة بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
