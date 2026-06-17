<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends TenantAwareController
{
    public function index()
    {
        $departments = Department::where('tenant_id', $this->getTenantId())
            ->with(['manager', 'parent'])
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $parentDepartments = Department::where('tenant_id', $this->getTenantId())->active()->orderBy('name')->get();

        return view('departments.create', compact('parentDepartments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $validated['is_active'] ?? true;

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'تم إنشاء القسم بنجاح');
    }

    public function show(Department $department)
    {
        if ($department->tenant_id !== $this->getTenantId()) abort(403);
        $department->load(['manager', 'parent', 'children', 'employees' => fn($q) => $q->limit(20)]);

        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        if ($department->tenant_id !== $this->getTenantId()) abort(403);
        $parentDepartments = Department::where('tenant_id', $this->getTenantId())
            ->where('id', '!=', $department->id)
            ->active()
            ->orderBy('name')
            ->get();

        return view('departments.edit', compact('department', 'parentDepartments'));
    }

    public function update(Request $request, Department $department)
    {
        if ($department->tenant_id !== $this->getTenantId()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
        ]);

        $department->update($validated);

        return redirect()->route('departments.show', $department)->with('success', 'تم تحديث القسم بنجاح');
    }

    public function destroy(Department $department)
    {
        if ($department->tenant_id !== $this->getTenantId()) abort(403);
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'تم حذف القسم بنجاح');
    }
}
