<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">الميزانيات</h2>
            <a href="{{ route('budgets.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة ميزانية
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">الاسم</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">السنة المالية</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">من تاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">إلى تاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">المبلغ المخطط</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">المبلغ الفعلي</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">نسبة التنفيذ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $budget)
                        @php
                            $utilization = $budget->total_planned_amount > 0 ? ($budget->total_actual_amount / $budget->total_planned_amount) * 100 : 0;
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
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <a href="{{ route('budgets.show', $budget) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $budget->name }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $budget->fiscalYear->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $budget->date_from->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $budget->date_to->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $stateColors[$budget->state] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $stateLabels[$budget->state] ?? $budget->state }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($budget->total_planned_amount, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($budget->total_actual_amount, 2) }}</td>
                            <td class="px-4 py-3 text-left">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-24 rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full {{ $utilization > 100 ? 'bg-red-500' : ($utilization > 80 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ min($utilization, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ number_format($utilization, 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('budgets.edit', $budget) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="تعديل">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if($budget->state === 'draft')
                                        <form action="{{ route('budgets.confirm', $budget) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من تأكيد هذه الميزانية؟')">
                                            @csrf
                                            <button type="submit" class="rounded p-1 text-gray-500 hover:bg-green-50 hover:text-green-600 transition cursor-pointer" title="تأكيد">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('budgets.cancel', $budget) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من إلغاء هذه الميزانية؟')">
                                            @csrf
                                            <button type="submit" class="rounded p-1 text-gray-500 hover:bg-yellow-50 hover:text-yellow-600 transition cursor-pointer" title="إلغاء">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الميزانية؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded p-1 text-gray-500 hover:bg-red-50 hover:text-red-600 transition cursor-pointer" title="حذف">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">لا توجد ميزانيات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $budgets->links() }}</div>
    </div>
</x-app-layout>
