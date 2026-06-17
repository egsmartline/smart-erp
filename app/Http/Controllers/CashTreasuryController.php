<?php

namespace App\Http\Controllers;

use App\Models\CashTreasury;
use Illuminate\Http\Request;

class CashTreasuryController extends TenantAwareController
{
    public function index()
    {
        $treasuries = $this->tenantQuery(CashTreasury::class)->orderBy('name')->get();
        return view('cash-treasuries.index', compact('treasuries'));
    }

    public function create()
    {
        $accounts = $this->tenantQuery(\App\Models\Account::class)
            ->where('type', 'asset')
            ->where('is_header', false)
            ->where('is_active', true)
            ->get();

        return view('cash-treasuries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = true;
        $validated['current_balance'] = 0;

        CashTreasury::create($validated);

        return redirect()->route('cash-treasuries.index')->with('success', 'تم إنشاء الخزينة بنجاح');
    }

    public function show(CashTreasury $cashTreasury)
    {
        $this->authorizeTenant($cashTreasury);
        return view('cash-treasuries.show', ['treasury' => $cashTreasury]);
    }

    public function edit(CashTreasury $cashTreasury)
    {
        $this->authorizeTenant($cashTreasury);
        $accounts = $this->tenantQuery(\App\Models\Account::class)
            ->where('type', 'asset')
            ->where('is_header', false)
            ->where('is_active', true)
            ->get();

        return view('cash-treasuries.edit', ['treasury' => $cashTreasury, 'accounts' => $accounts]);
    }

    public function update(Request $request, CashTreasury $cashTreasury)
    {
        $this->authorizeTenant($cashTreasury);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id',
            'description' => 'nullable|string',
        ]);

        $cashTreasury->update($validated);

        return redirect()->route('cash-treasuries.index')->with('success', 'تم تحديث الخزينة بنجاح');
    }

    public function destroy(CashTreasury $cashTreasury)
    {
        $this->authorizeTenant($cashTreasury);
        $cashTreasury->delete();
        return redirect()->route('cash-treasuries.index')->with('success', 'تم حذف الخزينة بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
