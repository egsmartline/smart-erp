<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">دليل الحسابات</h2>
            <a href="{{ route('accounts.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة حساب
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
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو الكود..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="min-w-[180px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">نوع الحساب</label>
                <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">الكل</option>
                    @foreach($accountTypes as $type => $typeName)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $typeName }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                بحث
            </button>
            <a href="{{ route('accounts.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                إعادة تعيين
            </a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">الكود</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">اسم الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الحساب الأب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">النوع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">{{ $account->code }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-indent-{{ strlen($account->code) > 2 ? '4' : (strlen($account->code) > 1 ? '2' : '0') }}"></span>
                                    <a href="{{ route('accounts.show', $account) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $account->name }}</a>
                                </div>
                                @if($account->name_en)
                                    <div class="text-xs text-gray-500 mr-6">{{ $account->name_en }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $account->parent->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $account->type === 'assets' ? 'bg-blue-100 text-blue-800' : ($account->type === 'liabilities' ? 'bg-red-100 text-red-800' : ($account->type === 'equity' ? 'bg-purple-100 text-purple-800' : ($account->type === 'revenue' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'))) }}">
                                    {{ $accountTypes[$account->type] ?? $account->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-left font-mono text-sm {{ $account->balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ number_format($account->balance, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('accounts.toggle-status', $account) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $account->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition cursor-pointer">
                                        {{ $account->is_active ? 'نشط' : 'غير نشط' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('accounts.edit', $account) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="تعديل">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')">
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
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد حسابات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
