<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">كشف الرواتب: {{ $payroll->payroll_number }}</h2>
            <div class="flex items-center gap-2">
                @if($payroll->state == 'draft')
                    <form action="{{ route('payroll.confirm', $payroll) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد كشف الرواتب؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">تأكيد</button>
                    </form>
                @endif
                <a href="{{ route('payroll.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات كشف الرواتب</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-sm">
            <div><span class="text-gray-500">المرجع:</span><span class="mr-2 font-medium font-mono">{{ $payroll->payroll_number }}</span></div>
            <div><span class="text-gray-500">الشهر:</span><span class="mr-2 font-medium">{{ $payroll->month }}</span></div>
            <div><span class="text-gray-500">السنة:</span><span class="mr-2 font-medium">{{ $payroll->year }}</span></div>
            <div><span class="text-gray-500">الإجمالي:</span><span class="mr-2 font-medium font-mono">{{ number_format($payroll->total_net, 2) }}</span></div>
            <div><span class="text-gray-500">الحالة:</span>
                @if($payroll->state == 'draft')
                    <span class="mr-2 inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                @else
                    <span class="mr-2 inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مؤكد</span>
                @endif
            </div>
            <div><span class="text-gray-500">أنشأه:</span><span class="mr-2 font-medium">{{ $payroll->payslips->first()->employee->full_name ?? auth()->user()->name }}</span></div>
            @if($payroll->notes)
                <div class="col-span-2 md:col-span-4"><span class="text-gray-500">ملاحظات:</span><span class="mr-2 font-medium">{{ $payroll->notes }}</span></div>
            @endif
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">كشوف الرواتب ({{ $payroll->payslips->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الموظف</th>
                        <th class="px-4 py-3 font-semibold text-center">الراتب الأساسي</th>
                        <th class="px-4 py-3 font-semibold text-center">البدلات</th>
                        <th class="px-4 py-3 font-semibold text-center">الخصومات</th>
                        <th class="px-4 py-3 font-semibold text-center">الصافي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payroll->payslips as $ps)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $ps->employee->full_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ number_format($ps->basic_salary, 2) }}</td>
                            <td class="px-4 py-3 text-center font-mono text-green-600">{{ number_format($ps->total_allowances, 2) }}</td>
                            <td class="px-4 py-3 text-center font-mono text-red-600">{{ number_format($ps->total_deductions, 2) }}</td>
                            <td class="px-4 py-3 text-center font-mono font-bold">{{ number_format($ps->net_salary, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا توجد كشوف</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-300 bg-gray-50 font-bold">
                        <td class="px-4 py-3">الإجمالي</td>
                        <td class="px-4 py-3 text-center font-mono">{{ number_format($payroll->payslips->sum('basic_salary'), 2) }}</td>
                        <td class="px-4 py-3 text-center font-mono text-green-600">{{ number_format($payroll->payslips->sum('total_allowances'), 2) }}</td>
                        <td class="px-4 py-3 text-center font-mono text-red-600">{{ number_format($payroll->payslips->sum('total_deductions'), 2) }}</td>
                        <td class="px-4 py-3 text-center font-mono">{{ number_format($payroll->payslips->sum('net_salary'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
