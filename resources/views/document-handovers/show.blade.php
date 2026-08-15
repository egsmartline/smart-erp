<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">حافظة مستندات: {{ $documentHandover->handover_number }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('document-handovers.print', $documentHandover) }}" target="_blank"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm6-14V5a2 2 0 00-2-2H9a2 2 0 00-2 2v5h10V5z"/></svg>
                    طباعة الحافظة
                </a>
                <a href="{{ route('document-handovers.edit', $documentHandover) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('document-handovers.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات الحافظة</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500">رقم الحافظة:</span><span class="mr-2 font-medium font-mono">{{ $documentHandover->handover_number }}</span></div>
            <div><span class="text-gray-500">تاريخ الاستلام:</span><span class="mr-2 font-medium">{{ $documentHandover->date->format('Y-m-d') }}</span></div>
            <div><span class="text-gray-500">رقم الشحنة:</span><span class="mr-2 font-medium">{{ $documentHandover->shipment_ref ?? '-' }}</span></div>
            <div><span class="text-gray-500">المستلم:</span><span class="mr-2 font-medium">{{ $documentHandover->receiver_name ?? '-' }}</span></div>
            <div><span class="text-gray-500">بصفته:</span><span class="mr-2 font-medium">{{ $documentHandover->receiver_role ?? '-' }}</span></div>
            <div><span class="text-gray-500">تابع لشركة / جهة:</span><span class="mr-2 font-medium">{{ $documentHandover->receiver_entity ?? '-' }}</span></div>
            <div><span class="text-gray-500">رقم بطاقة الرقم القومي:</span><span class="mr-2 font-medium">{{ $documentHandover->receiver_id_card ?? '-' }}</span></div>
            <div><span class="text-gray-500">الصادرة من:</span><span class="mr-2 font-medium">{{ $documentHandover->receiver_id_issuer ?? '-' }}</span></div>
            <div><span class="text-gray-500">العنوان:</span><span class="mr-2 font-medium">{{ $documentHandover->receiver_address ?? '-' }}</span></div>
            <div><span class="text-gray-500">من شركة:</span><span class="mr-2 font-medium">{{ $documentHandover->company_name ?? '-' }}</span></div>
            @if($documentHandover->notes)
                <div class="col-span-2 md:col-span-3"><span class="text-gray-500">ملاحظات:</span><span class="mr-2 font-medium">{{ $documentHandover->notes }}</span></div>
            @endif
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">بيان المستندات المسلمة ({{ $documentHandover->items->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center" style="width:40px">تسليم</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center" style="width:50px">#</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">اسم المستند</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">عدد النسخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentHandover->items as $index => $item)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3 text-center">
                                @if($item->selected)
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></span>
                                @else
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->document_name }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->copies }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا توجد مستندات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>