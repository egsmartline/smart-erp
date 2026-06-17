<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات الوحدة: {{ $unit->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('item-units.edit', $unit) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('item-units.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات الوحدة</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الاسم:</span><span class="font-medium">{{ $unit->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الرمز:</span><span class="font-medium font-mono">{{ $unit->symbol ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الوحدة الأساسية:</span><span class="font-medium">{{ $unit->baseUnit->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">معامل التحويل:</span><span class="font-medium font-mono">{{ $unit->conversion_factor }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $unit->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $unit->is_active ? 'نشط' : 'غير نشط' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">الأصناف بهذه الوحدة ({{ $unit->items->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الكود</th>
                        <th class="px-4 py-3 font-semibold">الاسم</th>
                        <th class="px-4 py-3 font-semibold">التصنيف</th>
                        <th class="px-4 py-3 font-semibold text-left">سعر الشراء</th>
                        <th class="px-4 py-3 font-semibold text-left">سعر البيع</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unit->items as $item)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2 font-mono text-xs">{{ $item->sku ?? '-' }}</td>
                            <td class="px-4 py-2"><a href="{{ route('items.show', $item) }}" class="hover:text-blue-600">{{ $item->name }}</a></td>
                            <td class="px-4 py-2">{{ $item->category->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ number_format($item->cost_price, 2) }}</td>
                            <td class="px-4 py-2 text-left font-mono text-emerald-600">{{ number_format($item->selling_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا توجد أصناف</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
