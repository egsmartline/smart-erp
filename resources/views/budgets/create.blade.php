<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة ميزانية</h2>
            <a href="{{ route('budgets.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">العودة للقائمة</a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('budgets.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">اسم الميزانية <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">السنة المالية <span class="text-red-500">*</span></label>
                    <select name="fiscal_year_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر السنة المالية</option>
                        @foreach($fiscalYears as $fy)
                            <option value="{{ $fy->id }}" {{ old('fiscal_year_id') == $fy->id ? 'selected' : '' }}>{{ $fy->name }}</option>
                        @endforeach
                    </select>
                    @error('fiscal_year_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">من تاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date_from" value="{{ old('date_from') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('date_from')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">إلى تاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date_to" value="{{ old('date_to') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('date_to')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">حفظ</button>
                <a href="{{ route('budgets.index') }}" class="rounded-lg bg-gray-200 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
