<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\JobPosition;
use Illuminate\Http\Request;

class EmployeeController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = Employee::where('tenant_id', $this->getTenantId())
            ->with(['department', 'jobPosition']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('is_active')) {
            $status = $request->boolean('is_active') ? 'active' : 'inactive';
            $query->where('employment_status', $status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest('hire_date')->paginate(20)->withQueryString();
        $departments = Department::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();
        $jobPositions = JobPosition::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('employees.index', compact('employees', 'departments', 'jobPositions'));
    }

    public function create()
    {
        $departments = Department::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();
        $jobPositions = JobPosition::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('employees.create', compact('departments', 'jobPositions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:male,female',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'job_position_id' => 'required|exists:job_positions,id',
            'hire_date' => 'required|date',
            'contract_end_date' => 'nullable|date',
            'gross_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $lastEmp = Employee::where('tenant_id', $this->getTenantId())->latest('id')->first();
        $nextNumber = $lastEmp ? (int) substr($lastEmp->employee_id, -4) + 1 : 1;
        $validated['employee_id'] = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $validated['tenant_id'] = $this->getTenantId();
        $validated['employment_status'] = 'active';
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'تم إنشاء الموظف بنجاح');
    }

    public function show(Employee $employee)
    {
        if ($employee->tenant_id !== $this->getTenantId()) abort(403);
        $employee->load(['department', 'jobPosition', 'attendances' => fn($q) => $q->latest('date')->limit(30),
            'leaves' => fn($q) => $q->latest('date_from')->limit(20),
            'payslips' => fn($q) => $q->latest()->limit(12),
            'loans' => fn($q) => $q->latest()->limit(10)]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        if ($employee->tenant_id !== $this->getTenantId()) abort(403);
        $departments = Department::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();
        $jobPositions = JobPosition::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('employees.edit', compact('employee', 'departments', 'jobPositions'));
    }

    public function update(Request $request, Employee $employee)
    {
        if ($employee->tenant_id !== $this->getTenantId()) abort(403);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:male,female',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'job_position_id' => 'required|exists:job_positions,id',
            'hire_date' => 'required|date',
            'contract_end_date' => 'nullable|date',
            'employment_status' => 'required|in:active,inactive,terminated,on_leave',
            'gross_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $employee->update($validated);

        return redirect()->route('employees.show', $employee)->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->tenant_id !== $this->getTenantId()) abort(403);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف بنجاح');
    }
}
