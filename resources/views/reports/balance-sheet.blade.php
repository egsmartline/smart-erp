<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">الميزانية العمومية</h2>
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                طباعة
            </button>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">عرض</button>
        </form>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="text-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">الميزانية العمومية</h3>
            <p class="text-sm text-gray-500">بتاريخ: {{ \Carbon\Carbon::parse($dateTo)->format('Y/m/d') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div>
                <h4 class="text-md font-bold text-blue-700 mb-3">الأصول</h4>
                <table class="w-full text-right text-sm">
                    <tbody>
                        @foreach($assetAccounts as $account)
                            <tr class="border-b border-gray-100">
                                <td class="px-3 py-2 text-xs">{{ $account->code }} - {{ $account->name }}</td>
                                <td class="px-3 py-2 text-left font-mono text-xs">{{ number_format($account->balance, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-blue-50 font-bold">
                            <td class="px-3 py-2">إجمالي الأصول</td>
                            <td class="px-3 py-2 text-left font-mono text-blue-700">{{ number_format($totalAssets, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <h4 class="text-md font-bold text-red-700 mb-3">الخصوم</h4>
                <table class="w-full text-right text-sm">
                    <tbody>
                        @foreach($liabilityAccounts as $account)
                            <tr class="border-b border-gray-100">
                                <td class="px-3 py-2 text-xs">{{ $account->code }} - {{ $account->name }}</td>
                                <td class="px-3 py-2 text-left font-mono text-xs">{{ number_format($account->balance, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-red-50 font-bold">
                            <td class="px-3 py-2">إجمالي الخصوم</td>
                            <td class="px-3 py-2 text-left font-mono text-red-700">{{ number_format($totalLiabilities, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <h4 class="text-md font-bold text-purple-700 mb-3">حقوق الملكية</h4>
                <table class="w-full text-right text-sm">
                    <tbody>
                        @foreach($equityAccounts as $account)
                            <tr class="border-b border-gray-100">
                                <td class="px-3 py-2 text-xs">{{ $account->code }} - {{ $account->name }}</td>
                                <td class="px-3 py-2 text-left font-mono text-xs">{{ number_format($account->balance, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-purple-50 font-bold">
                            <td class="px-3 py-2">إجمالي حقوق الملكية</td>
                            <td class="px-3 py-2 text-left font-mono text-purple-700">{{ number_format($totalEquity, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border-t-2 border-gray-300 mt-6 pt-4">
            <div class="flex items-center justify-between rounded-xl bg-gray-100 p-4">
                <div class="text-center">
                    <div class="text-sm text-gray-500">الأصول</div>
                    <div class="text-lg font-bold text-blue-700">{{ number_format($totalAssets, 2) }}</div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-500">الخصوم + حقوق الملكية</div>
                    <div class="text-lg font-bold text-purple-700">{{ number_format($totalLiabilities + $totalEquity, 2) }}</div>
                </div>
            </div>
            @if(abs($totalAssets - ($totalLiabilities + $totalEquity)) > 0.01)
                <div class="text-center mt-2 text-sm text-red-600 font-bold">تنبيه: الميزانية غير متوازنة بفرق {{ number_format(abs($totalAssets - ($totalLiabilities + $totalEquity)), 2) }}</div>
            @else
                <div class="text-center mt-2 text-sm text-emerald-600 font-bold">الميزانية متوازنة</div>
            @endif
        </div>
    </div>
</x-app-layout>
