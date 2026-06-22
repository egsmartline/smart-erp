<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Employee;
use Illuminate\Http\Request;

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

        return view('loans.create', compact('employees'));
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
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['status'] = 'active';
        $validated['remaining'] = $validated['amount'];

        $validated['loan_number'] = 'L-' . str_pad(Loan::withTrashed()->max('id') + 1, 5, '0', STR_PAD_LEFT);

        Loan::create($validated);

        return redirect()->route('loans.index')->with('success', 'تم إنشاء السلفة بنجاح');
    }

    public function show(Loan $loan)
    {
        if ($loan->tenant_id !== $this->getTenantId()) abort(403);
        $loan->load('employee');

        return view('loans.show', compact('loan'));
    }

    public function destroy(Loan $loan)
    {
        if ($loan->tenant_id !== $this->getTenantId()) abort(403);
        $loan->delete();

        return redirect()->route('loans.index')->with('success', 'تم حذف السلفة بنجاح');
    }
}
