<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Tenant;
use App\Models\Currency;
use App\Models\Account;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends TenantAwareController
{
    public function index()
    {
        $tenants = auth()->user()->getAccessibleTenants();
        $companies = Company::whereIn('tenant_id', $tenants->pluck('id'))->with('secondaryCurrency')->get();
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

        DB::beginTransaction();

        try {
            $tenant = Tenant::create([
                'name' => $validated['name'],
                'slug' => 'company-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $validated['name'])) . '-' . uniqid(),
                'is_active' => true,
            ]);

            $currency = Currency::where('is_active', true)->first();
            $currencyId = $currency?->id;

            if ($validated['currency_code'] ?? null) {
                $dbCurrency = Currency::where('code', $validated['currency_code'])->first();
                if ($dbCurrency) {
                    $currencyId = $dbCurrency->id;
                }
            }

            $currencyId ??= 1;

            $companyData = array_merge($validated, ['tenant_id' => $tenant->id]);

            if ($request->hasFile('logo')) {
                $companyData['logo'] = $request->file('logo')->store('logos', 'public');
            }

            if (!isset($companyData['is_active'])) {
                $companyData['is_active'] = true;
            }

            unset($companyData['secondary_currency_id']);
            if ($validated['secondary_currency_id'] ?? null) {
                $companyData['secondary_currency_id'] = $validated['secondary_currency_id'];
            }

            Company::create($companyData);

            $this->createDefaultAccounts($tenant->id, $currencyId);

            FiscalYear::create([
                'tenant_id' => $tenant->id,
                'name' => date('Y'),
                'start_date' => date('Y-01-01'),
                'end_date' => date('Y-12-31'),
                'is_current' => true,
            ]);

            $user = auth()->user();
            $user->tenants()->attach($tenant->id, ['role' => $user->role]);

            session(['current_tenant_id' => $tenant->id]);
            session(['current_company_id' => Company::where('tenant_id', $tenant->id)->first()->id]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'تم إنشاء الشركة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء إنشاء الشركة: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Company $company)
    {
        $company->load('secondaryCurrency');
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        $currencies = Currency::where('tenant_id', $company->tenant_id)->where('is_active', true)->get();
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

    private function createDefaultAccounts($tenantId, $currencyId)
    {
        $accounts = [
            ['code' => '1000', 'name' => 'الصندوق', 'type' => 'asset', 'is_header' => false, 'parent_code' => null],
            ['code' => '1100', 'name' => 'البنوك', 'type' => 'asset', 'is_header' => false, 'parent_code' => null],
            ['code' => '1200', 'name' => 'العملاء', 'type' => 'asset', 'is_header' => false, 'parent_code' => null],
            ['code' => '1300', 'name' => 'المخزون', 'type' => 'asset', 'is_header' => false, 'parent_code' => null],
            ['code' => '2000', 'name' => 'الموردون', 'type' => 'liability', 'is_header' => false, 'parent_code' => null],
            ['code' => '2100', 'name' => 'ضريبة القيمة المضافة', 'type' => 'liability', 'is_header' => false, 'parent_code' => null],
            ['code' => '3000', 'name' => 'رأس المال', 'type' => 'equity', 'is_header' => false, 'parent_code' => null],
            ['code' => '4000', 'name' => 'إيرادات المبيعات', 'type' => 'revenue', 'is_header' => false, 'parent_code' => null],
            ['code' => '5000', 'name' => 'تكلفة البضاعة المباعة', 'type' => 'expense', 'is_header' => false, 'parent_code' => null],
            ['code' => '6000', 'name' => 'المصروفات', 'type' => 'expense', 'is_header' => false, 'parent_code' => null],
        ];

        foreach ($accounts as $account) {
            Account::create([
                'tenant_id' => $tenantId,
                'name' => $account['name'],
                'code' => $account['code'],
                'type' => $account['type'],
                'currency_id' => $currencyId,
                'is_header' => $account['is_header'],
                'is_active' => true,
            ]);
        }
    }
}
