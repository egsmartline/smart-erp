<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تسوية المخزون: {{ $adj->reference }}</h2>
            <div class="flex items-center gap-2">
                @if($adj->state == 'draft')
                    <form action="{{ route('inventory-adjustments.confirm', $adj) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من التأكيد؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">تأكيد</button>
                    </form>
                    <form action="{{ route('inventory-adjustments.cancel', $adj) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الإلغاء؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 transition">إلغاء</button>
                    </form>
                @endif
                <a href="{{ route('inventory-adjustments.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات التسوية</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">المرجع:</span><span class="font-medium font-mono">{{ $adj->reference }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">التاريخ:</span><span class="font-medium">{{ $adj->adjustment_date->format('Y/m/d') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المخزن:</span><span class="font-medium">{{ $adj->warehouse->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    @if($adj->state == 'draft')
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                    @elseif($adj->state == 'done')
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مؤكدة</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">ملغاة</span>
                    @endif
                </div>
                <div class="flex justify-between"><span class="text-gray-500">أنشأه:</span><span class="font-medium">{{ $adj->creator->name ?? '-' }}</span></div>
                @if($adj->notes)
                    <div class="flex justify-between"><span class="text-gray-500">ملاحظات:</span><span class="font-medium">{{ $adj->notes }}</span></div>
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">بنود التسوية</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الصنف</th>
                        <th class="px-4 py-3 font-semibold text-center">الكمية النظرية</th>
                        <th class="px-4 py-3 font-semibold text-center">الكمية الفعلية</th>
                        <th class="px-4 py-3 font-semibold text-center">الفرق</th>
                        <th class="px-4 py-3 font-semibold">السبب</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adj->lines as $line)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $line->item->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $line->theoretical_qty }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $line->actual_qty }}</td>
                            <td class="px-4 py-3 text-center font-mono {{ $line->difference > 0 ? 'text-green-600' : ($line->difference < 0 ? 'text-red-600' : '') }}">
                                {{ $line->difference > 0 ? '+' : '' }}{{ $line->difference }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $line->reason ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا توجد بنود</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
