<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل كشف الرواتب: {{ $payroll->payroll_number }}</h2>
            <a href="{{ route('payroll.show', $payroll) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('payroll.update', $payroll) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم المرجع</label>
                    <p class="font-mono font-bold text-gray-900">{{ $payroll->payroll_number }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الشهر / السنة</label>
                    <p class="text-gray-900">{{ $payroll->month }}/{{ $payroll->year }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الحالة</label>
                    @if($payroll->state == 'draft')
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مؤكد</span>
                    @endif
                </div>
                <div>
                    <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes', $payroll->notes) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>

            <div class="overflow-x-auto mt-6">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 font-semibold text-gray-700">الموظف</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center">الراتب الأساسي</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center">البدلات</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center">الخصومات</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-center">الإضافي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payroll->payslips as $ps)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $ps->employee->full_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <input type="hidden" name="payslips[{{ $loop->index }}][id]" value="{{ $ps->id }}">
                                <input type="number" step="0.01" min="0" name="payslips[{{ $loop->index }}][basic_salary]" value="{{ old('payslips.' . $loop->index . '.basic_salary', $ps->basic_salary) }}" class="w-24 rounded border border-gray-300 px-2 py-1 text-center text-sm">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" step="0.01" min="0" name="payslips[{{ $loop->index }}][total_allowances]" value="{{ old('payslips.' . $loop->index . '.total_allowances', $ps->total_allowances) }}" class="w-24 rounded border border-gray-300 px-2 py-1 text-center text-sm">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" step="0.01" min="0" name="payslips[{{ $loop->index }}][total_deductions]" value="{{ old('payslips.' . $loop->index . '.total_deductions', $ps->total_deductions) }}" class="w-24 rounded border border-gray-300 px-2 py-1 text-center text-sm">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" step="0.01" min="0" name="payslips[{{ $loop->index }}][overtime_pay]" value="{{ old('payslips.' . $loop->index . '.overtime_pay', $ps->overtime_pay) }}" class="w-24 rounded border border-gray-300 px-2 py-1 text-center text-sm">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">حفظ التعديلات</button>
                <a href="{{ route('payroll.show', $payroll) }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
