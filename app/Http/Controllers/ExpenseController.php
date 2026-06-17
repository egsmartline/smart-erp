<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = Expense::where('tenant_id', $this->getTenantId())
            ->with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $expenses = $query->latest('expense_date')->paginate(20)->withQueryString();
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();

        return view('expenses.index', compact('expenses', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();

        return view('expenses.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'category' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['status'] = 'pending';

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'تم تسجيل المصروف بنجاح');
    }

    public function show(Expense $expense)
    {
        if ($expense->tenant_id !== $this->getTenantId()) abort(403);
        $expense->load(['employee', 'approver']);

        return view('expenses.show', compact('expense'));
    }

    public function approve(Expense $expense)
    {
        if ($expense->tenant_id !== $this->getTenantId()) abort(403);
        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('expenses.show', $expense)->with('success', 'تم الموافقة على المصروف');
    }

    public function reject(Expense $expense)
    {
        if ($expense->tenant_id !== $this->getTenantId()) abort(403);
        $expense->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('expenses.show', $expense)->with('success', 'تم رفض المصروف');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->tenant_id !== $this->getTenantId()) abort(403);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'تم حذف المصروف بنجاح');
    }
}
