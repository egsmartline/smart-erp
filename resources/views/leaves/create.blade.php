<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">طلب إجازة</h2>
            <a href="{{ route('leaves.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('leaves.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="employee_id" class="mb-1 block text-sm font-medium text-gray-700">الموظف <span class="text-red-500">*</span></label>
                    <select name="employee_id" id="employee_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">اختر الموظف</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="type" class="mb-1 block text-sm font-medium text-gray-700">نوع الإجازة <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="annual">إجازة سنوية</option>
                        <option value="sick">إجازة مرضية</option>
                        <option value="maternity">إجازة أمومة</option>
                        <option value="personal">إجازة شخصية</option>
                        <option value="unpaid">إجازة بدون راتب</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
                <div>
                    <label for="start_date" class="mb-1 block text-sm font-medium text-gray-700">من تاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="end_date" class="mb-1 block text-sm font-medium text-gray-700">إلى تاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label for="reason" class="mb-1 block text-sm font-medium text-gray-700">السبب</label>
                    <textarea name="reason" id="reason" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('reason') }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">تقديم الطلب</button>
                <a href="{{ route('leaves.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
