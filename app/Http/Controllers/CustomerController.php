<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Currency;
use Illuminate\Http\Request;

class CustomerController extends TenantAwareController
{
    public function index(Request $request)
    {
        $customers = $this->tenantQuery(Customer::class)
            ->with('openingBalanceCurrency')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
            ->when($request->classification, fn($q, $c) => $q->where('classification', $c))
            ->orderBy('name')
            ->paginate(20);

        $totalBalance = $this->tenantQuery(Customer::class)->where('is_active', true)->sum('balance');

        return view('customers.index', compact('customers', 'totalBalance'));
    }

    public function create()
    {
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->orderBy('name')->get();
        return view('customers.create', compact('currencies'));
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
            'opening_balance_currency_id' => 'nullable|exists:currencies,id',
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
        $customer->load(['openingBalanceCurrency', 'salesInvoices' => fn($q) => $q->latest(), 'payments' => fn($q) => $q->latest(), 'discountNotes' => fn($q) => $q->latest()]);

        $openingBal = (float) ($customer->opening_balance ?? 0);
        $realBalance = $customer->opening_balance_type === 'credit' ? -$openingBal : $openingBal;
        foreach ($customer->salesInvoices as $inv) { $realBalance += (float) $inv->total; }
        foreach ($customer->payments as $pay) {
            $amount = (float) $pay->amount;
            if ($pay->type === 'receipt') $amount = -$amount;
            $realBalance += $amount;
        }
        foreach ($customer->discountNotes as $dn) { $realBalance -= (float) $dn->amount; }

        $receivableCustomers = $this->tenantQuery(Customer::class)
            ->where('is_active', true)
            ->with(['openingBalanceCurrency', 'salesInvoices' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'total', 'date', 'created_at'),
                    'payments' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'type', 'amount', 'payment_method', 'date', 'created_at'),
                    'discountNotes' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'amount', 'date', 'created_at')])
            ->get()
            ->map(function ($c) {
                $openingBal = (float) ($c->opening_balance ?? 0);
                $balance = $c->opening_balance_type === 'credit' ? -$openingBal : $openingBal;
                foreach ($c->salesInvoices as $inv) { $balance += (float) $inv->total; }
                foreach ($c->payments as $pay) {
                    $amount = (float) $pay->amount;
                    if ($pay->type === 'receipt') $amount = -$amount;
                    $balance += $amount;
                }
                foreach ($c->discountNotes as $dn) { $balance -= (float) $dn->amount; }
                $c->real_balance = $balance;
                return $c;
            })
            ->filter(fn($c) => $c->real_balance > 0)
            ->sortByDesc('real_balance')
            ->values();

        $totalReceivable = $receivableCustomers->sum('real_balance');

        return view('customers.show', compact('customer', 'receivableCustomers', 'totalReceivable', 'realBalance'));
    }

    public function edit(Customer $customer)
    {
        $this->authorizeTenant($customer);
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->orderBy('name')->get();
        return view('customers.edit', compact('customer', 'currencies'));
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
            'opening_balance_currency_id' => 'nullable|exists:currencies,id',
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

    public function balanceReport(Request $request)
    {
        $customers = $this->tenantQuery(Customer::class)
            ->where('is_active', true)
            ->with(['openingBalanceCurrency', 'salesInvoices' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'total', 'date', 'created_at'),
                    'payments' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'type', 'amount', 'payment_method', 'date', 'created_at'),
                    'discountNotes' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'amount', 'date', 'created_at')])
            ->get()
            ->map(function ($customer) {
                $openingBal = (float) ($customer->opening_balance ?? 0);
                $balance = $customer->opening_balance_type === 'credit' ? -$openingBal : $openingBal;

                foreach ($customer->salesInvoices as $inv) {
                    $balance += (float) $inv->total;
                }
                foreach ($customer->payments as $pay) {
                    $amount = (float) $pay->amount;
                    if ($pay->type === 'receipt') $amount = -$amount;
                    $balance += $amount;
                }
                foreach ($customer->discountNotes as $dn) { $balance -= (float) $dn->amount; }

                $customer->real_balance = $balance;
                return $customer;
            })
            ->filter(fn($c) => $c->real_balance > 0)
            ->sortByDesc('real_balance')
            ->values();

        $totalReceivable = $customers->sum('real_balance');
        $count = $customers->count();

        if ($request->ajax()) {
            return response()->json(['customers' => $customers, 'total' => $totalReceivable, 'count' => $count]);
        }

        return view('customers.balance-report', compact('customers', 'totalReceivable', 'count'));
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
