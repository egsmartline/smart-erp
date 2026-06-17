<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تقرير المخزون</h2>
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">طباعة</button>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <div class="rounded-xl bg-blue-50 border border-blue-200 p-6 text-center">
            <div class="text-sm text-blue-600 mb-2">إجمالي قيمة المخزون</div>
            <div class="text-3xl font-bold text-blue-700">{{ number_format($totalValue, 2) }} ج.م</div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الكود</th>
                        <th class="px-4 py-3 font-semibold">اسم الصنف</th>
                        <th class="px-4 py-3 font-semibold">التصنيف</th>
                        <th class="px-4 py-3 font-semibold">الوحدة</th>
                        <th class="px-4 py-3 font-semibold text-left">سعر الشراء</th>
                        <th class="px-4 py-3 font-semibold text-left">الكمية</th>
                        <th class="px-4 py-3 font-semibold text-left">القيمة</th>
                        <th class="px-4 py-3 font-semibold text-left">حد إعادة الطلب</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php $stock = $item->warehouses->sum('quantity'); @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2 font-mono text-xs">{{ $item->sku ?? '-' }}</td>
                            <td class="px-4 py-2 font-medium">{{ $item->name }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $item->category->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $item->unit->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ number_format($item->cost_price, 2) }}</td>
                            <td class="px-4 py-2 text-left">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $stock <= 0 ? 'bg-red-100 text-red-800' : ($stock <= ($item->minimum_stock ?? 0) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $stock }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-left font-mono font-bold">{{ number_format($stock * $item->cost_price, 2) }}</td>
                            <td class="px-4 py-2 text-left font-mono text-gray-500">{{ $item->minimum_stock ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">لا توجد أصناف</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-300 bg-gray-100 font-bold">
                        <td colspan="6" class="px-4 py-3">الإجمالي</td>
                        <td class="px-4 py-3 text-left font-mono text-blue-700">{{ number_format($totalValue, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
