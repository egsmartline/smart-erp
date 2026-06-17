<?php

namespace App\Http\Controllers;

use App\Models\JobPosition;
use App\Models\Department;
use Illuminate\Http\Request;

class JobPositionController extends TenantAwareController
{
    public function index()
    {
        $positions = JobPosition::where('tenant_id', $this->getTenantId())
            ->with('department')
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('job-positions.index', compact('positions'));
    }

    public function create()
    {
        $departments = Department::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('job-positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string|max:500',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $validated['is_active'] ?? true;

        JobPosition::create($validated);

        return redirect()->route('job-positions.index')->with('success', 'تم إنشاء الوظيفة بنجاح');
    }

    public function show(JobPosition $position)
    {
        if ($position->tenant_id !== $this->getTenantId()) abort(403);
        $position->load(['department', 'employees' => fn($q) => $q->limit(20)]);

        return view('job-positions.show', compact('position'));
    }

    public function edit(JobPosition $position)
    {
        if ($position->tenant_id !== $this->getTenantId()) abort(403);
        $departments = Department::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('job-positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, JobPosition $position)
    {
        if ($position->tenant_id !== $this->getTenantId()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string|max:500',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $position->update($validated);

        return redirect()->route('job-positions.show', $position)->with('success', 'تم تحديث الوظيفة بنجاح');
    }

    public function destroy(JobPosition $position)
    {
        if ($position->tenant_id !== $this->getTenantId()) abort(403);
        $position->delete();

        return redirect()->route('job-positions.index')->with('success', 'تم حذف الوظيفة بنجاح');
    }
}
