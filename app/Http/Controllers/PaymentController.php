<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class PaymentController extends TenantAwareController
{
    public function index(Request $request)
    {
        $payments = $this->tenantQuery(Payment::class)
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->where('is_active', true)->get();

        return view('payments.create', compact('customers', 'suppliers', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:receipt,payment',
            'date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'customer_id' => 'required_if:type,receipt|nullable|exists:customers,id',
            'supplier_id' => 'required_if:type,payment|nullable|exists:suppliers,id',
            'bank_account_id' => 'required_if:payment_method,bank_transfer|nullable|exists:bank_accounts,id',
            'check_number' => 'required_if:payment_method,check|nullable|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['amount_in_base_currency'] = $validated['amount'] * $validated['exchange_rate'];
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'completed';

        Payment::create($validated);

        $message = $validated['type'] === 'receipt' ? 'تم تسجيل القبض بنجاح' : 'تم تسجيل الدفع بنجاح';
        return redirect()->route('payments.index')->with('success', $message);
    }

    public function show(Payment $payment)
    {
        $this->authorizeTenant($payment);
        $payment->load(['customer', 'supplier', 'bankAccount', 'currency']);
        return view('payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $this->authorizeTenant($payment);
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'تم حذف العملية بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
