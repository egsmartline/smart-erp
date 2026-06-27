<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\CashTreasury;
use App\Models\TreasuryTransaction;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends TenantAwareController
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index()
    {
        $transfers = collect();

        TreasuryTransaction::where('tenant_id', $this->getTenantId())
            ->where('type', 'transfer')
            ->with(['treasury', 'targetTreasury', 'user'])
            ->orderBy('created_at', 'desc')
            ->chunk(100, function ($items) use (&$transfers) {
                $transfers = $transfers->concat($items);
            });

        BankTransaction::where('tenant_id', $this->getTenantId())
            ->where('type', 'transfer')
            ->with(['bankAccount', 'targetBankAccount', 'user'])
            ->orderBy('created_at', 'desc')
            ->chunk(100, function ($items) use (&$transfers) {
                $transfers = $transfers->concat($items);
            });

        $transfers = $transfers->sortByDesc('created_at')
            ->groupBy('reference_number')
            ->map(fn($group) => $group->first())
            ->sortByDesc('created_at');

        return view('transfers.index', compact('transfers'));
    }

    public function create()
    {
        $treasuries = $this->tenantQuery(CashTreasury::class)->with('currency')->where('is_active', true)->orderBy('name')->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->with('currency')->where('is_active', true)->orderBy('account_name')->get();
        $accounts = $this->tenantQuery(Account::class)->where('is_active', true)->orderBy('code')->get(['id', 'name', 'code', 'current_balance']);
        return view('transfers.create', compact('treasuries', 'bankAccounts', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_type' => 'required|in:treasury,bank,account',
            'from_id' => 'required|integer',
            'to_type' => 'required|in:treasury,bank,account',
            'to_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'date' => 'required|date',
        ]);

        if ($validated['from_type'] === $validated['to_type'] && $validated['from_id'] === $validated['to_id']) {
            return back()->withErrors(['error' => 'لا يمكن التحويل من نفس الحساب إلى نفسه'])->withInput();
        }

        DB::beginTransaction();
        try {
            $this->createTransfer($validated);
            DB::commit();
            return redirect()->route('transfers.index')->with('success', 'تم التحويل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطأ في التحويل: ' . $e->getMessage()])->withInput();
        }
    }

    private function createTransfer(array $data): void
    {
        $tenantId = $this->getTenantId();
        $userId = auth()->id();

        $source = $this->resolveSource($data['from_type'], $data['from_id'], $tenantId);
        $target = $this->resolveTarget($data['to_type'], $data['to_id'], $tenantId);

        if ($source['balance'] < $data['amount']) {
            throw new \Exception('الرصيد غير كافٍ في الحساب المصدر');
        }

        $refNum = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        $this->createSourceTransaction($data, $tenantId, $userId, $refNum);
        $this->createTargetTransaction($data, $tenantId, $userId, $refNum);
        $this->updateBalances($data, $source, $target);
        $this->createJournalEntry($data, $tenantId, $source, $target, $refNum);
    }

    private function resolveSource($type, $id, $tenantId): array
    {
        if ($type === 'treasury') {
            $t = CashTreasury::where('tenant_id', $tenantId)->findOrFail($id);
            return ['model' => $t, 'balance' => $t->current_balance, 'name' => $t->name, 'account_id' => $t->account_id];
        }
        if ($type === 'bank') {
            $b = BankAccount::where('tenant_id', $tenantId)->findOrFail($id);
            return ['model' => $b, 'balance' => $b->current_balance, 'name' => $b->account_name, 'account_id' => $b->account_id];
        }
        $a = Account::where('tenant_id', $tenantId)->findOrFail($id);
        return ['model' => $a, 'balance' => $a->current_balance, 'name' => $a->code . ' - ' . $a->name, 'account_id' => $a->id];
    }

    private function resolveTarget($type, $id, $tenantId): array
    {
        if ($type === 'treasury') {
            $t = CashTreasury::where('tenant_id', $tenantId)->findOrFail($id);
            return ['model' => $t, 'name' => $t->name, 'account_id' => $t->account_id];
        }
        if ($type === 'bank') {
            $b = BankAccount::where('tenant_id', $tenantId)->findOrFail($id);
            return ['model' => $b, 'name' => $b->account_name, 'account_id' => $b->account_id];
        }
        $a = Account::where('tenant_id', $tenantId)->findOrFail($id);
        return ['model' => $a, 'name' => $a->code . ' - ' . $a->name, 'account_id' => $a->id];
    }

    private function createSourceTransaction($data, $tenantId, $userId, $refNum)
    {
        $base = [
            'tenant_id' => $tenantId,
            'type' => 'transfer',
            'amount' => $data['amount'],
            'reference_type' => $data['to_type'],
            'reference_id' => $data['to_id'],
            'reference_number' => $refNum,
            'description' => 'تحويل صادر: ' . ($data['description'] ?? ''),
            'user_id' => $userId,
        ];

        if ($data['from_type'] === 'treasury') {
            TreasuryTransaction::create(array_merge($base, [
                'treasury_id' => $data['from_id'],
                'target_treasury_id' => $data['to_type'] === 'treasury' ? $data['to_id'] : null,
            ]));
        } elseif ($data['from_type'] === 'bank') {
            BankTransaction::create(array_merge($base, [
                'bank_account_id' => $data['from_id'],
                'target_bank_account_id' => $data['to_type'] === 'bank' ? $data['to_id'] : null,
            ]));
        } elseif ($data['from_type'] === 'account') {
            TreasuryTransaction::create(array_merge($base, [
                'treasury_id' => null,
                'target_treasury_id' => $data['from_id'],
            ]));
        }
    }

    private function createTargetTransaction($data, $tenantId, $userId, $refNum)
    {
        $base = [
            'tenant_id' => $tenantId,
            'type' => 'transfer',
            'amount' => $data['amount'],
            'reference_type' => $data['from_type'],
            'reference_id' => $data['from_id'],
            'reference_number' => $refNum,
            'description' => 'تحويل وارد: ' . ($data['description'] ?? ''),
            'user_id' => $userId,
        ];

        if ($data['to_type'] === 'treasury') {
            TreasuryTransaction::create(array_merge($base, [
                'treasury_id' => $data['to_id'],
                'target_treasury_id' => $data['from_type'] === 'treasury' ? $data['from_id'] : null,
            ]));
        } elseif ($data['to_type'] === 'bank') {
            BankTransaction::create(array_merge($base, [
                'bank_account_id' => $data['to_id'],
                'target_bank_account_id' => $data['from_type'] === 'bank' ? $data['from_id'] : null,
            ]));
        } elseif ($data['to_type'] === 'account') {
            TreasuryTransaction::create(array_merge($base, [
                'treasury_id' => null,
                'target_treasury_id' => $data['to_id'],
            ]));
        }
    }

    private function updateBalances($data, $source, $target)
    {
        $source['model']->decrement('current_balance', $data['amount']);
        $target['model']->increment('current_balance', $data['amount']);
    }

    private function createJournalEntry($data, $tenantId, $source, $target, $refNum)
    {
        if (! $source['account_id'] || ! $target['account_id']) return;

        $lines = [
            ['account_id' => $target['account_id'], 'debit' => $data['amount'], 'credit' => 0],
            ['account_id' => $source['account_id'], 'debit' => 0, 'credit' => $data['amount']],
        ];

        $this->journalService->createEntry([
            'tenant_id' => $tenantId,
            'date' => $data['date'],
            'description' => 'تحويل من ' . $source['name'] . ' إلى ' . $target['name'],
            'reference' => $refNum,
            'type' => 'transfer',
            'lines' => $lines,
        ]);
    }

    public function edit($id)
    {
        $txn = TreasuryTransaction::where('tenant_id', $this->getTenantId())
            ->where('id', $id)
            ->where('type', 'transfer')
            ->first();

        if (!$txn) {
            $txn = BankTransaction::where('tenant_id', $this->getTenantId())
                ->where('id', $id)
                ->where('type', 'transfer')
                ->firstOrFail();
        }

        $treasuries = $this->tenantQuery(CashTreasury::class)->with('currency')->where('is_active', true)->orderBy('name')->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->with('currency')->where('is_active', true)->orderBy('account_name')->get();
        $accounts = $this->tenantQuery(Account::class)->where('is_active', true)->orderBy('code')->get(['id', 'name', 'code', 'current_balance']);

        $isTreasury = $txn instanceof TreasuryTransaction;
        $fromType = $isTreasury ? ($txn->treasury_id ? 'treasury' : 'account') : 'bank';
        $fromId = $isTreasury ? ($txn->treasury_id ?? $txn->target_treasury_id) : $txn->bank_account_id;
        $toType = $txn->reference_type;
        $toId = $txn->reference_id;

        return view('transfers.edit', compact(
            'txn', 'treasuries', 'bankAccounts', 'accounts',
            'fromType', 'fromId', 'toType', 'toId'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'from_type' => 'required|in:treasury,bank,account',
            'from_id' => 'required|integer',
            'to_type' => 'required|in:treasury,bank,account',
            'to_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'date' => 'required|date',
        ]);

        if ($validated['from_type'] === $validated['to_type'] && $validated['from_id'] === $validated['to_id']) {
            return back()->withErrors(['error' => 'لا يمكن التحويل من نفس الحساب إلى نفسه'])->withInput();
        }

        DB::beginTransaction();
        try {
            $this->deleteTransfer($id);
            $this->createTransfer($validated);
            DB::commit();
            return redirect()->route('transfers.index')->with('success', 'تم تحديث التحويل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطأ في التحديث: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->deleteTransfer($id);
            DB::commit();
            return redirect()->route('transfers.index')->with('success', 'تم حذف التحويل وعكس القيود');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطأ في الحذف: ' . $e->getMessage()]);
        }
    }

    private function deleteTransfer($id): void
    {
        $txn = TreasuryTransaction::where('tenant_id', $this->getTenantId())
            ->where('id', $id)
            ->where('type', 'transfer')
            ->first();

        if ($txn) {
            $related = TreasuryTransaction::where('tenant_id', $this->getTenantId())
                ->where('reference_number', $txn->reference_number)
                ->where('id', '!=', $id);

            $target = $related->first();

            if ($target) {
                if ($txn->treasury_id) {
                    $treasury = CashTreasury::find($txn->treasury_id);
                    if ($treasury) $treasury->increment('current_balance', $txn->amount);
                } else {
                    $account = Account::find($txn->target_treasury_id);
                    if ($account) $account->increment('current_balance', $txn->amount);
                }
                if ($target->treasury_id) {
                    $targetTreasury = CashTreasury::find($target->treasury_id);
                    if ($targetTreasury) $targetTreasury->decrement('current_balance', $txn->amount);
                } else {
                    $targetAccount = Account::find($target->target_treasury_id);
                    if ($targetAccount) $targetAccount->decrement('current_balance', $txn->amount);
                }
                $target->delete();
            }

            $this->journalService->reverseEntryByReference($txn->reference_number, 'transfer');
            $txn->delete();
        } else {
            $txn = BankTransaction::where('tenant_id', $this->getTenantId())
                ->where('id', $id)
                ->where('type', 'transfer')
                ->firstOrFail();

            $related = BankTransaction::where('tenant_id', $this->getTenantId())
                ->where('reference_number', $txn->reference_number)
                ->where('id', '!=', $id)
                ->first();

            if ($related) {
                $bank = BankAccount::find($txn->bank_account_id);
                $targetBank = BankAccount::find($related->bank_account_id);
                if ($bank) $bank->increment('current_balance', $txn->amount);
                if ($targetBank) $targetBank->decrement('current_balance', $txn->amount);
                $related->delete();
            }

            $this->journalService->reverseEntryByReference($txn->reference_number, 'transfer');
            $txn->delete();
        }
    }
}
