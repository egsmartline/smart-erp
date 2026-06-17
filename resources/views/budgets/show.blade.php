<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل الميزانية</h2>
            <div class="flex items-center gap-2">
                @if($budget->state === 'draft')
                    <a href="{{ route('budgets.edit', $budget) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                @endif
                <a href="{{ route('budgets.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">العودة للقائمة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">معلومات الميزانية</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">الاسم</span>
                    <span class="text-sm font-medium text-gray-900">{{ $budget->name }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">السنة المالية</span>
                    <span class="text-sm text-gray-900">{{ $budget->fiscalYear->name ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">من تاريخ</span>
                    <span class="text-sm text-gray-900">{{ $budget->date_from->format('Y-m-d') }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">إلى تاريخ</span>
                    <span class="text-sm text-gray-900">{{ $budget->date_to->format('Y-m-d') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">الحالة</span>
                    @php
                        $stateColors = [
                            'draft' => 'bg-yellow-100 text-yellow-800',
                            'confirmed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $stateLabels = [
                            'draft' => 'مسودة',
                            'confirmed' => 'مؤكدة',
                            'cancelled' => 'ملغاة',
                        ];
                    @endphp
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $stateColors[$budget->state] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $stateLabels[$budget->state] ?? $budget->state }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">ملخص الميزانية</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">المبلغ المخطط</span>
                    <span class="font-mono text-sm font-bold text-gray-900">{{ number_format($budget->total_planned_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <span class="text-sm text-gray-500">المبلغ الفعلي</span>
                    <span class="font-mono text-sm font-bold text-gray-900">{{ number_format($budget->total_actual_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">المتبقي</span>
                    @php $remaining = $budget->total_planned_amount - $budget->total_actual_amount; @endphp
                    <span class="font-mono text-sm font-bold {{ $remaining < 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($remaining, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">نسبة التنفيذ</h3>
            <div class="flex flex-col items-center justify-center">
                <div class="relative mb-4">
                    <svg class="h-32 w-32 -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#e5e7eb" stroke-width="12"/>
                        <circle cx="60" cy="60" r="50" fill="none"
                            stroke="{{ $utilization > 100 ? '#ef4444' : ($utilization > 80 ? '#eab308' : '#22c55e') }}"
                            stroke-width="12"
                            stroke-linecap="round"
                            stroke-dasharray="{{ 2 * 3.14159 * 50 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 50 * (1 - min($utilization, 100) / 100) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold {{ $utilization > 100 ? 'text-red-600' : ($utilization > 80 ? 'text-yellow-600' : 'text-green-600') }}">{{ number_format($utilization, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="mb-4 text-lg font-bold text-gray-800">بنود الميزانية</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">كود الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">اسم الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">المبلغ المخطط</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">المبلغ الفعلي</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">نسبة التنفيذ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budget->lines as $line)
                        @php
                            $lineUtilization = $line->planned_amount > 0 ? ($line->actual_amount / $line->planned_amount) * 100 : 0;
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">{{ $line->account->code ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $line->account->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($line->planned_amount, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($line->actual_amount, 2) }}</td>
                            <td class="px-4 py-3 text-left">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-24 rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full {{ $lineUtilization > 100 ? 'bg-red-500' : ($lineUtilization > 80 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ min($lineUtilization, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ number_format($lineUtilization, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">لا توجد بنود ميزانية</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
