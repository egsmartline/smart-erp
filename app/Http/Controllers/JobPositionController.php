<?php

namespace App\Http\Controllers;

use App\Models\JobPosition;
use Illuminate\Http\Request;

class JobPositionController extends TenantAwareController
{
    public function index()
    {
        $positions = JobPosition::where('tenant_id', $this->getTenantId())
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('job-positions.index', compact('positions'));
    }

    public function create()
    {
        return view('job-positions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:job_positions,code',
            'description' => 'nullable|string|max:500',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $validated['is_active'] ?? true;

        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $maxId = JobPosition::withTrashed()->max('id') ?? 0;
            $validated['code'] = 'POS-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
        }

        JobPosition::create($validated);

        return redirect()->route('job-positions.index')->with('success', 'تم إنشاء الوظيفة بنجاح');
    }

    public function show(JobPosition $position)
    {
        if ($position->tenant_id !== $this->getTenantId()) abort(403);
        $position->load(['employees' => fn($q) => $q->limit(20)]);

        return view('job-positions.show', compact('position'));
    }

    public function edit(JobPosition $position)
    {
        if ($position->tenant_id !== $this->getTenantId()) abort(403);
        return view('job-positions.edit', compact('position'));
    }

    public function update(Request $request, JobPosition $position)
    {
        if ($position->tenant_id !== $this->getTenantId()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:job_positions,code,' . $position->id,
            'description' => 'nullable|string|max:500',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = $position->code ?? 'POS-' . str_pad($position->id, 4, '0', STR_PAD_LEFT);
        }

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
