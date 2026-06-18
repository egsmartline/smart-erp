<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">إضافة شركة جديدة</h2>
    </x-slot>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">اسم الشركة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="name_en" class="mb-1 block text-sm font-medium text-gray-700">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" id="name_en" value="{{ old('name_en') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">الهاتف</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="tax_number" class="mb-1 block text-sm font-medium text-gray-700">الرقم الضريبي</label>
                    <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="website" class="mb-1 block text-sm font-medium text-gray-700">الموقع الإلكتروني</label>
                    <input type="url" name="website" id="website" value="{{ old('website') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="lg:col-span-3">
                    <label for="address" class="mb-1 block text-sm font-medium text-gray-700">العنوان</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="currency_code" class="mb-1 block text-sm font-medium text-gray-700">العملة الأساسية</label>
                    <select name="currency_code" id="currency_code"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="EGP" {{ old('currency_code') == 'EGP' ? 'selected' : '' }}>ج.م - جنيه مصري</option>
                        <option value="USD" {{ old('currency_code') == 'USD' ? 'selected' : '' }}>$ - دولار أمريكي</option>
                        <option value="SAR" {{ old('currency_code') == 'SAR' ? 'selected' : '' }}>ر.س - ريال سعودي</option>
                        <option value="AED" {{ old('currency_code') == 'AED' ? 'selected' : '' }}>د.إ - درهم إماراتي</option>
                    </select>
                </div>

                <div>
                    <label for="secondary_currency_id" class="mb-1 block text-sm font-medium text-gray-700">العملة الثانوية</label>
                    <select name="secondary_currency_id" id="secondary_currency_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">-- بدون عملة ثانوية --</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('secondary_currency_id') == $currency->id ? 'selected' : '' }}>
                                {{ $currency->name }} ({{ $currency->code }}) - {{ $currency->symbol }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="logo" class="mb-1 block text-sm font-medium text-gray-700">شعار الشركة</label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <div id="logo-preview" class="mt-2"></div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700">نشطة</label>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ الشركة
                </button>
                <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('logo').addEventListener('change', function(e) {
            const preview = document.getElementById('logo-preview');
            preview.innerHTML = '';
            if (e.target.files[0]) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(e.target.files[0]);
                img.className = 'h-20 w-20 rounded-lg object-cover';
                preview.appendChild(img);
            }
        });
    </script>
</x-app-layout>
