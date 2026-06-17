<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = Attendance::where('tenant_id', $this->getTenantId())
            ->with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', Carbon::today());
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();

        return view('attendance.index', compact('attendances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('tenant_id', $this->getTenantId())->active()->orderBy('first_name')->get();

        return view('attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'nullable|in:present,absent,late,excused',
            'notes' => 'nullable|string|max:500',
        ]);

        $date = $validated['date'];
        $checkIn = isset($validated['check_in']) ? Carbon::parse($date . ' ' . $validated['check_in']) : null;
        $checkOut = isset($validated['check_out']) ? Carbon::parse($date . ' ' . $validated['check_out']) : null;

        $workHours = null;
        if ($checkIn && $checkOut) {
            $workHours = round($checkIn->floatDiffInHours($checkOut), 2);
        }

        Attendance::create([
            'tenant_id' => $this->getTenantId(),
            'employee_id' => $validated['employee_id'],
            'date' => $date,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'work_hours' => $workHours,
            'status' => $validated['status'] ?? 'present',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('attendance.index')->with('success', 'تم تسجيل الحضور بنجاح');
    }

    public function checkOut(Attendance $att)
    {
        if ($att->tenant_id !== $this->getTenantId()) abort(403);

        $now = Carbon::now();
        $att->check_out = $now;
        $att->work_hours = round($att->check_in->floatDiffInHours($now), 2);
        $att->save();

        return redirect()->route('attendance.index')->with('success', 'تم تسجيل الانصراف بنجاح');
    }

    public function show(Employee $employee)
    {
        if ($employee->tenant_id !== $this->getTenantId()) abort(403);

        $attendances = Attendance::where('tenant_id', $this->getTenantId())
            ->where('employee_id', $employee->id)
            ->latest('date')
            ->paginate(30);

        return view('attendance.show', compact('employee', 'attendances'));
    }
}
