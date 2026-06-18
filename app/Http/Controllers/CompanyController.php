<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Currency;
use Illuminate\Http\Request;

class CompanyController extends TenantAwareController
{
    public function index()
    {
        $companies = $this->tenantQuery(Company::class)->with('secondaryCurrency')->get();
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        $currencies = Currency::where('tenant_id', $this->getTenantId())->where('is_active', true)->get();
        return view('companies.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'currency_code' => 'nullable|string|max:10',
            'secondary_currency_id' => 'nullable|exists:currencies,id',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $this->getTenantId();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        Company::create($validated);

        return redirect()->route('companies.index')->with('success', 'تم إنشاء الشركة بنجاح');
    }

    public function show(Company $company)
    {
        $company->load('secondaryCurrency');
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        $currencies = Currency::where('tenant_id', $this->getTenantId())->where('is_active', true)->get();
        return view('companies.edit', compact('company', 'currencies'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'currency_code' => 'nullable|string|max:10',
            'secondary_currency_id' => 'nullable|exists:currencies,id',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        $company->update($validated);

        return redirect()->route('companies.index')->with('success', 'تم تحديث الشركة بنجاح');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'تم حذف الشركة بنجاح');
    }
}
