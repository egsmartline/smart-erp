<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">حركات المخزون</h2>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">الصنف</label>
                <select name="item_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
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
            <div class="min-w-[160px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">النوع</label>
                <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>شراء</option>
                    <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>بيع</option>
                    <option value="return_in" {{ request('type') == 'return_in' ? 'selected' : '' }}>إدخال مرتجع</option>
                    <option value="return_out" {{ request('type') == 'return_out' ? 'selected' : '' }}>إخراج مرتجع</option>
                    <option value="transfer_in" {{ request('type') == 'transfer_in' ? 'selected' : '' }}>تحويل وارد</option>
                    <option value="transfer_out" {{ request('type') == 'transfer_out' ? 'selected' : '' }}>تحويل صادر</option>
                    <option value="adjustment_in" {{ request('type') == 'adjustment_in' ? 'selected' : '' }}>تسديد</option>
                    <option value="adjustment_out" {{ request('type') == 'adjustment_out' ? 'selected' : '' }}>حسم</option>
                    <option value="opening" {{ request('type') == 'opening' ? 'selected' : '' }}>رصيد افتتاحي</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="min-w-[150px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
            <a href="{{ route('stock-movements.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إعادة تعيين</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الصنف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">المخزن</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">النوع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الكمية</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">تكلفة الوحدة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">التكلفة الإجمالية</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">المرجع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">المستخدم</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $typeLabels = [
                            'purchase' => ['label' => 'شراء', 'color' => 'bg-blue-100 text-blue-800'],
                            'sale' => ['label' => 'بيع', 'color' => 'bg-purple-100 text-purple-800'],
                            'return_in' => ['label' => 'إدخال مرتجع', 'color' => 'bg-cyan-100 text-cyan-800'],
                            'return_out' => ['label' => 'إخراج مرتجع', 'color' => 'bg-amber-100 text-amber-800'],
                            'transfer_in' => ['label' => 'تحويل وارد', 'color' => 'bg-green-100 text-green-800'],
                            'transfer_out' => ['label' => 'تحويل صادر', 'color' => 'bg-orange-100 text-orange-800'],
                            'adjustment_in' => ['label' => 'تسديد', 'color' => 'bg-emerald-100 text-emerald-800'],
                            'adjustment_out' => ['label' => 'حسم', 'color' => 'bg-red-100 text-red-800'],
                            'opening' => ['label' => 'رصيد افتتاحي', 'color' => 'bg-gray-100 text-gray-800'],
                        ];
                        $inTypes = ['purchase', 'return_in', 'transfer_in', 'adjustment_in', 'opening'];
                    @endphp
                    @forelse($stockMovements as $movement)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-600">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $movement->item->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $movement->warehouse->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @php $typeInfo = $typeLabels[$movement->type] ?? ['label' => $movement->type, 'color' => 'bg-gray-100 text-gray-800']; @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeInfo['color'] }}">{{ $typeInfo['label'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <span class="font-mono text-sm {{ in_array($movement->type, $inTypes) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ in_array($movement->type, $inTypes) ? '+' : '-' }}{{ number_format($movement->quantity, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($movement->unit_cost, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($movement->total_cost, 2) }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($movement->reference_type && $movement->reference_id)
                                    {{ class_basename($movement->reference_type) }} #{{ $movement->reference_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $movement->creator->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">لا توجد حركات مخزون</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $stockMovements->links() }}</div>
    </div>
</x-app-layout>
