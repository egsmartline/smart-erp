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
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['is_current'] = false;

        if ($validated['is_current'] ?? false) {
            $this->tenantQuery(FiscalYear::class)->update(['is_current' => false]);
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
        ]);

        if ($validated['is_current'] ?? false) {
            $this->tenantQuery(FiscalYear::class)->update(['is_current' => false]);
        }

        $fiscalYear->update($validated);

        return redirect()->route('fiscal-years.index')->with('success', 'تم تحديث السنة المالية بنجاح');
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
