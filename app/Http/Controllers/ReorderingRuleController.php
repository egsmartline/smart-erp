<?php

namespace App\Http\Controllers;

use App\Models\ReorderingRule;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ReorderingRuleController extends TenantAwareController
{
    public function index(Request $request)
    {
        $rules = ReorderingRule::where('tenant_id', $this->getTenantId())
            ->with(['item', 'warehouse'])
            ->latest()
            ->paginate(20);

        $warehouses = Warehouse::where('tenant_id', $this->getTenantId())->orderBy('name')->get();
        $items = Item::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('reordering-rules.index', compact('rules', 'warehouses', 'items'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('tenant_id', $this->getTenantId())->orderBy('name')->get();
        $items = Item::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('reordering-rules.create', compact('warehouses', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'minimum_qty' => 'required|numeric|min:0',
            'maximum_qty' => 'required|numeric|min:0|gte:minimum_qty',
            'reorder_qty' => 'required|numeric|min:0.01',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $validated['is_active'] ?? true;

        ReorderingRule::create($validated);

        return redirect()->route('reordering-rules.index')->with('success', 'تم إنشاء قاعدة إعادة الطلب بنجاح');
    }

    public function destroy(ReorderingRule $rule)
    {
        if ($rule->tenant_id !== $this->getTenantId()) abort(403);
        $rule->delete();

        return redirect()->route('reordering-rules.index')->with('success', 'تم حذف القاعدة بنجاح');
    }
}
