<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::where('tenant_id', Auth::user()->tenant_id)
            ->with('fiscalYear')
            ->orderByDesc('id')
            ->paginate(20);

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $fiscalYears = FiscalYear::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_closed', false)
            ->orderByDesc('id')
            ->get();

        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('budgets.create', compact('fiscalYears', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'notes' => 'nullable|string',
            'line_account_id' => 'nullable|array',
            'line_account_id.*' => 'exists:chart_of_accounts,id',
            'line_planned_amount' => 'nullable|array',
            'line_planned_amount.*' => 'numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $totalPlanned = 0;
            $linesData = [];

            if (!empty($validated['line_account_id'])) {
                foreach ($validated['line_account_id'] as $index => $accountId) {
                    if (!empty($validated['line_planned_amount'][$index])) {
                        $amount = $validated['line_planned_amount'][$index];
                        $totalPlanned += $amount;
                        $linesData[] = [
                            'tenant_id' => Auth::user()->tenant_id,
                            'account_id' => $accountId,
                            'planned_amount' => $amount,
                            'actual_amount' => 0,
                        ];
                    }
                }
            }

            $budget = Budget::create([
                'tenant_id' => Auth::user()->tenant_id,
                'name' => $validated['name'],
                'fiscal_year_id' => $validated['fiscal_year_id'],
                'date_from' => $validated['date_from'],
                'date_to' => $validated['date_to'],
                'state' => 'draft',
                'total_planned_amount' => $totalPlanned,
                'total_actual_amount' => 0,
                'user_id' => Auth::id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($linesData as $line) {
                $line['budget_id'] = $budget->id;
                BudgetLine::create($line);
            }

            return redirect()->route('budgets.index')
                ->with('success', 'تم إنشاء الميزانية بنجاح');
        });
    }

    public function show(Budget $budget)
    {
        $this->authorizeBudget($budget);

        $budget->load('fiscalYear', 'lines.account');

        $utilization = $budget->total_planned_amount > 0
            ? ($budget->total_actual_amount / $budget->total_planned_amount) * 100
            : 0;

        return view('budgets.show', compact('budget', 'utilization'));
    }

    public function edit(Budget $budget)
    {
        $this->authorizeBudget($budget);

        $budget->load('lines.account');

        $fiscalYears = FiscalYear::where('tenant_id', Auth::user()->tenant_id)
            ->orderByDesc('id')
            ->get();

        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('budgets.edit', compact('budget', 'fiscalYears', 'accounts'));
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorizeBudget($budget);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'notes' => 'nullable|string',
            'line_id' => 'nullable|array',
            'line_account_id' => 'nullable|array',
            'line_account_id.*' => 'exists:chart_of_accounts,id',
            'line_planned_amount' => 'nullable|array',
            'line_planned_amount.*' => 'numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $budget, $request) {
            $totalPlanned = 0;

            $budget->lines()->delete();

            if (!empty($validated['line_account_id'])) {
                foreach ($validated['line_account_id'] as $index => $accountId) {
                    if (!empty($validated['line_planned_amount'][$index])) {
                        $amount = $validated['line_planned_amount'][$index];
                        $totalPlanned += $amount;
                        BudgetLine::create([
                            'tenant_id' => Auth::user()->tenant_id,
                            'budget_id' => $budget->id,
                            'account_id' => $accountId,
                            'planned_amount' => $amount,
                            'actual_amount' => 0,
                        ]);
                    }
                }
            }

            $budget->update([
                'name' => $validated['name'],
                'fiscal_year_id' => $validated['fiscal_year_id'],
                'date_from' => $validated['date_from'],
                'date_to' => $validated['date_to'],
                'total_planned_amount' => $totalPlanned,
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('budgets.index')
                ->with('success', 'تم تحديث الميزانية بنجاح');
        });
    }

    public function confirm(Budget $budget)
    {
        $this->authorizeBudget($budget);

        if ($budget->state !== 'draft') {
            return redirect()->back()
                ->with('error', 'يمكن تأكيد الميزانيات في حالة مسودة فقط');
        }

        $budget->update(['state' => 'confirmed']);

        return redirect()->route('budgets.index')
            ->with('success', 'تم تأكيد الميزانية بنجاح');
    }

    public function cancel(Budget $budget)
    {
        $this->authorizeBudget($budget);

        if ($budget->state !== 'draft') {
            return redirect()->back()
                ->with('error', 'يمكن إلغاء الميزانيات في حالة مسودة فقط');
        }

        $budget->update(['state' => 'cancelled']);

        return redirect()->route('budgets.index')
            ->with('success', 'تم إلغاء الميزانية بنجاح');
    }

    public function destroy(Budget $budget)
    {
        $this->authorizeBudget($budget);

        if ($budget->state !== 'draft') {
            return redirect()->back()
                ->with('error', 'يمكن حذف الميزانيات في حالة مسودة فقط');
        }

        $budget->lines()->delete();
        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'تم حذف الميزانية بنجاح');
    }

    private function authorizeBudget(Budget $budget): void
    {
        if ($budget->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'غير مصرح לך بالوصول لهذه الميزانية');
        }
    }
}
