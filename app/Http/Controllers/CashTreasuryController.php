<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CashTreasury;
use App\Models\Payment;
use App\Models\TreasuryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashTreasuryController extends TenantAwareController
{
    public function index()
    {
        $treasuries = $this->tenantQuery(CashTreasury::class)->with('currency')->orderBy('name')->get();
        return view('cash-treasuries.index', compact('treasuries'));
    }

    public function create()
    {
        $accounts = $this->tenantQuery(\App\Models\Account::class)
            ->where('type', 'asset')
            ->where('is_active', true)
            ->get();
        $currencies = $this->tenantQuery(\App\Models\Currency::class)->get();

        return view('cash-treasuries.create', compact('accounts', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:cash_treasuries,code,NULL,id,tenant_id,' . $this->getTenantId(),
            'account_id' => 'required|exists:chart_of_accounts,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:20',
            'whatsapp_message' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = true;
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;
        if (isset($validated['description'])) {
            $validated['notes'] = $validated['description'];
            unset($validated['description']);
        }

        CashTreasury::create($validated);

        return redirect()->route('cash-treasuries.index')->with('success', 'تم إنشاء الخزينة بنجاح');
    }

    public function show(CashTreasury $cashTreasury)
    {
        $this->authorizeTenant($cashTreasury);

        $bankAccounts = $this->tenantQuery(BankAccount::class)
            ->with('currency')
            ->where('is_active', true)
            ->orderBy('account_name')
            ->get();

        $txnData = collect();

        foreach (TreasuryTransaction::where('treasury_id', $cashTreasury->id)->where(function ($q) { $q->where('reference_type', '!=', 'payment')->orWhereNull('reference_type'); })->with('user')->orderBy('created_at', 'desc')->cursor() as $t) {
            $txnData->push((object) [
                'date' => $t->created_at->format('Y-m-d'),
                'type' => $t->type,
                'description' => $t->description ?? $t->reference_number,
                'party' => '-',
                'amount' => $t->amount,
                'user_name' => $t->user->name ?? '-',
            ]);
        }

        foreach (Payment::where('treasury_id', $cashTreasury->id)->with(['customer', 'supplier', 'user'])->orderBy('date', 'desc')->cursor() as $p) {
            $txnData->push((object) [
                'date' => $p->date instanceof \Carbon\Carbon ? $p->date->format('Y-m-d') : $p->date,
                'type' => $p->type,
                'description' => $p->notes ?? $p->payment_number,
                'party' => $p->customer->name ?? $p->supplier->name ?? '-',
                'amount' => $p->amount,
                'user_name' => $p->user->name ?? '-',
            ]);
        }

        $transactions = $txnData->sortByDesc('date')->values();

        return view('cash-treasuries.show', [
            'treasury' => $cashTreasury,
            'transactions' => $transactions,
            'bankAccounts' => $bankAccounts,
        ]);
    }

    public function edit(CashTreasury $cashTreasury)
    {
        $this->authorizeTenant($cashTreasury);
        $accounts = $this->tenantQuery(\App\Models\Account::class)
            ->where('type', 'asset')
            ->where('is_active', true)
            ->get();
        $currencies = $this->tenantQuery(\App\Models\Currency::class)->get();

        return view('cash-treasuries.edit', ['treasury' => $cashTreasury, 'accounts' => $accounts, 'currencies' => $currencies]);
    }

    public function update(Request $request, CashTreasury $cashTreasury)
    {
        $this->authorizeTenant($cashTreasury);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:cash_treasuries,code,' . $cashTreasury->id . ',id,tenant_id,' . $this->getTenantId(),
            'account_id' => 'required|exists:chart_of_accounts,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:20',
            'whatsapp_message' => 'nullable|string',
        ]);

        if (isset($validated['description'])) {
            $validated['notes'] = $validated['description'];
            unset($validated['description']);
        }

        if (array_key_exists('opening_balance', $validated)) {
            $diff = $validated['opening_balance'] - ($cashTreasury->opening_balance ?? 0);
            $validated['current_balance'] = $cashTreasury->current_balance + $diff;
        }

        $cashTreasury->update($validated);

        return redirect()->route('cash-treasuries.index')->with('success', 'تم تحديث الخزينة بنجاح');
    }

    public function balances(Request $request)
    {
        $treasuries = $this->tenantQuery(CashTreasury::class)->with('currency')->orderBy('name')->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->with('currency')->orderBy('account_name')->get();

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $hasFilter = $dateFrom || $dateTo;

        if ($hasFilter) {
            foreach ($treasuries as $t) {
                $t->balance_at_date = $this->calcBalanceAtDate('cash_treasuries', $t->id, $t->opening_balance, $dateFrom, $dateTo);
                $t->period_receipts = $this->sumTreasuryPayments($t->id, 'receipt', $dateFrom, $dateTo);
                $t->period_payments = $this->sumTreasuryPayments($t->id, 'payment', $dateFrom, $dateTo);
            }
            foreach ($bankAccounts as $b) {
                $b->balance_at_date = $this->calcBalanceAtDate('bank_accounts', $b->id, $b->opening_balance, $dateFrom, $dateTo);
                $b->period_receipts = $this->sumBankPayments($b->id, 'receipt', $dateFrom, $dateTo);
                $b->period_payments = $this->sumBankPayments($b->id, 'payment', $dateFrom, $dateTo);
            }
        }

        $treasuryByCurrency = $treasuries->groupBy(fn($t) => $t->currency->code ?? 'ج.م')
            ->map(fn($group) => $group->sum(fn($t) => $t->balance_at_date ?? $t->current_balance));
        $bankByCurrency = $bankAccounts->groupBy(fn($b) => $b->currency->code ?? 'ج.م')
            ->map(fn($group) => $group->sum(fn($b) => $b->balance_at_date ?? $b->current_balance));
        $allCurrencies = collect(array_keys($treasuryByCurrency->toArray() + $bankByCurrency->toArray()))->sort()->values();

        return view('cash-treasuries.balances', compact('treasuries', 'bankAccounts', 'treasuryByCurrency', 'bankByCurrency', 'allCurrencies', 'dateFrom', 'dateTo', 'hasFilter'));
    }

    protected function calcBalanceAtDate($table, $id, $openingBalance, $dateFrom, $dateTo)
    {
        $query = DB::table('payments')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($table, $id) {
                if ($table === 'cash_treasuries') {
                    $q->where('treasury_id', $id)->where('payment_method', 'cash');
                } else {
                    $q->where('bank_account_id', $id)->where('payment_method', 'bank_transfer');
                }
            })
            ->where('tenant_id', $this->getTenantId());

        $receipts = (clone $query)->where('type', 'receipt');
        $payments = (clone $query)->where('type', 'payment');

        if ($dateTo) {
            $receipts->where('date', '<=', $dateTo);
            $payments->where('date', '<=', $dateTo);
        }

        return $openingBalance
            + (float) $receipts->sum('amount')
            - (float) $payments->sum('amount');
    }

    protected function sumTreasuryPayments($treasuryId, $type, $dateFrom, $dateTo)
    {
        $q = DB::table('payments')
            ->whereNull('deleted_at')
            ->where('treasury_id', $treasuryId)
            ->where('payment_method', 'cash')
            ->where('type', $type)
            ->where('tenant_id', $this->getTenantId());
        if ($dateFrom) $q->where('date', '>=', $dateFrom);
        if ($dateTo) $q->where('date', '<=', $dateTo);
        return (float) $q->sum('amount');
    }

    protected function sumBankPayments($bankId, $type, $dateFrom, $dateTo)
    {
        $q = DB::table('payments')
            ->whereNull('deleted_at')
            ->where('bank_account_id', $bankId)
            ->where('payment_method', 'bank_transfer')
            ->where('type', $type)
            ->where('tenant_id', $this->getTenantId());
        if ($dateFrom) $q->where('date', '>=', $dateFrom);
        if ($dateTo) $q->where('date', '<=', $dateTo);
        return (float) $q->sum('amount');
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
