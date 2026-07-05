<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Account;
use App\Models\CashTreasury;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\BankAccount;
use App\Models\Currency;
use App\Models\TreasuryTransaction;
use App\Models\BankTransaction;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(Payment::class)
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->when($request->date_from, fn($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->date_to, fn($q, $d) => $q->whereDate('date', '<=', $d));

        $totalReceipts = (clone $query)->where('type', 'receipt')->sum('amount');
        $totalPayments = (clone $query)->where('type', 'payment')->sum('amount');

        $payments = $query->latest()->paginate(20);

        return view('payments.index', compact('payments', 'totalReceipts', 'totalPayments'));
    }

    public function create()
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $accounts = $this->tenantQuery(Account::class)->where('is_active', true)->orderBy('code')->get();
        $treasuries = $this->tenantQuery(CashTreasury::class)->where('is_active', true)->orderBy('name')->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->where('is_active', true)->get();
        $currencies = $this->tenantQuery(Currency::class)->get();

        return view('payments.create', compact('customers', 'suppliers', 'accounts', 'treasuries', 'bankAccounts', 'currencies'));
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
            'supplier_id' => 'nullable|exists:suppliers,id',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'treasury_id' => 'required_if:payment_method,cash|nullable|exists:cash_treasuries,id',
            'bank_account_id' => 'required_if:payment_method,bank_transfer|nullable|exists:bank_accounts,id',
            'check_number' => 'required_if:payment_method,check|nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['payment_number'] = $this->generatePaymentNumber($validated['type']);
        $validated['amount_in_currency'] = $validated['amount'] * $validated['exchange_rate'];
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'completed';

        $direction = $validated['type'] === 'receipt' ? 1 : -1;
        $txType = $validated['type'] === 'receipt' ? 'in' : 'out';

        DB::beginTransaction();
        try {
            $payment = Payment::create($validated);

            if (!empty($validated['treasury_id'])) {
                $treasury = CashTreasury::findOrFail($validated['treasury_id']);
                $treasury->increment('current_balance', $validated['amount'] * $direction);
                TreasuryTransaction::create([
                    'tenant_id' => $validated['tenant_id'],
                    'treasury_id' => $validated['treasury_id'],
                    'type' => $txType,
                    'amount' => $validated['amount'],
                    'reference_type' => 'payment',
                    'reference_id' => $payment->id,
                    'reference_number' => $validated['payment_number'],
                    'description' => $validated['notes'] ?? (($validated['type'] === 'receipt' ? 'قبض' : 'صرف') . ' - ' . $validated['payment_number']),
                    'user_id' => $validated['user_id'],
                ]);
            }

            if (!empty($validated['bank_account_id'])) {
                $bankAccount = BankAccount::findOrFail($validated['bank_account_id']);
                $bankAccount->increment('current_balance', $validated['amount'] * $direction);
                BankTransaction::create([
                    'tenant_id' => $validated['tenant_id'],
                    'bank_account_id' => $validated['bank_account_id'],
                    'type' => $txType,
                    'amount' => $validated['amount'],
                    'reference_type' => 'payment',
                    'reference_id' => $payment->id,
                    'reference_number' => $validated['payment_number'],
                    'description' => $validated['notes'] ?? (($validated['type'] === 'receipt' ? 'قبض' : 'صرف') . ' - ' . $validated['payment_number']),
                    'user_id' => $validated['user_id'],
                ]);
            }

            if ($validated['account_id']) {
                $journalService = app(JournalService::class);
                $lines = $journalService->buildPaymentLines($validated);
                if (count($lines) === 2) {
                    $journalService->createEntry([
                        'tenant_id' => $validated['tenant_id'],
                        'date' => $validated['date'],
                        'description' => $validated['notes'] ?? ($validated['type'] === 'receipt' ? 'قبض' : 'صرف') . ' - ' . $validated['payment_number'],
                        'reference' => $validated['payment_number'],
                        'type' => $validated['type'] === 'receipt' ? 'receipt' : 'payment',
                        'lines' => $lines,
                    ]);
                }
            }

            DB::commit();
            $message = $validated['type'] === 'receipt' ? 'تم تسجيل القبض بنجاح' : 'تم تسجيل الصرف بنجاح';
            return redirect()->route('payments.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطأ في تسجيل الدفعة: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Payment $payment)
    {
        $this->authorizeTenant($payment);
        $payment->load(['customer', 'supplier', 'account', 'treasury', 'bankAccount', 'currency']);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $this->authorizeTenant($payment);
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $accounts = $this->tenantQuery(Account::class)->where('is_active', true)->orderBy('code')->get();
        $treasuries = $this->tenantQuery(CashTreasury::class)->where('is_active', true)->orderBy('name')->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->where('is_active', true)->get();
        $currencies = $this->tenantQuery(Currency::class)->get();

        return view('payments.edit', compact('payment', 'customers', 'suppliers', 'accounts', 'treasuries', 'bankAccounts', 'currencies'));
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorizeTenant($payment);

        $validated = $request->validate([
            'type' => 'required|in:receipt,payment',
            'date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'customer_id' => 'required_if:type,receipt|nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'treasury_id' => 'nullable|exists:cash_treasuries,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'check_number' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['amount_in_currency'] = $validated['amount'] * $validated['exchange_rate'];

        $payment->update($validated);

        return redirect()->route('payments.index')->with('success', 'تم تحديث الدفعة بنجاح');
    }

    public function destroy(Payment $payment)
    {
        $this->authorizeTenant($payment);

        $direction = $payment->type === 'receipt' ? -1 : 1;

        if ($payment->treasury_id) {
            $treasury = CashTreasury::find($payment->treasury_id);
            if ($treasury) {
                $treasury->increment('current_balance', $payment->amount * $direction);
            }
            TreasuryTransaction::where('reference_type', 'payment')->where('reference_id', $payment->id)->delete();
        }

        if ($payment->bank_account_id) {
            $bankAccount = BankAccount::find($payment->bank_account_id);
            if ($bankAccount) {
                $bankAccount->increment('current_balance', $payment->amount * $direction);
            }
        }

        $payment->delete();

        if ($payment->account_id) {
            $journalEntry = JournalEntry::where('reference', $payment->payment_number)->first();
            if ($journalEntry) {
                app(JournalService::class)->reverseEntry($journalEntry);
            }
        }

        return redirect()->route('payments.index')->with('success', 'تم حذف العملية بنجاح');
    }

    protected function generatePaymentNumber(string $type): string
    {
        $prefix = $type === 'receipt' ? 'RCP' : 'PAY';
        $year = date('Y');
        $last = $this->tenantQuery(Payment::class)
            ->withTrashed()
            ->where('payment_number', 'like', $prefix . '-' . $year . '-%')
            ->max('payment_number');

        if ($last) {
            $seq = (int) substr($last, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
