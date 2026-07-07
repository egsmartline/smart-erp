<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة مورد جديد</h2>
            <a href="{{ route('suppliers.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                إلغاء
            </a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">اسم المورد <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">الهاتف</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="mobile" class="mb-1 block text-sm font-medium text-gray-700">الموبايل</label>
                    <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="address" class="mb-1 block text-sm font-medium text-gray-700">العنوان</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="city" class="mb-1 block text-sm font-medium text-gray-700">المدينة</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="country" class="mb-1 block text-sm font-medium text-gray-700">الدولة</label>
                    <input type="text" name="country" id="country" value="{{ old('country') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="tax_number" class="mb-1 block text-sm font-medium text-gray-700">الرقم الضريبي</label>
                    <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="credit_limit" class="mb-1 block text-sm font-medium text-gray-700">حد الائتمان</label>
                    <input type="number" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="opening_balance" class="mb-1 block text-sm font-medium text-gray-700">الرصيد الافتتاحي</label>
                    <input type="number" name="opening_balance" id="opening_balance" value="{{ old('opening_balance', 0) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="opening_balance_type" class="mb-1 block text-sm font-medium text-gray-700">نوع الرصيد</label>
                    <select name="opening_balance_type" id="opening_balance_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="debit" {{ old('opening_balance_type', 'debit') === 'debit' ? 'selected' : '' }}>مدين (له)</option>
                        <option value="credit" {{ old('opening_balance_type') === 'credit' ? 'selected' : '' }}>دائن (عليه)</option>
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ المورد
                </button>
                <a href="{{ route('suppliers.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
