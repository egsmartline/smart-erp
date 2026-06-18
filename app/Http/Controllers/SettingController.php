<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Currency;
use Illuminate\Http\Request;

class SettingController extends TenantAwareController
{
    public function index()
    {
        $company = $this->tenantQuery(Company::class)->first();
        $currencies = Currency::where('tenant_id', $this->getTenantId())->get();
        $companies = $this->tenantQuery(Company::class)->get();

        return view('settings.index', compact('company', 'currencies', 'companies'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'currency_id' => 'required|exists:currencies,id',
            'secondary_currency_id' => 'nullable|exists:currencies,id',
        ]);

        $company = $this->tenantQuery(Company::class)->first();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($validated);

        return redirect()->route('settings.index')->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|max:2048',
        ]);

        $company = $this->tenantQuery(Company::class)->first();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $company->update(['logo' => $path]);
        }

        return back()->with('success', 'تم تحديث شعار الشركة بنجاح');
    }
}
