<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return new \Illuminate\Http\RedirectResponse(route('home'));
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return new \Illuminate\Http\RedirectResponse(route('select-company'));
        }

        return back()->withErrors([
            'name' => 'بيانات تسجيل الدخول غير صحيحة',
        ])->onlyInput('name');
    }

    public function showSelectCompany()
    {
        $user = auth()->user();

        $tenantIds = $user->getAccessibleTenants()->pluck('id');

        $companies = Company::whereIn('tenant_id', $tenantIds)
            ->where('is_active', true)
            ->get();

        return view('auth.select-company', compact('companies', 'user'));
    }

    public function switchCompany(Request $request, $companyId)
    {
        $user = auth()->user();
        $tenantIds = $user->getAccessibleTenants()->pluck('id');

        $company = Company::whereIn('tenant_id', $tenantIds)
            ->where('is_active', true)
            ->findOrFail($companyId);

        $user->update(['tenant_id' => $company->tenant_id]);

        session(['current_tenant_id' => $company->tenant_id]);
        session(['current_company_id' => $company->id]);

        return redirect()->route('dashboard')
            ->with('success', 'تم التبديل إلى ' . ($company->name ?? $company->name_en));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return new \Illuminate\Http\RedirectResponse('/login');
    }
}
