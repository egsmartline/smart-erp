<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">الضرائب</h2>
            <a href="{{ route('taxes.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة ضريبة
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-4 flex items-center gap-3">
            <select name="type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">كل الأنواع</option>
                <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>ثابتة</option>
                <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                <option value="group" {{ request('type') === 'group' ? 'selected' : '' }}>مجموعة</option>
            </select>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">الكود</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الاسم</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">النوع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">النسبة %</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxes as $tax)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-gray-900">{{ $tax->code }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('taxes.show', $tax) }}" class="text-blue-600 hover:text-blue-800">{{ $tax->name }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @switch($tax->type)
                                    @case('fixed') ثابتة @break
                                    @case('percentage') نسبة مئوية @break
                                    @case('group') مجموعة @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold text-gray-900">{{ $tax->rate }}%</td>
                            <td class="px-4 py-3 text-gray-600">{{ $tax->account->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($tax->is_active)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">نشط</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">غير نشط</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('taxes.edit', $tax) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="تعديل">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('taxes.destroy', $tax) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
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
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد ضرائب</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $taxes->links() }}
        </div>
    </div>
</x-app-layout>
