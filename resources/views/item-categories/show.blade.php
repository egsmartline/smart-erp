<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات التصنيف: {{ $category->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('item-categories.edit', $category) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('item-categories.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات التصنيف</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الاسم:</span><span class="font-medium">{{ $category->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">التصنيف الأب:</span><span class="font-medium">{{ $category->parent->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الوصف:</span><span class="font-medium">{{ $category->description ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $category->is_active ? 'نشط' : 'غير نشط' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">الأصناف في هذا التصنيف ({{ $category->items->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الكود</th>
                        <th class="px-4 py-3 font-semibold">الاسم</th>
                        <th class="px-4 py-3 font-semibold">الوحدة</th>
                        <th class="px-4 py-3 font-semibold text-left">سعر الشراء</th>
                        <th class="px-4 py-3 font-semibold text-left">سعر البيع</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($category->items as $item)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2 font-mono text-xs">{{ $item->sku ?? '-' }}</td>
                            <td class="px-4 py-2"><a href="{{ route('items.show', $item) }}" class="hover:text-blue-600">{{ $item->name }}</a></td>
                            <td class="px-4 py-2">{{ $item->unit->name ?? '-' }}</td>
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
