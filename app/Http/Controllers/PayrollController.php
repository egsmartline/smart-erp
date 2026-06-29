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
        $nextNumber = $lastPayroll ? (int) substr($lastPayroll->payroll_number, -4) + 1 : 1;
        $payrollNumber = 'PAY-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->get();
        $totalBasic = 0;
        $totalNet = 0;

        $dateFrom = Carbon::create($validated['year'], $validated['month'], 1);
        $dateTo = $dateFrom->copy()->lastOfMonth();

        $payroll = Payroll::create([
            'tenant_id' => $this->getTenantId(),
            'payroll_number' => $payrollNumber,
            'month' => $validated['month'],
            'year' => $validated['year'],
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'state' => 'draft',
            'total_basic' => 0,
            'total_allowances' => 0,
            'total_deductions' => 0,
            'total_net' => 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($employees as $employee) {
            $basic = $employee->gross_salary ?? 0;
            $payslip = Payslip::create([
                'tenant_id' => $this->getTenantId(),
                'payroll_id' => $payroll->id,
                'employee_id' => $employee->id,
                'basic_salary' => $basic,
                'allowances' => null,
                'total_allowances' => 0,
                'deductions' => null,
                'total_deductions' => 0,
                'overtime_pay' => 0,
                'net_salary' => $basic,
            ]);
            $totalBasic += $basic;
            $totalNet += $payslip->net_salary;
        }

        $payroll->update([
            'total_basic' => $totalBasic,
            'total_net' => $totalNet,
        ]);

        return redirect()->route('payroll.show', $payroll)->with('success', 'تم إنشاء كشف الرواتب بنجاح');
    }

    public function show(Payroll $payroll)
    {
        if ($payroll->tenant_id !== $this->getTenantId()) abort(403);
        $payroll->load(['payslips.employee']);

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
