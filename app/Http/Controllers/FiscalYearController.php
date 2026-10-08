<?php

namespace App\Http\Controllers;

use App\Models\FiscalYear;
use Illuminate\Http\Request;

class FiscalYearController extends TenantAwareController
{
    public function index()
    {
        $fiscalYears = $this->tenantQuery(FiscalYear::class)->orderByDesc('start_date')->get();
        return view('fiscal-years.index', compact('fiscalYears'));
    }

    public function create()
    {
        return view('fiscal-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_active']) {
            $this->tenantQuery(FiscalYear::class)->update(['is_active' => false]);
        }

        FiscalYear::create($validated);

        return redirect()->route('fiscal-years.index')->with('success', 'تم إنشاء السنة المالية بنجاح');
    }

    public function show(FiscalYear $fiscalYear)
    {
        $this->authorizeTenant($fiscalYear);
        return view('fiscal-years.show', compact('fiscalYear'));
    }

    public function edit(FiscalYear $fiscalYear)
    {
        $this->authorizeTenant($fiscalYear);
        return view('fiscal-years.edit', compact('fiscalYear'));
    }

    public function update(Request $request, FiscalYear $fiscalYear)
    {
        $this->authorizeTenant($fiscalYear);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_active']) {
            $this->tenantQuery(FiscalYear::class)
                ->where('id', '!=', $fiscalYear->id)
                ->update(['is_active' => false]);
        }

        $fiscalYear->update($validated);

        return redirect()->route('fiscal-years.index')->with('success', 'تم تحديث السنة المالية بنجاح');
    }

    public function close(FiscalYear $fiscalYear)
    {
        $this->authorizeTenant($fiscalYear);

        if ($fiscalYear->is_closed) {
            return redirect()->route('fiscal-years.index')->with('error', 'السنة المالية مقفولة بالفعل');
        }

        $fiscalYear->update([
            'is_closed' => true,
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);

        return redirect()->route('fiscal-years.index')->with('success', 'تم إقفال السنة المالية بنجاح');
    }

    public function reopen(FiscalYear $fiscalYear)
    {
        $this->authorizeTenant($fiscalYear);

        $fiscalYear->update([
            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return redirect()->route('fiscal-years.index')->with('success', 'تم إعادة فتح السنة المالية');
    }

    public function destroy(FiscalYear $fiscalYear)
    {
        $this->authorizeTenant($fiscalYear);
        $fiscalYear->delete();
        return redirect()->route('fiscal-years.index')->with('success', 'تم حذف السنة المالية بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
