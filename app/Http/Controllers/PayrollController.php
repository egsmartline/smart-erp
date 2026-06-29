<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\Employee;
use App\Models\Loan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends TenantAwareController
{
    public function index()
    {
        $payrolls = Payroll::where('tenant_id', $this->getTenantId())
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
        $totalAllowances = 0;
        $totalDeductions = 0;
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
            $loanDeduction = Loan::where('employee_id', $employee->id)
                ->where('status', 'active')
                ->where('remaining', '>', 0)
                ->sum('monthly_deduction');
            $net = $basic - $loanDeduction;
            $payslip = Payslip::create([
                'tenant_id' => $this->getTenantId(),
                'payroll_id' => $payroll->id,
                'employee_id' => $employee->id,
                'basic_salary' => $basic,
                'allowances' => null,
                'total_allowances' => 0,
                'deductions' => $loanDeduction > 0 ? [['type' => 'loan', 'amount' => $loanDeduction]] : null,
                'total_deductions' => $loanDeduction,
                'overtime_pay' => 0,
                'net_salary' => $net,
            ]);
            $totalBasic += $basic;
            $totalDeductions += $loanDeduction;
            $totalNet += $net;
        }

        $payroll->update([
            'total_basic' => $totalBasic,
            'total_deductions' => $totalDeductions,
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
        $payroll->load('payslips');

        foreach ($payroll->payslips as $payslip) {
            $loanDeduction = Loan::where('employee_id', $payslip->employee_id)
                ->where('status', 'active')
                ->where('remaining', '>', 0)
                ->sum('monthly_deduction');

            if ($loanDeduction > 0) {
                $loans = Loan::where('employee_id', $payslip->employee_id)
                    ->where('status', 'active')
                    ->where('remaining', '>', 0)
                    ->get();

                foreach ($loans as $loan) {
                    $actualDeduction = min($loan->monthly_deduction, $loan->remaining);
                    $loan->increment('total_paid', $actualDeduction);
                    $loan->decrement('remaining', $actualDeduction);
                    if ($loan->remaining <= 0) {
                        $loan->update(['status' => 'completed']);
                    }
                }
            }
        }

        $payroll->update([
            'state' => 'confirmed',
            'confirmed_at' => Carbon::now(),
        ]);

        return redirect()->route('payroll.show', $payroll)->with('success', 'تم تأكيد كشف الرواتب بنجاح');
    }
}
