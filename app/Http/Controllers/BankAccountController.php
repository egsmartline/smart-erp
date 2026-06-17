<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends TenantAwareController
{
    public function index()
    {
        $bankAccounts = $this->tenantQuery(BankAccount::class)->orderBy('bank_name')->get();
        return view('bank-accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        $accounts = $this->tenantQuery(\App\Models\Account::class)
            ->where('type', 'asset')
            ->where('is_header', false)
            ->where('is_active', true)
            ->get();

        $currencies = $this->tenantQuery(\App\Models\Currency::class)->get();

        return view('bank-accounts.create', compact('accounts', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'account_id' => 'required|exists:accounts,id',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = true;
        $validated['current_balance'] = 0;

        BankAccount::create($validated);

        return redirect()->route('bank-accounts.index')->with('success', 'تم إنشاء الحساب البنكي بنجاح');
    }

    public function show(BankAccount $bankAccount)
    {
        $this->authorizeTenant($bankAccount);
        return view('bank-accounts.show', compact('bankAccount'));
    }

    public function edit(BankAccount $bankAccount)
    {
        $this->authorizeTenant($bankAccount);
        $accounts = $this->tenantQuery(\App\Models\Account::class)
            ->where('type', 'asset')
            ->where('is_header', false)
            ->where('is_active', true)
            ->get();

        $currencies = $this->tenantQuery(\App\Models\Currency::class)->get();

        return view('bank-accounts.edit', compact('bankAccount', 'accounts', 'currencies'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $this->authorizeTenant($bankAccount);

        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'iban' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'account_id' => 'required|exists:accounts,id',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'nullable|string',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('bank-accounts.index')->with('success', 'تم تحديث الحساب البنكي بنجاح');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $this->authorizeTenant($bankAccount);
        $bankAccount->delete();
        return redirect()->route('bank-accounts.index')->with('success', 'تم حذف الحساب البنكي بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
