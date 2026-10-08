<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Allowance;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\CashTreasury;
use App\Models\Employee;
use App\Models\TreasuryTransaction;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllowanceController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = Allowance::where('tenant_id', $this->getTenantId())
            ->with('employee', 'treasury', 'bankAccount');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $allowances = $query->latest()->paginate(20)->withQueryString();
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();

        return view('allowances.index', compact('allowances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();
        $treasuries = $this->tenantQuery(CashTreasury::class)->active()->orderBy('name')->get();
        $bankAccounts = $this->tenantQuery(BankAccount::class)->active()->orderBy('account_name')->get();

        return view('allowances.create', compact('employees', 'treasuries', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:housing,transport,communication,travel',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'payment_method' => 'required|in:treasury,bank,payroll',
            'cash_treasury_id' => 'required_if:payment_method,treasury|nullable|exists:cash_treasuries,id',
            'bank_account_id' => 'required_if:payment_method,bank|nullable|exists:bank_accounts,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['user_id'] = auth()->id();
        $validated['allowance_number'] = 'ALW-' . str_pad(Allowance::withTrashed()->max('id') + 1, 5, '0', STR_PAD_LEFT);
        $validated['status'] = $validated['payment_method'] === 'payroll' ? 'pending' : 'paid';

        DB::beginTransaction();
        try {
            $allowance = Allowance::create($validated);
            $allowance->load('employee');

            $employeeName = $allowance->employee->full_name ?? '';

            if ($validated['payment_method'] === 'treasury') {
                $treasury = CashTreasury::findOrFail($validated['cash_treasury_id']);
                $treasury->decrement('current_balance', $validated['amount']);

                TreasuryTransaction::create([
                    'tenant_id' => $validated['tenant_id'],
                    'treasury_id' => $validated['cash_treasury_id'],
                    'type' => 'out',
                    'amount' => $validated['amount'],
                    'date' => $validated['date'],
                    'reference_type' => 'allowance',
                    'reference_id' => $allowance->id,
                    'reference_number' => $validated['allowance_number'],
                    'description' => $allowance->type_label . ' - ' . $employeeName,
                    'user_id' => auth()->id(),
                ]);

                $this->createJournal($allowance, $treasury->account_id, $employeeName);
            } elseif ($validated['payment_method'] === 'bank') {
                $bankAccount = BankAccount::findOrFail($validated['bank_account_id']);
                $bankAccount->decrement('current_balance', $validated['amount']);

                BankTransaction::create([
                    'tenant_id' => $validated['tenant_id'],
                    'bank_account_id' => $validated['bank_account_id'],
                    'type' => 'out',
                    'amount' => $validated['amount'],
                    'date' => $validated['date'],
                    'reference_type' => 'allowance',
                    'reference_id' => $allowance->id,
                    'reference_number' => $validated['allowance_number'],
                    'description' => $allowance->type_label . ' - ' . $employeeName,
                    'user_id' => auth()->id(),
                ]);

                $this->createJournal($allowance, $bankAccount->account_id, $employeeName);
            }

            DB::commit();
            return redirect()->route('allowances.index')->with('success', 'تم تسجيل البدل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    protected function getAllowanceAccount(int $tenantId)
    {
        $account = Account::where('tenant_id', $tenantId)
            ->where('type', 'expense')
            ->where(function ($q) {
                $q->where('name', 'بدلات')->orWhere('name_en', 'Allowances');
            })
            ->first();

        if ($account) {
            return $account;
        }

        $parent = Account::where('tenant_id', $tenantId)->where('code', '5')->where('type', 'expense')->first();
        if (!$parent) {
            return null;
        }

        $usedCodes = Account::where('tenant_id', $tenantId)
            ->where('type', 'expense')
            ->pluck('code')
            ->all();

        $code = null;
        for ($i = 57; $i <= 99; $i++) {
            if (!in_array((string) $i, $usedCodes)) {
                $code = (string) $i;
                break;
            }
        }
        if (!$code) {
            return null;
        }

        return Account::create([
            'tenant_id' => $tenantId,
            'code' => $code,
            'name' => 'بدلات',
            'name_en' => 'Allowances',
            'type' => 'expense',
            'sub_type' => 'other_expenses',
            'parent_id' => $parent->id,
            'opening_balance' => 0,
            'current_balance' => 0,
            'is_active' => true,
        ]);
    }

    protected function createJournal(Allowance $allowance, ?int $creditAccountId, string $employeeName): void
    {
        if (!$creditAccountId) {
            return;
        }

        $journalService = app(JournalService::class);
        $allowanceAccount = $this->getAllowanceAccount($allowance->tenant_id);
        if (!$allowanceAccount) {
            return;
        }

        $journalService->createEntry([
            'tenant_id' => $allowance->tenant_id,
            'date' => $allowance->date->format('Y-m-d'),
            'description' => $allowance->type_label . ' - ' . $employeeName . ' - ' . $allowance->allowance_number,
            'reference' => $allowance->allowance_number,
            'type' => 'allowance',
            'lines' => [
                ['account_id' => $allowanceAccount->id, 'debit' => $allowance->amount, 'credit' => 0],
                ['account_id' => $creditAccountId, 'debit' => 0, 'credit' => $allowance->amount],
            ],
        ]);
    }

    public function destroy(Allowance $allowance)
    {
        if ($allowance->tenant_id !== $this->getTenantId()) abort(403);

        DB::beginTransaction();
        try {
            $journalService = app(JournalService::class);
            $journalService->reverseEntryByReference($allowance->allowance_number, 'allowance');

            if ($allowance->status === 'paid') {
                if ($allowance->payment_method === 'treasury' && $allowance->cash_treasury_id) {
                    CashTreasury::findOrFail($allowance->cash_treasury_id)->increment('current_balance', $allowance->amount);
                    TreasuryTransaction::where('reference_type', 'allowance')
                        ->where('reference_id', $allowance->id)->delete();
                } elseif ($allowance->payment_method === 'bank' && $allowance->bank_account_id) {
                    BankAccount::findOrFail($allowance->bank_account_id)->increment('current_balance', $allowance->amount);
                    BankTransaction::where('reference_type', 'allowance')
                        ->where('reference_id', $allowance->id)->delete();
                }
            }

            $allowance->delete();

            DB::commit();
            return redirect()->route('allowances.index')->with('success', 'تم حذف البدل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
