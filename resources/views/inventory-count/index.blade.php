<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">جرد المخزون</h2>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">بحث بالصنف</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو الكود..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="min-w-[180px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">المخزن</label>
                <select name="warehouse_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
            <a href="{{ route('inventory-count.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إعادة تعيين</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">الكود</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">اسم الصنف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">المخزن</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الكمية الحالية</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الكمية المحجوزة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الكمية المتاحة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">متوسط التكلفة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">القيمة الإجمالية</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalQuantity = 0;
                        $totalReserved = 0;
                        $totalAvailable = 0;
                        $totalValue = 0;
                    @endphp
                    @forelse($inventoryItems as $iw)
                        @php
                            $available = $iw->quantity - $iw->reserved_quantity;
                            $value = $iw->quantity * $iw->average_cost;
                            $totalQuantity += $iw->quantity;
                            $totalReserved += $iw->reserved_quantity;
                            $totalAvailable += $available;
                            $totalValue += $value;
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">{{ $iw->item->sku ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $iw->item->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $iw->warehouse->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($iw->quantity, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm text-amber-600">{{ number_format($iw->reserved_quantity, 2) }}</td>
                            <td class="px-4 py-3 text-left">
                                <span class="font-mono text-sm {{ $available <= 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($available, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($iw->average_cost, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm text-blue-600">{{ number_format($value, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">لا توجد بيانات مخزون</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($inventoryItems->count() > 0)
                    <tfoot>
                        <tr class="border-t-2 border-gray-300 bg-gray-50 font-bold">
                            <td colspan="3" class="px-4 py-3 text-gray-900">الإجمالي</td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($totalQuantity, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm text-amber-600">{{ number_format($totalReserved, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm {{ $totalAvailable <= 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($totalAvailable, 2) }}</td>
                            <td class="px-4 py-3 text-left">-</td>
                            <td class="px-4 py-3 text-left font-mono text-sm text-blue-600">{{ number_format($totalValue, 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        <div class="mt-4">{{ $inventoryItems->links() }}</div>
    </div>
</x-app-layout>
