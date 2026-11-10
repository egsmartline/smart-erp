@extends('layouts.auth')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4" dir="rtl">
    <div class="mx-auto w-full max-w-3xl space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-white p-5 shadow-sm border border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-100 text-lg font-bold text-primary-700">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">أمان الحساب</h1>
                    <p class="text-sm text-gray-500">{{ $user->name }} — {{ $user->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    تسجيل الخروج
                </button>
            </form>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Recovery codes (shown once) --}}
        @if($recoveryCodes)
            <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-5">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-amber-900">رموز الاسترداد — احفظها الآن</h2>
                    <button type="button" id="copy-codes" data-codes="{{ implode("\n", $recoveryCodes) }}"
                        class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-amber-700 transition">
                        نسخ الكل
                    </button>
                </div>
                <p class="mb-4 text-xs leading-6 text-amber-800">
                    كل رمز يُستخدم مرة واحدة فقط عند فقدان الهاتف. اطبعها أو احفظها في مكان آمن — <strong>لن تظهر مرة أخرى</strong>.
                </p>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                    @foreach($recoveryCodes as $code)
                        <code class="rounded-lg border border-amber-300 bg-white px-2 py-2 text-center font-mono text-sm text-gray-800" dir="ltr">{{ $code }}</code>
                    @endforeach
                </div>
            </div>
        @endif

        @if($enabled)
            {{-- Enabled --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-200">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-base font-bold text-gray-800">المصادقة الثنائية مفعّلة</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            يُطلب رمز من تطبيق المصادقة عند كل تسجيل دخول.
                            رموز الاسترداد المتاحة: <strong class="text-gray-800">{{ $remainingRecoveryCodes }}</strong> من 8.
                        </p>
                        @if($remainingRecoveryCodes <= 2)
                            <p class="mt-2 text-xs font-semibold text-amber-700">عدد رموز الاسترداد منخفض — يُنصح بتوليد رموز جديدة.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                {{-- Regenerate recovery codes --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-200">
                    <h3 class="mb-1 text-sm font-bold text-gray-800">توليد رموز استرداد جديدة</h3>
                    <p class="mb-4 text-xs leading-6 text-gray-500">تُلغي الرموز السابقة نهائياً.</p>
                    <form method="POST" action="{{ route('security.recovery-codes') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">كلمة المرور</label>
                            <input type="password" name="password" required autocomplete="current-password"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">رمز التطبيق الحالي</label>
                            <input type="text" name="code" required inputmode="numeric" maxlength="10" dir="ltr"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-center font-mono text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-2 text-sm font-bold text-white hover:bg-primary-700 transition">
                            توليد رموز جديدة
                        </button>
                    </form>
                </div>

                {{-- Reset device --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-200">
                    <h3 class="mb-1 text-sm font-bold text-gray-800">ربط جهاز جديد</h3>
                    <p class="mb-4 text-xs leading-6 text-gray-500">افصل التطبيق الحالي وابدأ التفعيل من جديد (تحتاج هاتفك الحالي).</p>
                    <form method="POST" action="{{ route('security.reset-device') }}" class="space-y-3"
                        onsubmit="return confirm('فصل التطبيق الحالي؟ ستحتاج لمسح رمز QR من جديد.')">
                        @csrf
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">كلمة المرور</label>
                            <input type="password" name="password" required autocomplete="current-password"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">رمز التطبيق الحالي</label>
                            <input type="text" name="code" required inputmode="numeric" maxlength="10" dir="ltr"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-center font-mono text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        </div>
                        <button type="submit" class="w-full rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50 transition">
                            فصل التطبيق الحالي
                        </button>
                    </form>
                </div>
            </div>

        @elseif($setupSecret)
            {{-- Setup in progress --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-200">
                <h2 class="text-base font-bold text-gray-800">تفعيل المصادقة الثنائية</h2>
                <ol class="mt-4 space-y-6 text-sm text-gray-600">
                    <li>
                        <p class="mb-3 font-semibold text-gray-800">1) افتح تطبيق المصادقة وامسح رمز QR:</p>
                        <div class="flex flex-wrap items-center gap-6">
                            <div id="qr-code" data-uri="{{ $provisioningUri }}"
                                class="flex h-[200px] w-[200px] items-center justify-center rounded-xl border border-gray-200 bg-white p-2">
                                <span class="p-2 text-center text-xs text-gray-400">جارٍ توليد رمز QR…</span>
                            </div>
                            <div class="flex-1 min-w-[220px]">
                                <p class="mb-2 text-xs text-gray-500">أو أدخل المفتاح يدوياً في التطبيق:</p>
                                <div class="flex items-center gap-2">
                                    <code id="secret-text" class="flex-1 break-all rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 font-mono text-xs text-gray-800" dir="ltr">{{ trim(chunk_split($setupSecret, 4, ' ')) }}</code>
                                    <button type="button" id="copy-secret" data-secret="{{ $setupSecret }}"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">نسخ</button>
                                </div>
                                <p class="mt-3 text-xs text-gray-500">النوع: TOTP — المدة: 30 ثانية — 6 أرقام.</p>
                            </div>
                        </div>
                    </li>
                    <li>
                        <p class="mb-3 font-semibold text-gray-800">2) أدخل الرمز الذي ظهر في التطبيق:</p>
                        <form method="POST" action="{{ route('security.confirm') }}" class="flex flex-wrap items-center gap-3">
                            @csrf
                            <input type="text" name="code" required autofocus inputmode="numeric" maxlength="10" dir="ltr"
                                placeholder="000000"
                                class="w-40 rounded-lg border border-gray-300 px-3 py-2 text-center font-mono text-lg tracking-widest focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            <button type="submit" class="rounded-lg bg-primary-600 px-5 py-2 text-sm font-bold text-white hover:bg-primary-700 transition">
                                تأكيد وتفعيل
                            </button>
                        </form>
                    </li>
                </ol>
                <form method="POST" action="{{ route('security.cancel') }}" class="mt-5">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">إلغاء التفعيل</button>
                </form>
            </div>

        @else
            {{-- Not enabled --}}
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 text-sm leading-7 text-blue-900">
                <strong>المصادقة الثنائية إلزامية على هذا النظام.</strong>
                لا يمكنك استخدام النظام قبل ربط تطبيق مصادقة (Google Authenticator / Microsoft Authenticator / Authy).
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-200">
                <h2 class="text-base font-bold text-gray-800">خطوات التفعيل</h2>
                <ol class="mt-3 list-decimal list-inside space-y-2 text-sm text-gray-600">
                    <li>ثبّت أحد تطبيقات المصادقة على هاتفك.</li>
                    <li>اضغط «بدء التفعيل» ليظهر لك رمز QR.</li>
                    <li>امسح الرمز ثم أدخل الأرقام الستة للتأكيد.</li>
                    <li>احفظ رموز الاسترداد في مكان آمن.</li>
                </ol>
                <form method="POST" action="{{ route('security.enable') }}" class="mt-6">
                    @csrf
                    <button type="submit" class="rounded-xl bg-primary-600 px-6 py-3 text-sm font-bold text-white hover:bg-primary-700 transition shadow-sm">
                        بدء التفعيل
                    </button>
                </form>
            </div>
        @endif

        <p class="text-center text-xs text-gray-400">Developer by BASSAM DAWOOD {{ date('Y') }}</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    (function () {
        var qr = document.getElementById('qr-code');
        if (qr && typeof QRCode !== 'undefined') {
            qr.innerHTML = '';
            new QRCode(qr, { text: qr.dataset.uri, width: 184, height: 184, correctLevel: QRCode.CorrectLevel.M });
        }

        function copy(text, button) {
            var done = function () {
                var original = button.textContent;
                button.textContent = 'تم النسخ';
                setTimeout(function () { button.textContent = original; }, 1500);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(done);
            } else {
                var area = document.createElement('textarea');
                area.value = text;
                document.body.appendChild(area);
                area.select();
                document.execCommand('copy');
                document.body.removeChild(area);
                done();
            }
        }

        var copySecret = document.getElementById('copy-secret');
        if (copySecret) {
            copySecret.addEventListener('click', function () {
                copy(copySecret.dataset.secret, copySecret);
            });
        }

        var copyCodes = document.getElementById('copy-codes');
        if (copyCodes) {
            copyCodes.addEventListener('click', function () {
                copy(copyCodes.dataset.codes, copyCodes);
            });
        }
    })();
</script>
@endsection
