<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">التحويل: {{ $transfer->reference }}</h2>
            <div class="flex items-center gap-2">
                @if($transfer->state == 'draft')
                    <form action="{{ route('stock-transfers.confirm', $transfer) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد التحويل؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تأكيد</button>
                    </form>
                @endif
                @if($transfer->state == 'confirmed')
                    <form action="{{ route('stock-transfers.done', $transfer) }}" method="POST" class="inline" onsubmit="return confirm('إتمام التحويل؟ سيتم تعديل كمية المخزون')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">إتمام</button>
                    </form>
                @endif
                @if($transfer->state == 'draft')
                    <form action="{{ route('stock-transfers.cancel', $transfer) }}" method="POST" class="inline" onsubmit="return confirm('إلغاء التحويل؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 transition">إلغاء</button>
                    </form>
                @endif
                <a href="{{ route('stock-transfers.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات التحويل</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">المرجع:</span><span class="font-medium font-mono">{{ $transfer->reference }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">التاريخ:</span><span class="font-medium">{{ $transfer->transfer_date->format('Y/m/d') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">من مخزن:</span><span class="font-medium">{{ $transfer->sourceWarehouse->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">إلى مخزن:</span><span class="font-medium">{{ $transfer->destinationWarehouse->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    @if($transfer->state == 'draft')
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                    @elseif($transfer->state == 'confirmed')
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">مؤكدة</span>
                    @elseif($transfer->state == 'done')
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">منجزة</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">ملغاة</span>
                    @endif
                </div>
                <div class="flex justify-between"><span class="text-gray-500">أنشأه:</span><span class="font-medium">{{ $transfer->creator->name ?? '-' }}</span></div>
                @if($transfer->notes)
                    <div class="flex justify-between"><span class="text-gray-500">ملاحظات:</span><span class="font-medium">{{ $transfer->notes }}</span></div>
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">بنود التحويل</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الصنف</th>
                        <th class="px-4 py-3 font-semibold text-center">الكمية</th>
                        <th class="px-4 py-3 font-semibold">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfer->lines as $line)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $line->item->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $line->quantity }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $line->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">لا توجد بنود</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
