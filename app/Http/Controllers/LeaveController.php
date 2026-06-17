<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = Leave::where('tenant_id', $this->getTenantId())
            ->with(['employee', 'approver']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->latest('start_date')->paginate(20)->withQueryString();
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();

        return view('leaves.index', compact('leaves', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();

        return view('leaves.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:annual,sick,maternity,paternity,unpaid,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        $days = Carbon::parse($validated['start_date'])->diffInDays(Carbon::parse($validated['end_date'])) + 1;

        $validated['tenant_id'] = $this->getTenantId();
        $validated['days'] = $days;
        $validated['status'] = 'pending';

        Leave::create($validated);

        return redirect()->route('leaves.index')->with('success', 'تم تقديم طلب الإجازة بنجاح');
    }

    public function approve(Leave $leave)
    {
        if ($leave->tenant_id !== $this->getTenantId()) abort(403);
        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('leaves.index')->with('success', 'تم الموافقة على الإجازة');
    }

    public function reject(Leave $leave)
    {
        if ($leave->tenant_id !== $this->getTenantId()) abort(403);
        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('leaves.index')->with('success', 'تم رفض الإجازة');
    }
}
