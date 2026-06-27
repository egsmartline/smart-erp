<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">بيان حركة حساب مالي</h2>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">الحساب <span class="text-red-500">*</span></label>
                <select name="account_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">اختر الحساب</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ $accountId == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">من تاريخ</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">عرض</button>
        </form>
    </div>

    @if($accountId)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
                <div class="text-sm text-gray-500">الرصيد الافتتاحي</div>
                <div class="text-xl font-bold font-mono {{ $openingBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    {{ number_format($openingBalance, 2) }}
                </div>
            </div>
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
                <div class="text-sm text-gray-500">إجمالي مدين</div>
                <div class="text-xl font-bold font-mono text-blue-600">{{ number_format($totalDebit, 2) }}</div>
            </div>
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
                <div class="text-sm text-gray-500">إجمالي دائن</div>
                <div class="text-xl font-bold font-mono text-emerald-600">{{ number_format($totalCredit, 2) }}</div>
            </div>
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 text-center">
                <div class="text-sm text-gray-500">الرصيد الختامي</div>
                <div class="text-xl font-bold font-mono {{ $closingBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    {{ number_format($closingBalance, 2) }}
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-300 bg-gray-50">
                            <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">رقم القيد</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">البيان</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-left">مدين</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-left">دائن</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100 bg-gray-50/50 font-bold">
                            <td colspan="3" class="px-4 py-2 text-gray-700">الرصيد الافتتاحي</td>
                            <td class="px-4 py-2 text-left font-mono">-</td>
                            <td class="px-4 py-2 text-left font-mono">-</td>
                            <td class="px-4 py-2 text-left font-mono {{ $openingBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                {{ number_format($openingBalance, 2) }}
                            </td>
                        </tr>
                        @forelse($lines as $line)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $line->journalEntry->date ?? '-' }}</td>
                                <td class="px-4 py-2 font-mono text-xs">{{ $line->journalEntry->entry_number ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $line->description ?? $line->journalEntry->description ?? '-' }}</td>
                                <td class="px-4 py-2 text-left font-mono">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                                <td class="px-4 py-2 text-left font-mono">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                                <td class="px-4 py-2 text-left font-mono font-bold {{ $line->running_balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                    {{ number_format($line->running_balance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد حركات في هذه الفترة</td></tr>
                        @endforelse
                        <tr class="border-t-2 border-gray-300 bg-gray-100 font-bold">
                            <td colspan="3" class="px-4 py-3">الإجمالي</td>
                            <td class="px-4 py-3 text-left font-mono text-blue-700">{{ number_format($totalDebit, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono text-emerald-700">{{ number_format($totalCredit, 2) }}</td>
                            <td class="px-4 py-3 text-left font-mono {{ $closingBalance >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                                {{ number_format($closingBalance, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-12 text-center">
            <p class="text-gray-500">اختر حساباً لعرض بيان حركته</p>
        </div>
    @endif
</x-app-layout>