<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SalesInvoice;
use App\Models\Account;
use App\Models\CashTreasury;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\BankAccount;
use App\Models\Currency;
use App\Models\TreasuryTransaction;
use App\Models\BankTransaction;
use App\Models\JournalEntry;
use App\Models\Company;
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

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('payment_number', 'like', "%{$s}%");
                $q->orWhere('notes', 'like', "%{$s}%");
                $q->orWhereHas('customer', function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%");
                });
                $q->orWhereHas('supplier', function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%");
                });
            });
        }

        if ($request->print) {
            $payments = $query->orderBy('date')->get();
            $totalReceipts = (clone $query)->where('type', 'receipt')->sum('amount');
            $totalPayments = (clone $query)->where('type', 'payment')->sum('amount');
            return view('payments.print', compact('payments', 'totalReceipts', 'totalPayments'));
        }

        $totalReceipts = (clone $query)->where('type', 'receipt')->sum('amount');
        $totalPayments = (clone $query)->where('type', 'payment')->sum('amount');

        $payments = $query->orderBy('date', 'desc')->paginate(20);

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
        $invoices = $this->tenantQuery(SalesInvoice::class)
            ->where('status', 'posted')
            ->where('payment_status', '!=', 'paid')
            ->orderBy('date', 'desc')
            ->get(['id', 'invoice_number', 'customer_id', 'total', 'paid_amount', 'due_amount']);

        return view('payments.create', compact('customers', 'suppliers', 'accounts', 'treasuries', 'bankAccounts', 'currencies', 'invoices'));
    }

    public function bulkCreate()
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $suppliers = $this->tenantQuery(Supplier::class)->where('is_active', true)->get();
        $treasuries = $this->tenantQuery(CashTreasury::class)->where('is_active', true)->orderBy('name')->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->where('is_active', true)->get();
        $currencies = $this->tenantQuery(Currency::class)->get();
        $invoices = $this->tenantQuery(SalesInvoice::class)
            ->where('status', 'posted')
            ->where('payment_status', '!=', 'paid')
            ->orderBy('date', 'desc')
            ->get(['id', 'invoice_number', 'customer_id', 'total', 'paid_amount', 'due_amount']);

        return view('payments.bulk-create', compact('customers', 'suppliers', 'treasuries', 'bankAccounts', 'currencies', 'invoices'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.type' => 'required|in:receipt,payment',
            'payments.*.date' => 'required|date',
            'payments.*.payment_method' => 'required|in:cash,bank_transfer,check',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.currency_id' => 'required|exists:currencies,id',
            'payments.*.exchange_rate' => 'required|numeric|min:0.0001',
            'payments.*.customer_id' => 'nullable|exists:customers,id',
            'payments.*.supplier_id' => 'nullable|exists:suppliers,id',
            'payments.*.invoice_id' => 'nullable|exists:sales_invoices,id',
            'payments.*.treasury_id' => 'nullable|exists:cash_treasuries,id',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payments.*.check_number' => 'nullable|string|max:50',
            'payments.*.notes' => 'nullable|string',
        ]);

        $tenantId = $this->getTenantId();
        $userId = auth()->id();
        $created = 0;
        $errors = [];
        $affectedTreasuries = collect();
        $affectedBanks = collect();

        DB::beginTransaction();
        try {
            foreach ($request->payments as $index => $data) {
                try {
                    if ($data['payment_method'] === 'cash' && empty($data['treasury_id'])) {
                        throw new \Exception('الخزينة مطلوبة لطريقة الدفع نقداً');
                    }
                    if ($data['payment_method'] === 'bank_transfer' && empty($data['bank_account_id'])) {
                        throw new \Exception('الحساب البنكي مطلوب لطريقة الدفع تحويل بنكي');
                    }
                    if ($data['payment_method'] === 'check' && empty($data['check_number'])) {
                        throw new \Exception('رقم الشيك مطلوب لطريقة الدفع شيك');
                    }
                    $data['tenant_id'] = $tenantId;
                    $data['payment_number'] = $this->generatePaymentNumber($data['type']);
                    $data['amount_in_currency'] = $data['amount'] * $data['exchange_rate'];
                    $data['user_id'] = $userId;
                    $data['status'] = 'completed';
                    $data['reference'] = $data['reference'] ?? $data['payment_number'];
                    $data['invoice_id'] = !empty($data['invoice_id']) ? $data['invoice_id'] : null;

                    $payment = Payment::create($data);

                    $direction = $data['type'] === 'receipt' ? 1 : -1;
                    $txType = $data['type'] === 'receipt' ? 'in' : 'out';

                    if ($data['payment_method'] === 'cash' && !empty($data['treasury_id'])) {
                        $affectedTreasuries->push($data['treasury_id']);
                        $treasury = CashTreasury::findOrFail($data['treasury_id']);
                        $treasury->increment('current_balance', $data['amount'] * $direction);
                        TreasuryTransaction::create([
                            'tenant_id' => $tenantId,
                            'treasury_id' => $data['treasury_id'],
                            'type' => $txType,
                            'amount' => $data['amount'],
                            'reference_type' => 'payment',
                            'reference_id' => $payment->id,
                            'reference_number' => $data['payment_number'],
                            'description' => $data['notes'] ?? (($data['type'] === 'receipt' ? 'قبض' : 'صرف') . ' - ' . $data['payment_number']),
                            'user_id' => $userId,
                        ]);
                    } elseif ($data['payment_method'] === 'bank_transfer' && !empty($data['bank_account_id'])) {
                        $affectedBanks->push($data['bank_account_id']);
                        $bankAccount = BankAccount::findOrFail($data['bank_account_id']);
                        $bankAccount->increment('current_balance', $data['amount'] * $direction);
                        BankTransaction::create([
                            'tenant_id' => $tenantId,
                            'bank_account_id' => $data['bank_account_id'],
                            'type' => $txType,
                            'amount' => $data['amount'],
                            'reference_type' => 'payment',
                            'reference_id' => $payment->id,
                            'reference_number' => $data['payment_number'],
                            'description' => $data['notes'] ?? (($data['type'] === 'receipt' ? 'قبض' : 'صرف') . ' - ' . $data['payment_number']),
                            'user_id' => $userId,
                        ]);
                    }

                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "الدفعة #" . ($index + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            $affectedTreasuries->unique()->each(fn($id) => $this->recalcTreasuryBalance($id));
            $affectedBanks->unique()->each(fn($id) => $this->recalcBankBalance($id));

            $affectedInvoices = collect();
            foreach ($request->payments as $data) {
                if (!empty($data['invoice_id']) && ($data['type'] ?? '') === 'receipt') {
                    $affectedInvoices->push($data['invoice_id']);
                }
            }
            $affectedInvoices->unique()->each(fn($id) => $this->syncInvoicePaidAmount($id));

            if ($created > 0) {
                $message = "تم تسجيل {$created} دفعة بنجاح";
                if (!empty($errors)) {
                    $message .= " مع وجود " . count($errors) . " أخطاء";
                }
                return redirect()->route('payments.index')->with('success', $message);
            }

            DB::rollBack();
            return back()->withErrors(['error' => 'فشل في تسجيل أي دفعة: ' . implode(', ', $errors)])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطأ: ' . $e->getMessage()])->withInput();
        }
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
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'invoice_id' => 'nullable|exists:sales_invoices,id',
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
        $validated['invoice_id'] = !empty($validated['invoice_id']) ? $validated['invoice_id'] : null;

        $direction = $validated['type'] === 'receipt' ? 1 : -1;
        $txType = $validated['type'] === 'receipt' ? 'in' : 'out';

        DB::beginTransaction();
        try {
            $payment = Payment::create($validated);

            if ($validated['payment_method'] === 'cash' && !empty($validated['treasury_id'])) {
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
            } elseif ($validated['payment_method'] === 'bank_transfer' && !empty($validated['bank_account_id'])) {
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

            if ($validated['payment_method'] === 'cash' && $validated['treasury_id']) {
                $this->recalcTreasuryBalance($validated['treasury_id']);
            } elseif ($validated['payment_method'] === 'bank_transfer' && $validated['bank_account_id']) {
                $this->recalcBankBalance($validated['bank_account_id']);
            }

            if (!empty($validated['invoice_id']) && $validated['type'] === 'receipt') {
                $this->syncInvoicePaidAmount($validated['invoice_id']);
            }

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

    public function voucher(Payment $payment)
    {
        $this->authorizeTenant($payment);
        $payment->load(['customer', 'supplier', 'account', 'treasury', 'bankAccount', 'currency', 'user']);

        $company = Company::where('tenant_id', $this->getTenantId())->first();
        $amountInWords = $this->numberToArabicWords($payment->amount);

        return view('payments.voucher', compact('payment', 'company', 'amountInWords'));
    }

    public function receiptVoucher(Payment $payment)
    {
        $this->authorizeTenant($payment);
        $payment->load(['customer', 'supplier', 'account']);

        $amountInWords = $this->numberToArabicWords($payment->amount);

        return view('payments.receipt-voucher', compact('payment', 'amountInWords'));
    }

    public function paymentVoucher(Payment $payment)
    {
        $this->authorizeTenant($payment);
        $payment->load(['customer', 'supplier', 'account']);

        $amountInWords = $this->numberToArabicWords($payment->amount);

        return view('payments.payment-voucher', compact('payment', 'amountInWords'));
    }

    protected function numberToArabicWords(float $number): string
    {
        if (class_exists(\NumberFormatter::class)) {
            $formatter = new \NumberFormatter('ar', \NumberFormatter::SPELLOUT);
            return $formatter->format($number);
        }
        return number_format($number, 2);
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
        $invoices = $this->tenantQuery(SalesInvoice::class)
            ->where('payment_status', '!=', 'paid')
            ->where('status', 'posted')
            ->orderBy('date', 'desc')
            ->get(['id', 'invoice_number', 'customer_id', 'total', 'paid_amount', 'due_amount']);

        return view('payments.edit', compact('payment', 'customers', 'suppliers', 'accounts', 'treasuries', 'bankAccounts', 'currencies', 'invoices'));
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
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'invoice_id' => 'nullable|exists:sales_invoices,id',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'treasury_id' => 'nullable|exists:cash_treasuries,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'check_number' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['amount_in_currency'] = $validated['amount'] * $validated['exchange_rate'];

        $oldTreasuryId = $payment->treasury_id;
        $oldBankId = $payment->bank_account_id;
        $oldMethod = $payment->payment_method;

        $payment->update($validated);

        $affectedTreasuries = collect();
        $affectedBanks = collect();

        if ($oldMethod === 'cash' && $oldTreasuryId) $affectedTreasuries->push($oldTreasuryId);
        if ($payment->payment_method === 'cash' && $payment->treasury_id) $affectedTreasuries->push($payment->treasury_id);
        if ($oldMethod === 'bank_transfer' && $oldBankId) $affectedBanks->push($oldBankId);
        if ($payment->payment_method === 'bank_transfer' && $payment->bank_account_id) $affectedBanks->push($payment->bank_account_id);

        $affectedTreasuries->unique()->each(fn($id) => $this->recalcTreasuryBalance($id));
        $affectedBanks->unique()->each(fn($id) => $this->recalcBankBalance($id));

        $oldInvoiceId = $payment->invoice_id;
        $newInvoiceId = $validated['invoice_id'] ?? null;

        if ($oldInvoiceId && $oldInvoiceId != $newInvoiceId) {
            $this->syncInvoicePaidAmount($oldInvoiceId);
        }
        if ($newInvoiceId) {
            $this->syncInvoicePaidAmount($newInvoiceId);
        }

        return redirect()->route('payments.index')->with('success', 'تم تحديث الدفعة بنجاح');
    }

    public function destroy(Payment $payment)
    {
        $this->authorizeTenant($payment);

        $treasuryId = $payment->treasury_id;
        $bankAccountId = $payment->bank_account_id;
        $paymentMethod = $payment->payment_method;
        $invoiceId = $payment->invoice_id;

        TreasuryTransaction::where('reference_type', 'payment')->where('reference_id', $payment->id)->delete();
        BankTransaction::where('reference_type', 'payment')->where('reference_id', $payment->id)->delete();

        $payment->delete();

        if ($paymentMethod === 'cash' && $treasuryId) {
            $this->recalcTreasuryBalance($treasuryId);
        } elseif ($paymentMethod === 'bank_transfer' && $bankAccountId) {
            $this->recalcBankBalance($bankAccountId);
        }

        if ($payment->account_id) {
            $journalEntry = JournalEntry::where('reference', $payment->payment_number)->first();
            if ($journalEntry) {
                app(JournalService::class)->reverseEntry($journalEntry);
            }
        }

        if ($invoiceId) {
            $this->syncInvoicePaidAmount($invoiceId);
        }

        return redirect()->route('payments.index')->with('success', 'تم حذف العملية بنجاح');
    }

    protected function recalcTreasuryBalance($treasuryId)
    {
        $treasury = CashTreasury::find($treasuryId);
        if (!$treasury) return;

        $tenantId = $this->getTenantId();
        $receipts = (float) Payment::where('tenant_id', $tenantId)
            ->where('treasury_id', $treasuryId)
            ->where('payment_method', 'cash')
            ->where('type', 'receipt')
            ->sum('amount');
        $payments = (float) Payment::where('tenant_id', $tenantId)
            ->where('treasury_id', $treasuryId)
            ->where('payment_method', 'cash')
            ->where('type', 'payment')
            ->sum('amount');

        $treasury->update(['current_balance' => ($treasury->opening_balance ?? 0) + $receipts - $payments]);
    }

    protected function recalcBankBalance($bankAccountId)
    {
        $bank = BankAccount::find($bankAccountId);
        if (!$bank) return;

        $tenantId = $this->getTenantId();
        $receipts = (float) Payment::where('tenant_id', $tenantId)
            ->where('bank_account_id', $bankAccountId)
            ->where('payment_method', 'bank_transfer')
            ->where('type', 'receipt')
            ->sum('amount');
        $payments = (float) Payment::where('tenant_id', $tenantId)
            ->where('bank_account_id', $bankAccountId)
            ->where('payment_method', 'bank_transfer')
            ->where('type', 'payment')
            ->sum('amount');

        $bank->update(['current_balance' => ($bank->opening_balance ?? 0) + $receipts - $payments]);
    }

    protected function syncInvoicePaidAmount($invoiceId)
    {
        $invoice = SalesInvoice::find($invoiceId);
        if (!$invoice) return;

        $totalPaid = (float) Payment::whereNull('deleted_at')
            ->where('type', 'receipt')
            ->where('customer_id', $invoice->customer_id)
            ->sum('amount');

        $paidAmount = min($totalPaid, $invoice->total);
        $dueAmount = $invoice->total - $paidAmount;
        $paymentStatus = $paidAmount <= 0 ? 'unpaid' : ($dueAmount <= 0 ? 'paid' : 'partial');

        $invoice->update([
            'paid_amount' => $paidAmount,
            'due_amount' => $dueAmount,
            'payment_status' => $paymentStatus,
            'status' => $paymentStatus === 'paid' ? 'paid' : 'posted',
        ]);
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
