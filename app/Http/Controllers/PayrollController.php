<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends TenantAwareController
{
    public function index()
    {
        $payrolls = Payroll::where('tenant_id', $this->getTenantId())
            ->with('creator')
            ->latest()
            ->paginate(20);

        return view('payroll.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();

        return view('payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'notes' => 'nullable|string',
        ]);

        $lastPayroll = Payroll::where('tenant_id', $this->getTenantId())->latest('id')->first();
        $nextNumber = $lastPayroll ? (int) substr($lastPayroll->reference, -4) + 1 : 1;
        $reference = 'PAY-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->get();
        $totalAmount = 0;

        $payroll = Payroll::create([
            'tenant_id' => $this->getTenantId(),
            'reference' => $reference,
            'month' => $validated['month'],
            'year' => $validated['year'],
            'state' => 'draft',
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
            'total_amount' => 0,
        ]);

        foreach ($employees as $employee) {
            $payslip = Payslip::create([
                'tenant_id' => $this->getTenantId(),
                'payroll_id' => $payroll->id,
                'employee_id' => $employee->id,
                'basic_salary' => $employee->basic_salary,
                'allowances' => 0,
                'deductions' => 0,
                'net_salary' => $employee->basic_salary,
            ]);
            $totalAmount += $payslip->net_salary;
        }

        $payroll->update(['total_amount' => $totalAmount]);

        return redirect()->route('payroll.show', $payroll)->with('success', 'تم إنشاء كشف الرواتب بنجاح');
    }

    public function show(Payroll $payroll)
    {
        if ($payroll->tenant_id !== $this->getTenantId()) abort(403);
        $payroll->load(['creator', 'payslips.employee']);

        return view('payroll.show', compact('payroll'));
    }

    public function confirm(Payroll $payroll)
    {
        if ($payroll->tenant_id !== $this->getTenantId()) abort(403);
        $payroll->update([
            'state' => 'confirmed',
            'confirmed_at' => Carbon::now(),
        ]);

        return redirect()->route('payroll.show', $payroll)->with('success', 'تم تأكيد كشف الرواتب بنجاح');
    }
}
