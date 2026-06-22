<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل الخزينة: {{ $treasury->name }}</h2>
            <a href="{{ route('cash-treasuries.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('cash-treasuries.update', $treasury) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">اسم الخزينة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $treasury->name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="code" class="mb-1 block text-sm font-medium text-gray-700">كود الخزينة <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $treasury->code) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="مثال: CASH-01">
                </div>
                <div>
                    <label for="account_id" class="mb-1 block text-sm font-medium text-gray-700">الحساب المحاسبي <span class="text-red-500">*</span></label>
                    <select name="account_id" id="account_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الحساب</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id', $treasury->account_id) == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="opening_balance" class="mb-1 block text-sm font-medium text-gray-700">الرصيد الافتتاحي</label>
                    <input type="number" name="opening_balance" id="opening_balance" value="{{ old('opening_balance', $treasury->opening_balance) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="currency_id" class="mb-1 block text-sm font-medium text-gray-700">العملة</label>
                    <select name="currency_id" id="currency_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">العملة الافتراضية</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('currency_id', $treasury->currency_id) == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-6 pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $treasury->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">نشطة</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $treasury->is_default) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">افتراضية</span>
                    </label>
                </div>
                <div>
                    <label for="whatsapp_number" class="mb-1 block text-sm font-medium text-gray-700">رقم الواتساب</label>
                    <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $treasury->whatsapp_number) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="مثال: 201234567890">
                </div>
                <div class="md:col-span-2">
                    <label for="whatsapp_message" class="mb-1 block text-sm font-medium text-gray-700">رسالة الواتساب</label>
                    <textarea name="whatsapp_message" id="whatsapp_message" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="السلام عليكم رصيد الخزينة {name} الحالي هو {balance} {currency}">{{ old('whatsapp_message', $treasury->whatsapp_message) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">الرصيد الحالي: {{ number_format($treasury->current_balance, 2) }} {{ $treasury->currency->code ?? 'ج.م' }}</p>
                    <p class="mt-1 text-xs text-gray-400">نموذج الرسالة: {{ $treasury->whatsapp_message ? str_replace(['{name}','{balance}','{currency}'],[$treasury->name, number_format($treasury->current_balance, 2), $treasury->currency->code ?? 'ج.م'], $treasury->whatsapp_message) : str_replace(['{name}','{balance}','{currency}'],[$treasury->name, number_format($treasury->current_balance, 2), $treasury->currency->code ?? 'ج.م'], 'السلام عليكم رصيد الخزينة {name} الحالي هو {balance} {currency}') }}</p>
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="mb-1 block text-sm font-medium text-gray-700">الوصف</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('description', $treasury->notes ?? '') }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    تحديث الخزينة
                </button>
                <a href="{{ route('cash-treasuries.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
