<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">الأصناف</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('import.export', 'items') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100 transition" title="تصدير"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg> تصدير</a>
                <a href="{{ route('import.index') }}?type=items" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-300 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100 transition" title="استيراد"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> استيراد</a>
                <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="no-print inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> طباعة</button>
                <a href="{{ route('items.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    إضافة صنف
                </a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو الكود أو الباركود..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="min-w-[180px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">التصنيف</label>
                <select name="category_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[180px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">الوحدة</label>
                <select name="unit_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
            <a href="{{ route('items.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إعادة تعيين</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">صورة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الكود</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">اسم الصنف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">التصنيف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الوحدة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">سعر الشراء</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">سعر البيع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">المخزون</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                @if($item->image)
                                    <img src="{{ asset($item->image) }}" class="h-10 w-10 rounded-lg border object-cover">
                                @else
                                    <div class="h-10 w-10 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">{{ $item->sku ?? '#' . $item->id }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('items.show', $item) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $item->name }}</a>
                                @if($item->barcode)
                                    <div class="text-xs text-gray-500">باركود: {{ $item->barcode }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->category->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->unit->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($item->cost_price, 2) }} <span class="text-xs text-gray-500">{{ $item->purchaseCurrency->symbol ?? $item->purchaseCurrency->code ?? '' }}</span></td>
                            <td class="px-4 py-3 text-left font-mono text-sm text-emerald-600">{{ number_format($item->selling_price, 2) }} <span class="text-xs text-gray-500">{{ $item->salesCurrency->symbol ?? $item->salesCurrency->code ?? '' }}</span></td>
                            <td class="px-4 py-3 text-left">
                                @php $totalStock = $item->warehouses->sum('quantity'); @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $totalStock <= 0 ? 'bg-red-100 text-red-800' : ($totalStock <= ($item->minimum_stock ?? 0) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $totalStock }}
                                </span>
                                @if(($item->opening_stock ?? 0) > 0)
                                    <div class="text-xs text-gray-400 mt-0.5">افتتاحي: {{ number_format($item->opening_stock, 0) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('items.edit', $item) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="تعديل">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1 text-gray-500 hover:bg-red-50 hover:text-red-600 transition cursor-pointer" title="حذف">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">لا توجد أصناف</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $items->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
