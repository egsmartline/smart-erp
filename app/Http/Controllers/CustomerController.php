<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends TenantAwareController
{
    public function index(Request $request)
    {
        $customers = $this->tenantQuery(Customer::class)
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
            ->when($request->classification, fn($q, $c) => $q->where('classification', $c))
            ->orderBy('name')
            ->paginate(20);

        $totalBalance = $this->tenantQuery(Customer::class)->where('is_active', true)->sum('balance');

        return view('customers.index', compact('customers', 'totalBalance'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'classification' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'opening_balance_type' => 'nullable|in:debit,credit',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = true;
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['opening_balance_type'] = $validated['opening_balance_type'] ?? 'debit';
        $validated['balance'] = $validated['opening_balance_type'] === 'credit'
            ? -$validated['opening_balance']
            : $validated['opening_balance'];

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'تم إنشاء العميل بنجاح');
    }

    public function show(Customer $customer)
    {
        $this->authorizeTenant($customer);
        $customer->load(['salesInvoices' => fn($q) => $q->latest()->limit(20), 'payments' => fn($q) => $q->latest()->limit(20)]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->authorizeTenant($customer);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorizeTenant($customer);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'classification' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'opening_balance_type' => 'nullable|in:debit,credit',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['opening_balance_type'] = $validated['opening_balance_type'] ?? 'debit';
        $validated['balance'] = $validated['opening_balance_type'] === 'credit'
            ? -$validated['opening_balance']
            : $validated['opening_balance'];

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'تم تحديث العميل بنجاح');
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeTenant($customer);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'تم حذف العميل بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
