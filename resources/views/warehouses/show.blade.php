<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيانات المخزن: {{ $warehouse->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('warehouses.edit', $warehouse) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
                <a href="{{ route('warehouses.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات المخزن</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الاسم:</span><span class="font-medium">{{ $warehouse->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الكود:</span><span class="font-medium font-mono">{{ $warehouse->code ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">العنوان:</span><span class="font-medium">{{ $warehouse->address ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المدينة:</span><span class="font-medium">{{ $warehouse->city ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المدير:</span><span class="font-medium">{{ $warehouse->manager_name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">هاتف المدير:</span><span class="font-medium">{{ $warehouse->manager_phone ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $warehouse->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">الأصناف في هذا المخزن</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الصنف</th>
                        <th class="px-4 py-3 font-semibold">الكمية</th>
                        <th class="px-4 py-3 font-semibold text-left">متوسط التكلفة</th>
                        <th class="px-4 py-3 font-semibold text-left">القيمة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouse->items as $whItem)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2"><a href="{{ route('items.show', $whItem->item) }}" class="hover:text-blue-600">{{ $whItem->item->name ?? '-' }}</a></td>
                            <td class="px-4 py-2 font-mono">{{ $whItem->quantity }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ number_format($whItem->average_cost, 2) }}</td>
                            <td class="px-4 py-2 text-left font-mono font-bold">{{ number_format($whItem->quantity * $whItem->average_cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">لا توجد أصناف في هذا المخزن</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
