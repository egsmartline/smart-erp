<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">أرصدة الخزائن والحسابات البنكية</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Summary per Currency -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($allCurrencies as $currencyCode)
                <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                    <p class="text-sm font-medium text-gray-500">إجمالي {{ $currencyCode }}</p>
                    <div class="mt-2 space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">الخزائن:</span>
                            <span class="font-mono font-medium text-blue-600">{{ number_format($treasuryByCurrency->get($currencyCode, 0), 2) }} {{ $currencyCode }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">الحسابات البنكية:</span>
                            <span class="font-mono font-medium text-emerald-600">{{ number_format($bankByCurrency->get($currencyCode, 0), 2) }} {{ $currencyCode }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-1 mt-1">
                            <span class="font-semibold text-gray-800">الإجمالي:</span>
                            <span class="font-mono font-bold text-gray-900">{{ number_format($treasuryByCurrency->get($currencyCode, 0) + $bankByCurrency->get($currencyCode, 0), 2) }} {{ $currencyCode }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Cash Treasuries -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800">الخزائن النقدية</h3>
            </div>
            <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 font-semibold text-gray-700">الاسم</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">الكود</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">الحساب المحاسبي</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">العملة</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد الافتتاحي</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد الحالي</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($treasuries as $treasury)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $treasury->name }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $treasury->code }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $treasury->account->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $treasury->currency->code ?? 'ج.م' }}</td>
                                    <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($treasury->opening_balance ?? 0, 2) }} {{ $treasury->currency->code ?? 'ج.م' }}</td>
                                    <td class="px-4 py-3 text-left font-mono text-sm font-bold {{ $treasury->current_balance > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ number_format($treasury->current_balance, 2) }} {{ $treasury->currency->code ?? 'ج.م' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('cash-treasuries.show', $treasury) }}" class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 transition" title="عرض">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد خزائن</td></tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>
        </div>

        <!-- Bank Accounts -->
        <div class="rounded-xl bg-white shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800">الحسابات البنكية</h3>
            </div>
            <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 font-semibold text-gray-700">البنك</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">اسم الحساب</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">رقم الحساب</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">العملة</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد الافتتاحي</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد الحالي</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bankAccounts as $account)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $account->bank_name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $account->account_name }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $account->account_number }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $account->currency->code ?? 'ج.م' }}</td>
                                    <td class="px-4 py-3 text-left font-mono text-sm">{{ number_format($account->opening_balance ?? 0, 2) }} {{ $account->currency->code ?? 'ج.م' }}</td>
                                    <td class="px-4 py-3 text-left font-mono text-sm font-bold {{ $account->current_balance > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ number_format($account->current_balance, 2) }} {{ $account->currency->code ?? 'ج.م' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('bank-accounts.show', $account) }}" class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 transition" title="عرض">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد حسابات بنكية</td></tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</x-app-layout>
