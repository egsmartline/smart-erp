<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

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

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'name' => 'بيانات تسجيل الدخول غير صحيحة',
            ])->onlyInput('name');
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            Auth::logout();

            $request->session()->put('2fa.pending_id', $user->id);
            $request->session()->put('2fa.remember', $remember);
            $request->session()->regenerate();

            return new \Illuminate\Http\RedirectResponse(route('two-factor.login'));
        }

        // المصادقة الثنائية إلزامية: المستخدم يبقى مسجلاً لكن لا يصل لأي صفحة
        // قبل إتمام التفعيل من صفحة الأمان (يتكفل بذلك EnsureTwoFactor).
        $request->session()->regenerate();

        return new \Illuminate\Http\RedirectResponse(route('select-company'));
    }

    public function showTwoFactorChallenge(Request $request)
    {
        if (!$request->session()->has('2fa.pending_id')) {
            return new \Illuminate\Http\RedirectResponse(route('login'));
        }

        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20',
        ]);

        $pendingId = $request->session()->get('2fa.pending_id');
        $user = $pendingId ? User::find($pendingId) : null;

        if (!$user || !$user->hasTwoFactorEnabled()) {
            $request->session()->forget(['2fa.pending_id', '2fa.remember']);

            return redirect()->route('login')->withErrors([
                'name' => 'انتهت جلسة الدخول، أعد تسجيل الدخول.',
            ]);
        }

        $throttleKey = '2fa:' . $user->id . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 6)) {
            throw ValidationException::withMessages([
                'code' => 'محاولات كثيرة جداً، انتظر ' . RateLimiter::availableIn($throttleKey) . ' ثانية ثم حاول مجدداً.',
            ]);
        }

        if (!$this->validTwoFactorCode($user, (string) $request->input('code'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'code' => 'رمز التحقق غير صحيح.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $remember = (bool) $request->session()->pull('2fa.remember', false);
        $request->session()->forget('2fa.pending_id');
        $request->session()->regenerate();

        Auth::login($user, $remember);
        $request->session()->put('2fa.verified', $user->id);

        return new \Illuminate\Http\RedirectResponse(route('select-company'));
    }

    /**
     * Accept either a TOTP code or a one-time recovery code.
     */
    private function validTwoFactorCode(User $user, string $code): bool
    {
        $code = preg_replace('/\s+/', '', trim($code)) ?? '';

        if (preg_match('/^\d{6}$/', $code) && Totp::verify((string) $user->two_factor_secret, $code)) {
            return true;
        }

        $normalized = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code) ?? '');
        if ($normalized === '') {
            return false;
        }

        $codes = $user->twoFactorRecoveryCodes();

        foreach ($codes as $index => $hash) {
            if (Hash::check($normalized, $hash)) {
                unset($codes[$index]);
                $user->forceFill([
                    'two_factor_recovery_codes' => json_encode(array_values($codes)),
                ])->save();

                return true;
            }
        }

        return false;
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
