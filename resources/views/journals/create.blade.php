<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة دفتر يومية جديد</h2>
            <a href="{{ route('journals.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
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
        <form action="{{ route('journals.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="mb-1 block text-sm font-medium text-gray-700">كود الدفتر <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required maxlength="10"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="مثال: J001">
                </div>

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">اسم الدفتر <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="مثال: دفتر المبيعات">
                </div>

                <div>
                    <label for="type" class="mb-1 block text-sm font-medium text-gray-700">نوع الدفتر <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر النوع</option>
                        <option value="sale" {{ old('type') === 'sale' ? 'selected' : '' }}>مبيعات</option>
                        <option value="purchase" {{ old('type') === 'purchase' ? 'selected' : '' }}>مشتريات</option>
                        <option value="cash" {{ old('type') === 'cash' ? 'selected' : '' }}>نقدية</option>
                        <option value="bank" {{ old('type') === 'bank' ? 'selected' : '' }}>بنكية</option>
                        <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>عامة</option>
                    </select>
                </div>

                <div>
                    <label for="default_account_id" class="mb-1 block text-sm font-medium text-gray-700">الحساب الافتراضي</label>
                    <select name="default_account_id" id="default_account_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الحساب</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('default_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="currency_id" class="mb-1 block text-sm font-medium text-gray-700">العملة</label>
                    <select name="currency_id" id="currency_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العملة</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                {{ $currency->name }} ({{ $currency->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">دفتر نشط</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ الدفتر
                </button>
                <a href="{{ route('journals.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
