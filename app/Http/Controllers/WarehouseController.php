<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends TenantAwareController
{
    public function index()
    {
        $warehouses = $this->tenantQuery(Warehouse::class)->withCount('items')->orderBy('name')->get();
        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:warehouses,code,NULL,id,tenant_id,' . $this->getTenantId(),
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'manager_name' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $validated['is_active'] ?? true;

        if ($validated['is_default'] ?? false) {
            $this->tenantQuery(Warehouse::class)->update(['is_default' => false]);
        }

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')->with('success', 'تم إنشاء المخزن بنجاح');
    }

    public function show(Warehouse $warehouse)
    {
        $this->authorizeTenant($warehouse);
        $warehouse->load('items.item.category', 'items.item.unit');
        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        $this->authorizeTenant($warehouse);
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $this->authorizeTenant($warehouse);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:warehouses,code,' . $warehouse->id . ',id,tenant_id,' . $this->getTenantId(),
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'manager_name' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            $this->tenantQuery(Warehouse::class)->update(['is_default' => false]);
        }

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')->with('success', 'تم تحديث المخزن بنجاح');
    }

    public function destroy(Warehouse $warehouse)
    {
        $this->authorizeTenant($warehouse);
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'تم حذف المخزن بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
