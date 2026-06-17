<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">دفاتر اليومية</h2>
            <a href="{{ route('journals.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة دفتر
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">
            <div class="min-w-[180px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">نوع الدفتر</label>
                <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>مبيعات</option>
                    <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>مشتريات</option>
                    <option value="cash" {{ request('type') === 'cash' ? 'selected' : '' }}>نقدية</option>
                    <option value="bank" {{ request('type') === 'bank' ? 'selected' : '' }}>بنكية</option>
                    <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>عامة</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                بحث
            </button>
            <a href="{{ route('journals.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                إعادة تعيين
            </a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الكود</th>
                        <th class="px-4 py-3 font-semibold">اسم الدفتر</th>
                        <th class="px-4 py-3 font-semibold">النوع</th>
                        <th class="px-4 py-3 font-semibold">الحساب الافتراضي</th>
                        <th class="px-4 py-3 font-semibold text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journals as $journal)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ $journal->code }}</td>
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('journals.show', $journal) }}" class="text-gray-900 hover:text-blue-600">{{ $journal->name }}</a>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $typeLabels = [
                                        'sale' => ['مبيعات', 'bg-blue-100 text-blue-800'],
                                        'purchase' => ['مشتريات', 'bg-orange-100 text-orange-800'],
                                        'cash' => ['نقدية', 'bg-green-100 text-green-800'],
                                        'bank' => ['بنكية', 'bg-purple-100 text-purple-800'],
                                        'general' => ['عامة', 'bg-gray-100 text-gray-800'],
                                    ];
                                    $label = $typeLabels[$journal->type] ?? ['غير محدد', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $label[1] }}">{{ $label[0] }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $journal->defaultAccount->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($journal->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">نشط</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">غير نشط</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('journals.edit', $journal) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="تعديل">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('journals.destroy', $journal) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدفتر؟')">
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
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد دفاتر يومية</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $journals->links() }}
        </div>
    </div>
</x-app-layout>
