<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل الحساب البنكي</h2>
            <a href="{{ route('bank-accounts.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('bank-accounts.update', $bankAccount) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="bank_name" class="mb-1 block text-sm font-medium text-gray-700">اسم البنك <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="account_name" class="mb-1 block text-sm font-medium text-gray-700">اسم الحساب <span class="text-red-500">*</span></label>
                    <input type="text" name="account_name" id="account_name" value="{{ old('account_name', $bankAccount->account_name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="account_number" class="mb-1 block text-sm font-medium text-gray-700">رقم الحساب <span class="text-red-500">*</span></label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="iban" class="mb-1 block text-sm font-medium text-gray-700">IBAN</label>
                    <input type="text" name="iban" id="iban" value="{{ old('iban', $bankAccount->iban) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="swift_code" class="mb-1 block text-sm font-medium text-gray-700">Swift Code</label>
                    <input type="text" name="swift_code" id="swift_code" value="{{ old('swift_code', $bankAccount->swift_code) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="account_id" class="mb-1 block text-sm font-medium text-gray-700">الحساب المحاسبي <span class="text-red-500">*</span></label>
                    <select name="account_id" id="account_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الحساب</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id', $bankAccount->account_id) == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="currency_id" class="mb-1 block text-sm font-medium text-gray-700">العملة <span class="text-red-500">*</span></label>
                    <select name="currency_id" id="currency_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العملة</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('currency_id', $bankAccount->currency_id) == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="opening_balance" class="mb-1 block text-sm font-medium text-gray-700">الرصيد الافتتاحي</label>
                    <input type="number" name="opening_balance" id="opening_balance" value="{{ old('opening_balance', $bankAccount->opening_balance) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="description" class="mb-1 block text-sm font-medium text-gray-700">الوصف</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('description', $bankAccount->notes ?? '') }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    تحديث الحساب البنكي
                </button>
                <a href="{{ route('bank-accounts.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
