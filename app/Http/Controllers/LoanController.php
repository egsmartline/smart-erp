<?php

namespace App\Http\Controllers;

use App\Models\CashTreasury;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\TreasuryTransaction;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = Loan::where('tenant_id', $this->getTenantId())
            ->with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->paginate(20)->withQueryString();
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();

        return view('loans.index', compact('loans', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();
        $treasuries = $this->tenantQuery(CashTreasury::class)->active()->orderBy('name')->get();

        return view('loans.create', compact('employees', 'treasuries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0.01',
            'monthly_deduction' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'cash_treasury_id' => 'required|exists:cash_treasuries,id',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['status'] = 'active';
        $validated['remaining'] = $validated['amount'];

        $validated['loan_number'] = 'L-' . str_pad(Loan::withTrashed()->max('id') + 1, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $loan = Loan::create($validated);
            $loan->load('employee');

            $treasury = CashTreasury::findOrFail($validated['cash_treasury_id']);
            $treasury->decrement('current_balance', $validated['amount']);

            TreasuryTransaction::create([
                'tenant_id' => $validated['tenant_id'],
                'treasury_id' => $validated['cash_treasury_id'],
                'type' => 'out',
                'amount' => $validated['amount'],
                'reference_type' => 'loan',
                'reference_id' => $loan->id,
                'reference_number' => $validated['loan_number'],
                'description' => 'سلفة - ' . ($loan->employee->full_name ?? 'موظف'),
                'user_id' => auth()->id(),
            ]);

            $journalService = app(JournalService::class);
            $loanAccount = $journalService->getAccountByCode($validated['tenant_id'], '1105');
            if ($loanAccount && $treasury->account_id) {
                $journalService->createEntry([
                    'tenant_id' => $validated['tenant_id'],
                    'date' => $validated['start_date'],
                    'description' => 'سلفة موظف: ' . ($loan->employee->full_name ?? '') . ' - ' . $validated['loan_number'],
                    'reference' => $validated['loan_number'],
                    'type' => 'loan',
                    'lines' => [
                        ['account_id' => $loanAccount->id, 'debit' => $validated['amount'], 'credit' => 0],
                        ['account_id' => $treasury->account_id, 'debit' => 0, 'credit' => $validated['amount']],
                    ],
                ]);
            }

            DB::commit();
            return redirect()->route('loans.index')->with('success', 'تم إنشاء السلفة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function show(Loan $loan)
    {
        if ($loan->tenant_id !== $this->getTenantId()) abort(403);
        $loan->load(['employee', 'treasury']);

        return view('loans.show', compact('loan'));
    }

    public function destroy(Loan $loan)
    {
        if ($loan->tenant_id !== $this->getTenantId()) abort(403);

        DB::beginTransaction();
        try {
            $journalService = app(JournalService::class);
            $journalService->reverseEntryByReference($loan->loan_number, 'loan');

            TreasuryTransaction::where('reference_type', 'loan')
                ->where('reference_id', $loan->id)
                ->delete();

            $treasury = CashTreasury::find($loan->cash_treasury_id);
            if ($treasury) {
                $treasury->increment('current_balance', $loan->amount);
            }

            $loan->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('loans.index')->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }

        return redirect()->route('loans.index')->with('success', 'تم حذف السلفة بنجاح');
    }
}
