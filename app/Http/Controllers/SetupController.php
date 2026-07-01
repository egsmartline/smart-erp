<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Company;
use App\Models\User;
use App\Models\Account;
use App\Models\Currency;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SetupController extends Controller
{
    public function index()
    {
        if (auth()->user()->tenant_id) {
            return redirect()->route('dashboard');
        }

        $currencies = Currency::pluck('name', 'id');
        return view('setup.index', compact('currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'company_phone' => 'required|string|max:50',
            'tax_number' => 'required|string|max:50',
            'currency_id' => 'required|exists:currencies,id',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6|confirmed',
        ]);

        DB::beginTransaction();

        try {
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'is_active' => true,
            ]);

            Company::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['company_name'],
                'address' => $validated['company_address'],
                'phone' => $validated['company_phone'],
                'tax_number' => $validated['tax_number'],
                'currency_id' => $validated['currency_id'],
            ]);

            $admin = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => 'admin',
                'is_active' => true,
            ]);

            $this->createDefaultAccounts($tenant->id, $validated['currency_id']);

            Auth::login($admin);
            session(['current_tenant_id' => $tenant->id]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'تم إعداد النظام بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء الإعداد: ' . $e->getMessage()]);
        }
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
                'code' => $account['code'],
                'name' => $account['name'],
                'type' => $account['type'],
                'currency_id' => $currencyId,
                'is_header' => $account['is_header'],
                'is_active' => true,
            ]);
        }
    }
}
