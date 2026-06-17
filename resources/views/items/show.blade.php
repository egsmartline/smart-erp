<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات الصنف: {{ $item->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('items.edit', $item) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للقائمة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات الصنف</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الاسم:</span><span class="font-medium">{{ $item->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الكود:</span><span class="font-medium font-mono">{{ $item->sku ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الباركود:</span><span class="font-medium font-mono">{{ $item->barcode ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">التصنيف:</span><span class="font-medium">{{ $item->category->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الوحدة:</span><span class="font-medium">{{ $item->unit->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">سعر الشراء:</span><span class="font-medium">{{ number_format($item->cost_price, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">سعر البيع:</span><span class="font-medium text-emerald-600">{{ number_format($item->selling_price, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الضريبة:</span><span class="font-medium">{{ $item->tax_rate }}%</span></div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">المخزون</h3>
            @php $totalStock = $item->warehouses->sum('quantity'); @endphp
            <div class="text-center py-4">
                <div class="text-3xl font-bold {{ $totalStock <= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $totalStock }}</div>
                <div class="text-sm text-gray-500 mt-1">إجمالي المخزون</div>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الحد الأدنى:</span><span class="font-medium">{{ $item->minimum_stock ?? 0 }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحد الأقصى:</span><span class="font-medium">{{ $item->maximum_stock ?? 0 }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">إعادة الطلب:</span><span class="font-medium">{{ $item->reorder_level ?? 0 }}</span></div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">المخزون حسب المخازن</h3>
            <div class="space-y-3">
                @forelse($item->warehouses as $wh)
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3">
                        <div>
                            <div class="font-medium text-sm">{{ $wh->warehouse->name ?? 'غير محدد' }}</div>
                            <div class="text-xs text-gray-500">متوسط التكلفة: {{ number_format($wh->average_cost, 2) }}</div>
                        </div>
                        <div class="text-lg font-bold {{ $wh->quantity <= 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $wh->quantity }}</div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا يوجد مخزون في أي مخزن</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
