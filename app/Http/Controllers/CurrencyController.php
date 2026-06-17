<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends TenantAwareController
{
    public function index()
    {
        $currencies = $this->tenantQuery(Currency::class)->orderBy('name')->get();
        return view('currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('currencies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:currencies,code,NULL,id,tenant_id,' . $this->getTenantId(),
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'is_default' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();

        if ($validated['is_default'] ?? false) {
            $this->tenantQuery(Currency::class)->update(['is_default' => false]);
        }

        Currency::create($validated);

        return redirect()->route('currencies.index')->with('success', 'تم إنشاء العملة بنجاح');
    }

    public function show(Currency $currency)
    {
        $this->authorizeTenant($currency);
        return view('currencies.show', compact('currency'));
    }

    public function edit(Currency $currency)
    {
        $this->authorizeTenant($currency);
        return view('currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $this->authorizeTenant($currency);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:currencies,code,' . $currency->id . ',id,tenant_id,' . $this->getTenantId(),
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            $this->tenantQuery(Currency::class)->update(['is_default' => false]);
        }

        $currency->update($validated);

        return redirect()->route('currencies.index')->with('success', 'تم تحديث العملة بنجاح');
    }

    public function destroy(Currency $currency)
    {
        $this->authorizeTenant($currency);
        $currency->delete();
        return redirect()->route('currencies.index')->with('success', 'تم حذف العملة بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
