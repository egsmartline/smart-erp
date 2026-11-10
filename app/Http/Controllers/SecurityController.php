<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SecurityController extends Controller
{
    private const RECOVERY_CODE_COUNT = 8;

    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $setupSecret = $request->session()->get('2fa.setup_secret');
        $recoveryCodes = $request->session()->pull('2fa.recovery_codes');

        return view('security.index', [
            'user' => $user,
            'enabled' => $user->hasTwoFactorEnabled(),
            'setupSecret' => $setupSecret,
            'provisioningUri' => $setupSecret
                ? Totp::provisioningUri($setupSecret, $user->email ?: $user->name)
                : null,
            'recoveryCodes' => $recoveryCodes,
            'remainingRecoveryCodes' => count($user->twoFactorRecoveryCodes()),
        ]);
    }

    public function enable(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('security.index');
        }

        $request->session()->put('2fa.setup_secret', Totp::generateSecret());
        $request->session()->forget('2fa.recovery_codes');

        return redirect()->route('security.index')
            ->with('success', 'امسح رمز QR بتطبيق المصادقة ثم أدخل الرمز المكوّن من 6 أرقام للتأكيد.');
    }

    public function cancelSetup(Request $request)
    {
        $request->session()->forget('2fa.setup_secret');

        return redirect()->route('security.index');
    }

    public function confirm(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'code' => 'required|string|max:10',
        ]);

        $secret = $request->session()->get('2fa.setup_secret');

        if (!$secret) {
            return redirect()->route('security.index')
                ->withErrors(['code' => 'انتهت صلاحية جلسة التفعيل، ابدأ من جديد.']);
        }

        if (!Totp::verify($secret, $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'الرمز غير صحيح. تأكد من ضبط ساعة الهاتف ثم أدخل الرمز الحالي.',
            ]);
        }

        $codes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($codes['hashed']),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('2fa.setup_secret');
        $request->session()->put('2fa.recovery_codes', $codes['plain']);
        $request->session()->put('2fa.verified', $user->id);

        return redirect()->route('security.index')
            ->with('success', 'تم تفعيل المصادقة الثنائية بنجاح. احفظ رموز الاسترداد الآن.');
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'password' => 'required|string',
            'code' => 'required|string|max:10',
        ]);

        if (!Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'كلمة المرور غير صحيحة.',
            ]);
        }

        if (!Totp::verify((string) $user->two_factor_secret, $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'رمز التحقق غير صحيح.',
            ]);
        }

        $codes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => json_encode($codes['hashed']),
        ])->save();

        $request->session()->put('2fa.recovery_codes', $codes['plain']);

        return redirect()->route('security.index')
            ->with('success', 'تم توليد رموز استرداد جديدة. الرموز القديمة لم تعد صالحة.');
    }

    /**
     * Unlink the current authenticator app and start a fresh setup.
     */
    public function resetDevice(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'password' => 'required|string',
            'code' => 'required|string|max:10',
        ]);

        if (!Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'كلمة المرور غير صحيحة.',
            ]);
        }

        if (!Totp::verify((string) $user->two_factor_secret, $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'رمز التحقق غير صحيح.',
            ]);
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $request->session()->forget(['2fa.recovery_codes']);

        return redirect()->route('security.index')
            ->with('success', 'تم فصل التطبيق السابق. ابدأ تفعيلاً جديداً بمسح رمز QR.');
    }

    /**
     * @return array{plain: array<int, string>, hashed: array<int, string>}
     */
    private function generateRecoveryCodes(): array
    {
        $plain = [];
        $hashed = [];
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        for ($i = 0; $i < self::RECOVERY_CODE_COUNT; $i++) {
            $raw = '';
            for ($j = 0; $j < 10; $j++) {
                $raw .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }

            $hashed[] = Hash::make($raw);
            $plain[] = substr($raw, 0, 5) . '-' . substr($raw, 5);
        }

        return ['plain' => $plain, 'hashed' => $hashed];
    }
}
