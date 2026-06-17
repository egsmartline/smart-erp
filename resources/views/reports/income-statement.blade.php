<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">قائمة الدخل</h2>
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                طباعة
            </button>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
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

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="text-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">قائمة الدخل</h3>
            <p class="text-sm text-gray-500">من {{ \Carbon\Carbon::parse($dateFrom)->format('Y/m/d') }} إلى {{ \Carbon\Carbon::parse($dateTo)->format('Y/m/d') }}</p>
        </div>

        <div class="space-y-6">
            <div>
                <h4 class="text-md font-bold text-emerald-700 mb-3">الإيرادات</h4>
                <table class="w-full text-right text-sm">
                    <tbody>
                        @foreach($revenueAccounts as $account)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-2">{{ $account->code }} - {{ $account->name }}</td>
                                <td class="px-4 py-2 text-left font-mono">{{ number_format($account->balance, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-emerald-50 font-bold">
                            <td class="px-4 py-2">إجمالي الإيرادات</td>
                            <td class="px-4 py-2 text-left font-mono text-emerald-700">{{ number_format($totalRevenue, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <h4 class="text-md font-bold text-red-700 mb-3">المصروفات</h4>
                <table class="w-full text-right text-sm">
                    <tbody>
                        @foreach($expenseAccounts as $account)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-2">{{ $account->code }} - {{ $account->name }}</td>
                                <td class="px-4 py-2 text-left font-mono">{{ number_format($account->balance, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-red-50 font-bold">
                            <td class="px-4 py-2">إجمالي المصروفات</td>
                            <td class="px-4 py-2 text-left font-mono text-red-700">{{ number_format($totalExpenses, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-t-2 border-gray-300 pt-4">
                <div class="flex items-center justify-between rounded-xl {{ $netIncome >= 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200' }} p-4">
                    <span class="text-lg font-bold {{ $netIncome >= 0 ? 'text-emerald-800' : 'text-red-800' }}">صافي الدخل</span>
                    <span class="text-lg font-bold {{ $netIncome >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ number_format($netIncome, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
