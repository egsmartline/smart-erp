<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل العهدة: {{ $custody->custody_number }}</h2>
            <a href="{{ route('custodies.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('custodies.update', $custody) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="employee_id" class="mb-1 block text-sm font-medium text-gray-700">الموظف <span class="text-red-500">*</span></label>
                    <select name="employee_id" id="employee_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الموظف</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id', $custody->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->full_name ?? $emp->first_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="amount" class="mb-1 block text-sm font-medium text-gray-700">المبلغ <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount', $custody->amount) }}" step="0.01" min="0.01" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="returned_amount" class="mb-1 block text-sm font-medium text-gray-700">المردود</label>
                    <input type="number" name="returned_amount" id="returned_amount" value="{{ old('returned_amount', $custody->returned_amount) }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="date" class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', $custody->date instanceof \Carbon\Carbon ? $custody->date->format('Y-m-d') : $custody->date) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-gray-700">الحالة</label>
                    <select name="status" id="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="active" {{ old('status', $custody->status) == 'active' ? 'selected' : '' }}>نشطة</option>
                        <option value="partial" {{ old('status', $custody->status) == 'partial' ? 'selected' : '' }}>مرتجعة جزئياً</option>
                        <option value="settled" {{ old('status', $custody->status) == 'settled' ? 'selected' : '' }}>مصفاة</option>
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="description" class="mb-1 block text-sm font-medium text-gray-700">الوصف</label>
                    <textarea name="description" id="description" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('description', $custody->description) }}</textarea>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('notes', $custody->notes) }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    تحديث العهدة
                </button>
                <a href="{{ route('custodies.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
