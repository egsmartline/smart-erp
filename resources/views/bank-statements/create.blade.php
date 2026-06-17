<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة كشف حساب بنكي جديد</h2>
            <a href="{{ route('bank-statements.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
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
        <form action="{{ route('bank-statements.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="bank_account_id" class="mb-1 block text-sm font-medium text-gray-700">الحساب البنكي <span class="text-red-500">*</span></label>
                    <select name="bank_account_id" id="bank_account_id" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الحساب البنكي</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->bank_name }} - {{ $account->account_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="journal_id" class="mb-1 block text-sm font-medium text-gray-700">دفتر اليومية</label>
                    <select name="journal_id" id="journal_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر دفتر اليومية</option>
                        @foreach($journals as $journal)
                            <option value="{{ $journal->id }}" {{ old('journal_id') == $journal->id ? 'selected' : '' }}>
                                {{ $journal->code }} - {{ $journal->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date" class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="start_balance" class="mb-1 block text-sm font-medium text-gray-700">الرصيد الافتتاحي <span class="text-red-500">*</span></label>
                    <input type="number" name="start_balance" id="start_balance" value="{{ old('start_balance', 0) }}" step="0.01" min="0" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="ملاحظات إضافية...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ الكشف
                </button>
                <a href="{{ route('bank-statements.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
