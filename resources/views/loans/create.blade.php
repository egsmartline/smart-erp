<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة سلفة جديدة</h2>
            <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('loans.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="employee_id" class="mb-1 block text-sm font-medium text-gray-700">الموظف <span class="text-red-500">*</span></label>
                    <select name="employee_id" id="employee_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">اختر الموظف</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="cash_treasury_id" class="mb-1 block text-sm font-medium text-gray-700">الخزينة <span class="text-red-500">*</span></label>
                    <select name="cash_treasury_id" id="cash_treasury_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">اختر الخزينة</option>
                        @foreach($treasuries as $treasury)
                            <option value="{{ $treasury->id }}" @selected(old('cash_treasury_id') == $treasury->id)>{{ $treasury->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="amount" class="mb-1 block text-sm font-medium text-gray-700">مبلغ السلفة <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="monthly_deduction" class="mb-1 block text-sm font-medium text-gray-700">القسط الشهري <span class="text-red-500">*</span></label>
                    <input type="number" name="monthly_deduction" id="monthly_deduction" step="0.01" min="0.01" value="{{ old('monthly_deduction') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="start_date" class="mb-1 block text-sm font-medium text-gray-700">تاريخ البداية <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="end_date" class="mb-1 block text-sm font-medium text-gray-700">تاريخ النهاية <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-2 lg:col-span-1">
                    <label for="reason" class="mb-1 block text-sm font-medium text-gray-700">السبب</label>
                    <textarea name="reason" id="reason" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('reason') }}</textarea>
                </div>
                <div class="md:col-span-3">
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">حفظ السلفة</button>
                <a href="{{ route('loans.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
