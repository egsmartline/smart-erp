<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">الإعدادات العامة</h2>
    </x-slot>

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
                            <option value="{{ $currency->id }}" {{ old('currency_id', $company->currency_id) == $currency->id ? 'selected' : '' }}>
                                {{ $currency->name }} ({{ $currency->code }}) - {{ $currency->symbol }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="logo" class="mb-1 block text-sm font-medium text-gray-700">شعار الشركة</label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @if($company->logo ?? false)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-12 w-12 rounded object-cover">
                        </div>
                    @endif
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
</x-app-layout>
