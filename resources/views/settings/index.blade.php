<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">الإعدادات</h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="mb-6 flex items-center gap-1 rounded-xl bg-gray-100 p-1">
        <a href="{{ route('settings.index') }}" class="flex-1 rounded-lg px-4 py-2.5 text-center text-sm font-bold transition {{ request()->routeIs('settings.index') ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">
            <svg class="inline-block h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            الإعدادات العامة
        </a>
        <a href="{{ route('settings.roles.index') }}" class="flex-1 rounded-lg px-4 py-2.5 text-center text-sm font-bold transition {{ request()->routeIs('settings.roles.*') ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">
            <svg class="inline-block h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            الصلاحيات
        </a>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <label for="company_name" class="mb-1 block text-sm font-medium text-gray-700">اسم الشركة <span class="text-red-500">*</span></label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $company->name ?? '') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $company->email ?? '') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">الهاتف</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone ?? '') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="tax_number" class="mb-1 block text-sm font-medium text-gray-700">الرقم الضريبي</label>
                    <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number', $company->tax_number ?? '') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="website" class="mb-1 block text-sm font-medium text-gray-700">الموقع الإلكتروني</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $company->website ?? '') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="lg:col-span-3">
                    <label for="address" class="mb-1 block text-sm font-medium text-gray-700">العنوان</label>
                    <input type="text" name="address" id="address" value="{{ old('address', $company->address ?? '') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="currency_id" class="mb-1 block text-sm font-medium text-gray-700">العملة الافتراضية <span class="text-red-500">*</span></label>
                    <select name="currency_id" id="currency_id" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('currency_id', $company->currency_id ?? '') == $currency->id ? 'selected' : '' }}>
                                {{ $currency->name }} ({{ $currency->code }}) - {{ $currency->symbol }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="secondary_currency_id" class="mb-1 block text-sm font-medium text-gray-700">العملة الثانوية</label>
                    <select name="secondary_currency_id" id="secondary_currency_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">-- بدون عملة ثانوية --</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('secondary_currency_id', $company->secondary_currency_id ?? '') == $currency->id ? 'selected' : '' }}>
                                {{ $currency->name }} ({{ $currency->code }}) - {{ $currency->symbol }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="logo" class="mb-1 block text-sm font-medium text-gray-700">شعار الشركة</label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <div id="logo-preview" class="mt-2">
                        @if($company->logo ?? false)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Current Logo" class="h-16 w-16 rounded-lg object-cover">
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>

    {{-- Reset Section --}}
    <div class="mt-8 rounded-xl border-2 border-red-200 bg-red-50 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-red-800">تصفير الحسابات وبيانات المشروع</h3>
                <p class="text-sm text-red-600">سيتم حذف جميع البيانات الحركة والمالية مع الاحتفاظ بالبيانات الأساسية</p>
            </div>
        </div>

        <div class="mb-4 rounded-lg bg-white border border-red-200 p-4 text-sm text-gray-700">
            <p class="font-bold text-red-700 mb-2">سيتم حذف جميع بيانات المشروع بالكامل:</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg> العملاء والموردين</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg> الأصناف والمستودعات</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg> الموظفين والرواتب</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg> قيود اليومية والفواتير</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg> أوامر الشراء</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg> المدفوعات والخزينة</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg> البنوك والمصروفات</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg> العهد والعقود والإجازات</span>
            </div>
            <p class="font-bold text-green-700 mt-3 mb-1">سيتم الاحتفاظ بـ:</p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> دليل الحسابات (تصفير الأرصدة)</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> العملات وإعدادات الضرائب</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> المستخدمين والصلاحيات</span>
                <span class="flex items-center gap-1"><svg class="h-3 w-3 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> السنوات المالية</span>
            </div>
        </div>

        <div x-data="{ confirmReset: false, confirmText: '' }">
            <div class="flex items-start gap-3 mb-4">
                <input type="checkbox" id="confirmCheckbox" x-model="confirmReset" class="mt-1 h-4 w-4 rounded border-red-300 text-red-600 focus:ring-red-500">
                <label for="confirmCheckbox" class="text-sm font-medium text-red-700">
                    أنا متأكد، أرغب في حذف جميع البيانات المالية والحركية وإعادة تعيين الأرصدة
                </label>
            </div>
            <div x-show="confirmReset" x-transition>
                <label class="mb-1 block text-sm font-medium text-gray-700">اكتب "تصفير" للتأكيد</label>
                <div class="flex items-center gap-3">
                    <input type="text" x-model="confirmText" placeholder="اكتب تصفير"
                        class="w-64 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    <form action="{{ route('settings.reset') }}" method="POST">
                        @csrf
                        <input type="hidden" name="confirm" value="1">
                        <button type="submit"
                            x-show="confirmText === 'تصفير'"
                            class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition"
                            onclick="return confirm('تحذير نهائي! هذا الإجراء لا يمكن التراجع عنه. هل أنت متأكد؟')">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            تصفير الحسابات والبيانات
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('logo').addEventListener('change', function(e) {
            const preview = document.getElementById('logo-preview');
            preview.innerHTML = '';
            if (e.target.files[0]) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(e.target.files[0]);
                img.className = 'h-16 w-16 rounded-lg object-cover';
                preview.appendChild(img);
            }
        });
    </script>
</x-app-layout>
