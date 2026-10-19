<x-app-layout>
    <style>
        @media print {
            .no-print { display: none !important; }
            #card-summary-grid { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
        }
    </style>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-xl font-bold text-gray-800">كارت صنف: {{ $item->name }}</h2>
            <div class="flex items-center gap-2 no-print">
                <a href="{{ route('items.show', $item) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بيانات الصنف</a>
                <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للقائمة</a>
                <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">طباعة</button>
            </div>
        </div>
    </x-slot>

    <div class="mb-4 rounded-xl bg-white shadow-sm border border-gray-200 p-4">
        <div class="grid gap-4 text-sm" style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem;">
            <div><span class="text-gray-500">الكود:</span> <span class="font-medium font-mono">{{ $item->sku ?? '-' }}</span></div>
            <div><span class="text-gray-500">الوحدة:</span> <span class="font-medium">{{ $item->unit->name ?? '-' }}</span></div>
            <div><span class="text-gray-500">التصنيف:</span> <span class="font-medium">{{ $item->category->name ?? '-' }}</span></div>
            <div><span class="text-gray-500">سعر البيع:</span> <span class="font-medium text-emerald-600">{{ number_format($item->selling_price, 2) }}</span></div>
        </div>
    </div>

    <div id="card-summary-grid" class="grid gap-4 mb-6" style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem;">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-800">{{ $rows->first()['balance_display'] ?? '0' }}</div>
            <div class="text-sm text-gray-500 mt-1">رصيد أول المدة</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($totalIn, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">إجمالي الوارد</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ number_format($totalOut, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">إجمالي الصادر</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold {{ $currentBalance <= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $rows->last()['balance_display'] ?? '0' }}</div>
            <div class="text-sm text-gray-500 mt-1">الرصيد الحالي</div>
        </div>
    </div>

    <div class="grid gap-4 mb-6" style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-xl font-bold text-gray-800">{{ number_format($currentValue, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">قيمة الرصيد الحالي</div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-xl font-bold text-gray-800">{{ number_format($currentAvg, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">متوسط التكلفة الحالي</div>
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
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">عرض</button>
            <a href="{{ route('items.stock-card', $item) }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إعادة تعيين</a>
        </form>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-3 py-3 font-medium">#</th>
                        <th class="px-3 py-3 font-medium">التاريخ</th>
                        <th class="px-3 py-3 font-medium">الحركة</th>
                        <th class="px-3 py-3 font-medium">الكمية</th>
                        <th class="px-3 py-3 font-medium">سعر الحركة</th>
                        <th class="px-3 py-3 font-medium">الرصيد</th>
                        <th class="px-3 py-3 font-medium">قيمة الرصيد</th>
                        <th class="px-3 py-3 font-medium">متوسط التكلفة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $index => $row)
                        <tr class="{{ $row['dir'] === 'open' ? 'bg-gray-50 font-medium' : 'hover:bg-gray-50' }}">
                            <td class="px-3 py-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-3 py-3 font-mono text-xs">{{ $row['date'] ? $row['date']->format('d/m/Y') : '-' }}</td>
                            <td class="px-3 py-3">
                                @if($row['doc_url'])
                                    <a href="{{ $row['doc_url'] }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:underline font-medium">
                                        <span>{{ $row['label'] }}</span>
                                        <span class="font-mono">{{ $row['doc_number'] }}</span>
                                    </a>
                                @else
                                    {{ $row['label'] }}
                                @endif
                            </td>
                            <td class="px-3 py-3 font-medium {{ $row['dir'] === 'out' ? 'text-red-600' : ($row['dir'] === 'in' ? 'text-emerald-600' : 'text-gray-800') }}">{{ $row['qty_display'] }}</td>
                            <td class="px-3 py-3 font-mono">{{ is_null($row['price']) ? '-' : number_format((float) $row['price'], 2) }}</td>
                            <td class="px-3 py-3 font-medium">{{ $row['balance_display'] }}</td>
                            <td class="px-3 py-3 font-mono">{{ number_format((float) $row['value'], 2) }}</td>
                            <td class="px-3 py-3 font-mono">{{ number_format((float) $row['avg'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">لا توجد حركات لهذا الصنف</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
