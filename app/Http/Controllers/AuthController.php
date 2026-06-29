<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return new \Illuminate\Http\RedirectResponse('/');
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
        $tenants = $user->getAccessibleTenants();

        if ($tenants->count() === 0) {
            if ($user->tenant_id) {
                session(['current_tenant_id' => $user->tenant_id]);
                return new \Illuminate\Http\RedirectResponse('/');
            }
            return new \Illuminate\Http\RedirectResponse(route('setup.index'));
        }

        if ($tenants->count() === 1) {
            session(['current_tenant_id' => $tenants->first()->id]);
            return new \Illuminate\Http\RedirectResponse('/');
        }

        return view('auth.select-company', compact('tenants'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return new \Illuminate\Http\RedirectResponse('/login');
    }
}
