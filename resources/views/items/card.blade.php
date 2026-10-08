<x-app-layout>
    <style>
        @media print {
            #card-info-grid,
            #card-summary-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }
            .no-print { display: none !important; }
        }
    </style>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-xl font-bold text-gray-800">كارت الصنف: {{ $item->name }}</h2>
            <div class="flex items-center gap-2 no-print">
                <a href="{{ route('items.card', $item) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إعادة تعيين</a>
                <a href="{{ route('items.show', $item) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بيانات الصنف</a>
                <a href="{{ route('items.print-card', $item) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition no-print">طباعة</a>
            </div>
        </div>
    </x-slot>

    <div class="mb-4 rounded-xl bg-white shadow-sm border border-gray-200 p-4">
        <div id="card-info-grid" class="grid gap-4 text-sm" style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem;">
            <div><span class="text-gray-500">الكود:</span> <span class="font-medium font-mono">{{ $item->sku ?? '-' }}</span></div>
            <div><span class="text-gray-500">الوحدة:</span> <span class="font-medium">{{ $item->unit->name ?? '-' }}</span></div>
            <div><span class="text-gray-500">التصنيف:</span> <span class="font-medium">{{ $item->category->name ?? '-' }}</span></div>
            <div><span class="text-gray-500">سعر البيع:</span> <span class="font-medium text-emerald-600">{{ number_format($item->selling_price, 2) }}</span></div>
        </div>
    </div>

    <div id="card-summary-grid" class="grid gap-4 mb-6" style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem;">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-800">{{ number_format($opening, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">الرصيد الافتتاحي</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($totals['in'], 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">إجمالي الوارد</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ number_format($totals['out'], 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">إجمالي الصادر</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold {{ $currentBalance <= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($currentBalance, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">الرصيد الحالي</div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 mb-6 no-print">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">من تاريخ</label>
                <input type="date" name="from" value="{{ $from ?? '' }}" class="rounded-lg border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">إلى تاريخ</label>
                <input type="date" name="to" value="{{ $to ?? '' }}" class="rounded-lg border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">الحركة</label>
                <select name="dir" class="rounded-lg border-gray-300 border px-3 py-2 text-sm">
                    <option value="all" @selected(($dir ?? 'all') === 'all')>الكل</option>
                    <option value="in" @selected(($dir ?? 'all') === 'in')>وارد فقط</option>
                    <option value="out" @selected(($dir ?? 'all') === 'out')>صادر فقط</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">عرض</button>
        </form>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 font-medium">#</th>
                        <th class="px-4 py-3 font-medium">التاريخ</th>
                        <th class="px-4 py-3 font-medium">البيان / المرجع</th>
                        <th class="px-4 py-3 font-medium">نوع الحركة</th>
                        <th class="px-4 py-3 font-medium text-emerald-600">وارد</th>
                        <th class="px-4 py-3 font-medium text-red-600">صادر</th>
                        <th class="px-4 py-3 font-medium">الرصيد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($display as $index => $m)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $m->created_at ? $m->created_at->format('Y-m-d H:i') : '-' }}</td>
                            <td class="px-4 py-3">
                                @if($m->doc_url)
                                    <a href="{{ $m->doc_url }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:underline font-medium">
                                        <span>{{ $m->doc_label }}</span>
                                        <span class="font-mono">{{ $m->doc_number }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4m14-10h-4m0 0V4m0 4l-9 9" /></svg>
                                    </a>
                                    @if($m->description)
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $m->description }}</div>
                                    @endif
                                @else
                                    {{ $m->description ?? '-' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $typeLabels[$m->type] ?? $m->type }}</td>
                            <td class="px-4 py-3 text-emerald-600 font-medium">{{ $m->in_qty ? number_format($m->in_qty, 2) : '' }}</td>
                            <td class="px-4 py-3 text-red-600 font-medium">{{ $m->out_qty ? number_format($m->out_qty, 2) : '' }}</td>
                            <td class="px-4 py-3 font-medium">{{ number_format($m->balance_after, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد حركات لهذا الصنف في الفترة المحددة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
