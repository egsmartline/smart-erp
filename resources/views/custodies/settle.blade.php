<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تسوية العهدة: {{ $custody->custody_number }}</h2>
            <a href="{{ route('custodies.show', $custody) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <p class="text-xs text-gray-500">الموظف</p>
            <p class="font-medium text-gray-900">{{ $custody->employee->full_name ?? $custody->employee->first_name ?? '-' }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <p class="text-xs text-gray-500">المبلغ الأصلي</p>
            <p class="font-mono font-bold text-gray-900">{{ number_format($custody->amount, 2) }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <p class="text-xs text-gray-500">المردود السابق</p>
            <p class="font-mono text-gray-900">{{ number_format($custody->returned_amount, 2) }}</p>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form action="{{ route('custodies.process-settlement', $custody) }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="returned_amount" class="mb-1 block text-sm font-medium text-gray-700">المبلغ المرتجع <span class="text-red-500">*</span></label>
                    <input type="number" name="returned_amount" id="returned_amount" value="{{ old('returned_amount', $custody->amount - $custody->returned_amount) }}" step="0.01" min="0" max="{{ $custody->amount }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="settlement_date" class="mb-1 block text-sm font-medium text-gray-700">تاريخ التسوية <span class="text-red-500">*</span></label>
                    <input type="date" name="settlement_date" id="settlement_date" value="{{ old('settlement_date', date('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="treasury_id" class="mb-1 block text-sm font-medium text-gray-700">الخزينة</label>
                    <select name="treasury_id" id="treasury_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الخزينة</option>
                        @foreach($treasuries as $treasury)
                            <option value="{{ $treasury->id }}" {{ old('treasury_id', $custody->treasury_id) == $treasury->id ? 'selected' : '' }}>{{ $treasury->name }} ({{ $treasury->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="currency_id" class="mb-1 block text-sm font-medium text-gray-700">العملة</label>
                    <select name="currency_id" id="currency_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العملة</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('currency_id', $custody->currency_id) == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="account_id" class="mb-1 block text-sm font-medium text-gray-700">الحساب المحاسبي</label>
                    <select name="account_id" id="account_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الحساب</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id', $custody->account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات التسوية</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="تفاصيل التسوية">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-green-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    تأكيد التسوية
                </button>
                <a href="{{ route('custodies.show', $custody) }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>