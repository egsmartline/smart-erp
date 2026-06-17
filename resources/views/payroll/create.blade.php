<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إنشاء كشف رواتب جديد</h2>
            <a href="{{ route('payroll.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('payroll.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="month" class="mb-1 block text-sm font-medium text-gray-700">الشهر <span class="text-red-500">*</span></label>
                    <select name="month" id="month" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('month', date('m')) == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="year" class="mb-1 block text-sm font-medium text-gray-700">السنة <span class="text-red-500">*</span></label>
                    <input type="number" name="year" id="year" min="2020" value="{{ old('year', date('Y')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>

            <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                <p>سيتم إنشاء كشف رواتب تلقائياً لجميع الموظفين النشطين براتبهم الأساسي. يمكنك تعديل البدلات والخصومات بعد الإنشاء.</p>
                <p class="mt-1 font-medium">عدد الموظفين النشطين: {{ $employees->count() }}</p>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">إنشاء كشف الرواتب</button>
                <a href="{{ route('payroll.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
