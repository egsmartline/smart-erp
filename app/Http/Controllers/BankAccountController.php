<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Payment;
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
            'account_id' => 'required|exists:chart_of_accounts,id',
            'currency_id' => 'required|exists:currencies,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = true;
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;
        if (isset($validated['description'])) {
            $validated['notes'] = $validated['description'];
            unset($validated['description']);
        }

        BankAccount::create($validated);

        return redirect()->route('bank-accounts.index')->with('success', 'تم إنشاء الحساب البنكي بنجاح');
    }

    public function show(BankAccount $bankAccount)
    {
        $this->authorizeTenant($bankAccount);
        $payments = Payment::where('bank_account_id', $bankAccount->id)
            ->with(['customer', 'supplier', 'user'])
            ->orderBy('date', 'desc')
            ->get();
        return view('bank-accounts.show', compact('bankAccount', 'payments'));
    }

    public function edit(BankAccount $bankAccount)
    {
        $this->authorizeTenant($bankAccount);
        $accounts = $this->tenantQuery(\App\Models\Account::class)
            ->where('type', 'asset')
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
            'account_id' => 'required|exists:chart_of_accounts,id',
            'currency_id' => 'required|exists:currencies,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if (isset($validated['description'])) {
            $validated['notes'] = $validated['description'];
            unset($validated['description']);
        }

        if (array_key_exists('opening_balance', $validated)) {
            $diff = $validated['opening_balance'] - ($bankAccount->opening_balance ?? 0);
            $validated['current_balance'] = $bankAccount->current_balance + $diff;
        }

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
