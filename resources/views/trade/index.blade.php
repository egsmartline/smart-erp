<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">الاستيراد والتصدير</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('trade.create') }}?type=import" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    عملية استيراد
                </a>
                <a href="{{ route('trade.create') }}?type=export" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    عملية تصدير
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
        <div class="rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 p-4">
            <div class="text-sm text-blue-600 font-medium">إجمالي عمليات الاستيراد</div>
            <div class="text-2xl font-bold text-blue-800">{{ $importCount }}</div>
        </div>
        <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 p-4">
            <div class="text-sm text-emerald-600 font-medium">إجمالي عمليات التصدير</div>
            <div class="text-2xl font-bold text-emerald-800">{{ $exportCount }}</div>
        </div>
        <div class="rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 p-4">
            <div class="text-sm text-amber-600 font-medium">قيد التنفيذ</div>
            <div class="text-2xl font-bold text-amber-800">{{ $activeCount }}</div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">النوع</label>
                    <select name="type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <option value="import" {{ request('type') === 'import' ? 'selected' : '' }}>استيراد</option>
                        <option value="export" {{ request('type') === 'export' ? 'selected' : '' }}>تصدير</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">الحالة</label>
                    <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">الكل</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>مؤكدة</option>
                        <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                        <option value="cleared" {{ request('status') === 'cleared' ? 'selected' : '' }}>تم التخليص</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغية</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="mb-1 block text-xs font-medium text-gray-500">بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم العملية، الطرف، الحاوية، السفينة..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
                <a href="{{ route('trade.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إعادة تعيين</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-right">
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">رقم العملية</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">النوع</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">التاريخ</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">الطرف</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">الحالة</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">القيمة</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">الحاوية</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600">LC</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($operations as $op)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3"><a href="{{ route('trade.show', $op) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $op->operation_number }}</a></td>
                        <td class="px-4 py-3">
                            @if($op->type === 'import')
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">استيراد</span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">تصدير</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $op->date->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $op->party_name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusMap = ['draft' => ['bg-gray-100', 'text-gray-800', 'مسودة'], 'confirmed' => ['bg-blue-100', 'text-blue-800', 'مؤكدة'], 'shipped' => ['bg-amber-100', 'text-amber-800', 'تم الشحن'], 'cleared' => ['bg-purple-100', 'text-purple-800', 'تم التخليص'], 'completed' => ['bg-emerald-100', 'text-emerald-800', 'مكتملة'], 'cancelled' => ['bg-red-100', 'text-red-800', 'ملغية']];
                                [$bg, $text, $label] = $statusMap[$op->status] ?? ['bg-gray-100', 'text-gray-800', $op->status];
                            @endphp
                            <span class="inline-flex items-center rounded-full {{ $bg }} px-2.5 py-0.5 text-xs font-medium {{ $text }}">{{ $label }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 font-medium">{{ number_format($op->total_value, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $op->container_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $op->lc_number ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('trade.show', $op) }}" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-blue-600" title="عرض">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('trade.edit', $op) }}" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-amber-600" title="تعديل">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p>لا توجد عمليات بعد</p>
                            <div class="mt-4 flex justify-center gap-3">
                                <a href="{{ route('trade.create') }}?type=import" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">إنشاء عملية استيراد</a>
                                <a href="{{ route('trade.create') }}?type=export" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">إنشاء عملية تصدير</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($operations->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $operations->links() }}
        </div>
        @endif
    </div>
</x-app-layout>