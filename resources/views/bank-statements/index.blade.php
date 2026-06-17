<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">كشوف الحسابات البنكية</h2>
            <a href="{{ route('bank-statements.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة كشف حساب
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
        <form method="GET" class="mb-4 flex items-center gap-3">
            <select name="bank_account_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">كل الحسابات البنكية</option>
                @foreach($bankAccounts as $account)
                    <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>
                        {{ $account->bank_name }} - {{ $account->account_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">رقم الكشف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الحساب البنكي</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد الافتتاحي</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد الختامي</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الفرق</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bankStatements as $statement)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <a href="{{ route('bank-statements.show', $statement) }}" class="text-blue-600 hover:text-blue-800 font-mono text-xs">
                                    {{ $statement->statement_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $statement->bankAccount->account_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $statement->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm text-gray-900">{{ number_format($statement->start_balance, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold text-gray-900">{{ number_format($statement->end_balance, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm font-bold {{ $statement->balance_difference >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ number_format($statement->balance_difference, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @switch($statement->state)
                                    @case('draft')
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                                        @break
                                    @case('posted')
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">مرحل</span>
                                        @break
                                    @case('reconciled')
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">موفق</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('bank-statements.show', $statement) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="عرض">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($statement->state === 'draft')
                                        <form action="{{ route('bank-statements.destroy', $statement) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded p-1 text-gray-500 hover:bg-red-50 hover:text-red-600 transition cursor-pointer" title="حذف">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">لا توجد كشوف حسابات بنكية</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $bankStatements->links() }}
        </div>
    </div>
</x-app-layout>
