@extends('layouts.auth')

@section('content')
<div class="min-h-screen login-bg flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8 floating">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4">
                <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">التحقق بخطوتين</h1>
            <p class="text-blue-100 mt-2">أدخل الرمز من تطبيق المصادقة لإكمال الدخول</p>
        </div>

        <div class="glass-card rounded-2xl shadow-2xl p-8">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}">
                @csrf

                <div class="mb-6">
                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">رمز التحقق</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" required autofocus
                        autocomplete="one-time-code" inputmode="numeric" maxlength="20" dir="ltr"
                        class="w-full px-4 py-3 text-center text-2xl tracking-[0.4em] font-mono border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                        placeholder="000000">
                    <p class="mt-3 text-xs text-gray-500 leading-6">
                        إذا فقدت هاتفك، أدخل أحد <strong>رموز الاسترداد</strong> في نفس الحقل (مثال: ABCDE-FGHIJ).
                    </p>
                </div>

                <button type="submit"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                    تأكيد الدخول
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">الرجوع لتسجيل الدخول</a>
            </div>
        </div>

        <p class="text-center text-blue-100 text-sm mt-6">
            Developer by BASSAM DAWOOD {{ date('Y') }}
        </p>
    </div>
</div>
@endsection
