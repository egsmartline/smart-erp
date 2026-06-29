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
        <div class="mb-4 leading-relaxed">
            <span class="text-gray-900 text-sm"><strong>رقم الإذن:</strong> {{ $salesDeliveryNote->delivery_number }}</span>
            <span class="mx-3 text-gray-300">|</span>
            <span class="text-gray-900 text-sm"><strong>التاريخ:</strong> {{ $salesDeliveryNote->date->format('Y/m/d') }}</span>
            <br>
            <span class="text-gray-900 text-sm"><strong>العميل:</strong> {{ $salesDeliveryNote->customer->name ?? '-' }}</span>
            <span class="mx-3 text-gray-300">|</span>
            <span class="text-gray-900 text-sm"><strong>الحالة:</strong>
                @if($salesDeliveryNote->status === 'confirmed')
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">مؤكد</span>
                @elseif($salesDeliveryNote->status === 'draft')
                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">ملغي</span>
                @endif
            </span>
            <br>
            <span class="text-gray-900 text-sm"><strong>المخزن:</strong> {{ $salesDeliveryNote->warehouse->name ?? '-' }}</span>
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
                            <th class="px-4 py-3 font-semibold">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesDeliveryNote->lines as $index => $line)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">{{ $line->item->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ number_format($line->quantity, 2) }}</td>
                                <td class="px-4 py-3">{{ $line->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-center border-t border-gray-200 pt-6 no-print">
            <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">طباعة الإذن</button>
        </div>
    </div>

<style>
    @media print {
        #printArea .no-print { display: none !important; }
        #printArea { padding: 10px !important; margin: 0 !important; box-shadow: none !important; border: none !important; border-radius: 0 !important; }
    }
</style>
</x-app-layout>
