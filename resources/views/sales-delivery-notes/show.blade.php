<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إذن تسليم - {{ $salesDeliveryNote->delivery_number }}</h2>
            <div class="flex items-center gap-2">
                <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300 transition no-print">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    طباعة
                </button>
                <a href="{{ route('sales-delivery-notes.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للقائمة</a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6" id="printArea">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">رقم الإذن</label>
                <p class="text-gray-900 text-sm font-bold">{{ $salesDeliveryNote->delivery_number }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">التاريخ</label>
                <p class="text-gray-900 text-sm">{{ $salesDeliveryNote->date->format('Y/m/d') }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">الحالة</label>
                <p class="text-gray-900 text-sm">
                    @if($salesDeliveryNote->status === 'confirmed')
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مؤكد</span>
                    @elseif($salesDeliveryNote->status === 'draft')
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">ملغي</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">العميل</label>
                <p class="text-gray-900 text-sm">{{ $salesDeliveryNote->customer->name ?? '-' }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">المخزن</label>
                <p class="text-gray-900 text-sm">{{ $salesDeliveryNote->warehouse->name ?? '-' }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">بواسطة</label>
                <p class="text-gray-900 text-sm">{{ $salesDeliveryNote->user->name ?? '-' }}</p>
            </div>
            <div class="md:col-span-6">
                <label class="mb-1 block text-sm font-medium text-gray-500">ملاحظات</label>
                <p class="text-gray-900 text-sm">{{ $salesDeliveryNote->notes ?? '-' }}</p>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-sm font-bold text-gray-700 mb-4">الأصناف</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-300 bg-gray-50">
                            <th class="px-4 py-3 font-semibold">#</th>
                            <th class="px-4 py-3 font-semibold">الصنف</th>
                            <th class="px-4 py-3 font-semibold">الكمية</th>
                            <th class="px-4 py-3 font-semibold">سعر الوحدة</th>
                            <th class="px-4 py-3 font-semibold">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesDeliveryNote->lines as $index => $line)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">{{ $line->item->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ number_format($line->quantity, 2) }}</td>
                                <td class="px-4 py-3">{{ number_format($line->unit_price, 2) }}</td>
                                <td class="px-4 py-3 font-bold">{{ number_format($line->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-300 bg-gray-50 font-bold">
                            <td colspan="4" class="px-4 py-3 text-left">الإجمالي</td>
                            <td class="px-4 py-3">{{ number_format($salesDeliveryNote->lines->sum('total'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-center border-t border-gray-200 pt-6 no-print">
            <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">طباعة الإذن</button>
        </div>
    </div>

<style>
    @media print {
        #printArea, #printArea * { display: block !important; }
        #printArea { position: absolute; left: 0; top: 0; width: 100%; padding: 20px !important; margin: 0 !important; box-shadow: none !important; border: none !important; border-radius: 0 !important; }
        #printArea .no-print { display: none !important; }
    }
</style>
</x-app-layout>
