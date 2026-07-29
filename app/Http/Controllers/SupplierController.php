<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Currency;
use Illuminate\Http\Request;

class SupplierController extends TenantAwareController
{
    public function index(Request $request)
    {
        $suppliers = $this->tenantQuery(Supplier::class)
            ->with('openingBalanceCurrency')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(20);

        $totalBalance = $this->tenantQuery(Supplier::class)->where('is_active', true)->sum('balance');

        return view('suppliers.index', compact('suppliers', 'totalBalance'));
    }

    public function create()
    {
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->orderBy('name')->get();
        return view('suppliers.create', compact('currencies'));
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

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'تم إنشاء المورد بنجاح');
    }

    public function show(Supplier $supplier)
    {
        $this->authorizeTenant($supplier);
        $supplier->load(['openingBalanceCurrency', 'purchaseInvoices' => fn($q) => $q->with('currency')->latest(), 'payments' => fn($q) => $q->with('currency')->latest()]);

        $openingBal = (float) ($supplier->opening_balance ?? 0);
        $openingBalSign = $supplier->opening_balance_type === 'credit' ? -$openingBal : $openingBal;

        $openingCurrencyCode = $supplier->openingBalanceCurrency?->code ?? 'default';
        $currencyGroups = [];

        foreach ($supplier->purchaseInvoices as $inv) {
            $curCode = $inv->currency?->code ?? 'default';
            if (!isset($currencyGroups[$curCode])) {
                $currencyGroups[$curCode] = [
                    'currency' => $inv->currency,
                    'openingBalance' => 0,
                    'transactions' => collect(),
                ];
            }
            $currencyGroups[$curCode]['transactions']->push([
                'date' => $inv->invoice_date ?? $inv->created_at,
                'type' => 'فاتورة شراء',
                'badge' => 'bg-orange-100 text-orange-800',
                'reference' => $inv->invoice_number,
                'amount' => (float) $inv->total,
                'sort' => ($inv->invoice_date?->format('Y-m-d') ?? '0000-00-00') . '|' . $inv->created_at,
            ]);
        }

        foreach ($supplier->payments as $pay) {
            $amount = (float) $pay->amount;
            if ($pay->type === 'payment') $amount = -$amount;
            $curCode = $pay->currency?->code ?? 'default';
            if (!isset($currencyGroups[$curCode])) {
                $currencyGroups[$curCode] = [
                    'currency' => $pay->currency,
                    'openingBalance' => 0,
                    'transactions' => collect(),
                ];
            }
            $currencyGroups[$curCode]['transactions']->push([
                'date' => $pay->date,
                'type' => $pay->payment_method === 'bank_transfer' ? 'تحويل بنكي' : ($pay->payment_method === 'check' ? 'شيك' : 'نقداً'),
                'badge' => 'bg-emerald-100 text-emerald-800',
                'reference' => $pay->payment_number,
                'amount' => $amount,
                'sort' => ($pay->date ? $pay->date->format('Y-m-d') : '0000-00-00') . '|' . $pay->created_at,
            ]);
        }

        if (!isset($currencyGroups[$openingCurrencyCode])) {
            $currencyGroups[$openingCurrencyCode] = [
                'currency' => $supplier->openingBalanceCurrency,
                'openingBalance' => 0,
                'transactions' => collect(),
            ];
        }
        $currencyGroups[$openingCurrencyCode]['openingBalance'] += $openingBalSign;

        foreach ($currencyGroups as $code => &$group) {
            $group['transactions'] = $group['transactions']->sortBy('sort')->values();
        }
        unset($group);

        $currencyGroups = array_filter($currencyGroups, fn($g) => $g['openingBalance'] != 0 || $g['transactions']->isNotEmpty());

        return view('suppliers.show', compact('supplier', 'currencyGroups'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorizeTenant($supplier);
        $currencies = $this->tenantQuery(Currency::class)->where('is_active', true)->orderBy('name')->get();
        return view('suppliers.edit', compact('supplier', 'currencies'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorizeTenant($supplier);

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

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'تم تحديث المورد بنجاح');
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorizeTenant($supplier);
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'تم حذف المورد بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
