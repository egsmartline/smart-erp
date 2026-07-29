<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تقرير المخزون</h2>
            <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">طباعة</button>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-row gap-2">
            <div class="flex-1 rounded bg-blue-50 border border-blue-200 p-2 text-center">
                <div class="text-[10px] text-blue-600 mb-0.5">إجمالي قيمة المخزون</div>
                <div class="text-sm md:text-base font-bold text-blue-700">{{ number_format($totalValue, 2) }} ج.م</div>
            </div>
            <div class="flex-1 rounded bg-green-50 border border-green-200 p-2 text-center">
                <div class="text-[10px] text-green-600 mb-0.5">إجمالي المبيعات</div>
                <div class="text-sm md:text-base font-bold text-green-700">{{ number_format($totalSales, 2) }} ج.م</div>
            </div>
            <div class="flex-1 rounded bg-amber-50 border border-amber-200 p-2 text-center">
                <div class="text-[10px] text-amber-600 mb-0.5">إجمالي المشتريات</div>
                <div class="text-sm md:text-base font-bold text-amber-700">{{ number_format($totalPurchases, 2) }} ج.م</div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-1 py-1.5 font-semibold w-[60px]">الكود</th>
                        <th class="px-1 py-1.5 font-semibold w-auto min-w-[280px]">اسم الصنف</th>
                        <th class="px-1 py-1.5 font-semibold w-[80px]">التصنيف</th>
                        <th class="px-1 py-1.5 font-semibold w-[50px]">الوحدة</th>
                        <th class="px-1 py-1.5 font-semibold text-left w-[70px]">سعر الشراء</th>
                        <th class="px-1 py-1.5 font-semibold text-left w-[50px]">الكمية</th>
                        <th class="px-1 py-1.5 font-semibold text-left w-[80px]">القيمة</th>
                        <th class="px-1 py-1.5 font-semibold text-left w-[60px]">حد الطلب</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php $stock = $item->warehouses->sum('quantity'); @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-1 py-1 font-mono text-[10px] w-[60px]">{{ $item->sku ?? '-' }}</td>
                            <td class="px-1 py-1 font-medium w-auto min-w-[280px]">{{ $item->name }}</td>
                            <td class="px-1 py-1 text-gray-600 w-[80px] text-xs">{{ $item->category->name ?? '-' }}</td>
                            <td class="px-1 py-1 text-gray-600 w-[50px] text-xs">{{ $item->unit->name ?? '-' }}</td>
                            <td class="px-1 py-1 text-left font-mono text-xs w-[70px]">{{ number_format($item->cost_price, 2) }}</td>
                            <td class="px-1 py-1 text-left w-[50px]">
                                <span class="inline-flex items-center rounded-full px-1 py-0.5 text-[10px] font-medium {{ $stock <= 0 ? 'bg-red-100 text-red-800' : ($stock <= ($item->minimum_stock ?? 0) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $stock }}
                                </span>
                            </td>
                            <td class="px-1 py-1 text-left font-mono font-bold text-xs w-[80px]">{{ number_format($stock * $item->cost_price, 2) }}</td>
                            <td class="px-1 py-1 text-left font-mono text-gray-500 text-xs w-[60px]">{{ $item->minimum_stock ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-2 py-8 text-center text-gray-500">لا توجد أصناف</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-300 bg-gray-100 font-bold">
                        <td colspan="6" class="px-1 py-1.5 text-xs">الإجمالي</td>
                        <td class="px-1 py-1.5 text-left font-mono text-blue-700 text-xs">{{ number_format($totalValue, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
