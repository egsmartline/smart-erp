<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\CashTreasury;
use App\Models\TreasuryTransaction;
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

        $lastNumber = Payroll::withTrashed()->max('payroll_number');
        $nextNumber = $lastNumber ? (int) substr($lastNumber, -4) + 1 : 1;
        $payrollNumber = 'PAY-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $existingPayroll = Payroll::where('tenant_id', $this->getTenantId())
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();
        if ($existingPayroll) {
            return back()->withErrors(['month' => 'كشف رواتب لهذا الشهر موجود بالفعل (#' . $existingPayroll->payroll_number . ')'])->withInput();
        }

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

    public function edit(Payroll $payroll)
    {
        if ($payroll->tenant_id !== $this->getTenantId()) abort(403);
        $payroll->load('payslips.employee');

        return view('payroll.edit', compact('payroll'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        if ($payroll->tenant_id !== $this->getTenantId()) abort(403);

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'payslips' => 'nullable|array',
            'payslips.*.id' => 'required|exists:payslip,id',
            'payslips.*.basic_salary' => 'nullable|numeric|min:0',
            'payslips.*.total_allowances' => 'nullable|numeric|min:0',
            'payslips.*.total_deductions' => 'nullable|numeric|min:0',
            'payslips.*.overtime_pay' => 'nullable|numeric|min:0',
        ]);

        $payroll->update([
            'notes' => $validated['notes'] ?? null,
        ]);

        if (isset($validated['payslips'])) {
            $totalBasic = 0;
            $totalAllowances = 0;
            $totalDeductions = 0;
            $totalNet = 0;

            foreach ($validated['payslips'] as $data) {
                $basic = $data['basic_salary'] ?? 0;
                $allowances = $data['total_allowances'] ?? 0;
                $deductions = $data['total_deductions'] ?? 0;
                $overtime = $data['overtime_pay'] ?? 0;
                $net = $basic + $allowances + $overtime - $deductions;

                Payslip::where('id', $data['id'])->update([
                    'basic_salary' => $basic,
                    'total_allowances' => $allowances,
                    'total_deductions' => $deductions,
                    'overtime_pay' => $overtime,
                    'net_salary' => max($net, 0),
                ]);

                $totalBasic += $basic;
                $totalAllowances += $allowances;
                $totalDeductions += $deductions;
                $totalNet += max($net, 0);
            }

            $payroll->update([
                'total_basic' => $totalBasic,
                'total_allowances' => $totalAllowances,
                'total_deductions' => $totalDeductions,
                'total_net' => $totalNet,
            ]);
        }

        $this->deductFromTreasury($payroll);

        return redirect()->route('payroll.show', $payroll)->with('success', 'تم تحديث كشف الرواتب بنجاح');
    }

    protected function deductFromTreasury(Payroll $payroll): void
    {
        if (TreasuryTransaction::where('tenant_id', $this->getTenantId())
            ->where('reference_type', 'payroll')
            ->where('reference_id', $payroll->id)
            ->exists()) {
            return;
        }

        $treasury = CashTreasury::where('tenant_id', $this->getTenantId())
            ->where('is_active', true)
            ->first();

        if (!$treasury) {
            return;
        }

        $prefix = 'PAY';
        $year = date('Y');
        $last = Payment::where('tenant_id', $this->getTenantId())
            ->withTrashed()
            ->where('payment_number', 'like', $prefix . '-' . $year . '-%')
            ->max('payment_number');
        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        $paymentNumber = $prefix . '-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        Payment::create([
            'tenant_id' => $this->getTenantId(),
            'payment_number' => $paymentNumber,
            'date' => Carbon::today(),
            'type' => 'payment',
            'treasury_id' => $treasury->id,
            'amount' => $payroll->total_net,
            'payment_method' => 'cash',
            'currency_id' => 1,
            'exchange_rate' => 1,
            'amount_in_currency' => $payroll->total_net,
            'reference' => $payroll->payroll_number,
            'notes' => 'مرتبات ' . $payroll->month . '/' . $payroll->year,
            'status' => 'completed',
            'user_id' => auth()->id(),
        ]);

        $treasury->decrement('current_balance', $payroll->total_net);

        TreasuryTransaction::create([
            'tenant_id' => $this->getTenantId(),
            'treasury_id' => $treasury->id,
            'type' => 'out',
            'amount' => $payroll->total_net,
            'date' => Carbon::today(),
            'reference_type' => 'payroll',
            'reference_id' => $payroll->id,
            'description' => 'مرتبات ' . $payroll->month . '/' . $payroll->year,
            'reference_number' => $payroll->payroll_number,
            'user_id' => auth()->id(),
        ]);
    }

    public function destroy(Payroll $payroll)
    {
        if ($payroll->tenant_id !== $this->getTenantId()) abort(403);

        $this->reverseTreasuryDeduction($payroll);

        $payroll->payslips()->delete();
        $payroll->delete();

        return redirect()->route('payroll.index')->with('success', 'تم حذف كشف الرواتب بنجاح');
    }

    protected function reverseTreasuryDeduction(Payroll $payroll): void
    {
        $payment = Payment::where('tenant_id', $this->getTenantId())
            ->where('reference', $payroll->payroll_number)
            ->first();

        if (!$payment) return;

        $treasury = CashTreasury::where('id', $payment->treasury_id)->first();
        if ($treasury) {
            $treasury->increment('current_balance', $payment->amount);
        }

        TreasuryTransaction::where('tenant_id', $this->getTenantId())
            ->where('reference_type', 'payroll')
            ->where('reference_id', $payroll->id)
            ->delete();

        $payment->delete();
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
        ]);

        $this->deductFromTreasury($payroll);

        return redirect()->route('payroll.show', $payroll)->with('success', 'تم تأكيد كشف الرواتب وخصمها من الخزينة بنجاح');
    }
}
