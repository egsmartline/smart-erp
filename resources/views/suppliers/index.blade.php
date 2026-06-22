<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">الموردين</h2>
            <div class="flex items-center gap-2">
                <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="no-print inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> طباعة</button>
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    إضافة مورد
                </a>
            </div>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد أو الهاتف..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
            <a href="{{ route('suppliers.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إعادة تعيين</a>
        </form>

        <div class="mb-4 flex items-center gap-4">
            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-2">
                <span class="text-sm text-red-700">إجمالي أرصدة الموردين: </span>
                <span class="text-sm font-bold text-red-800">{{ number_format($totalBalance, 2) }} ج.م</span>
            </div>
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-2">
                <span class="text-sm text-emerald-700">عدد الموردين: </span>
                <span class="text-sm font-bold text-emerald-800">{{ $suppliers->total() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">الكود</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">اسم المورد</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">البريد الإلكتروني</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الهاتف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">#{{ $supplier->id }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $supplier->name }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $supplier->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $supplier->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm {{ $supplier->balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ number_format($supplier->balance, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $supplier->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $supplier->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="تعديل">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟')">
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
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">لا يوجد موردين</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $suppliers->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
