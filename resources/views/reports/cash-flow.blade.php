<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">التدفقات النقدية</h2>
            <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">طباعة</button>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">من</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">إلى</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">عرض</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-6 text-center">
            <div class="text-sm text-emerald-600 mb-2">التدفقات الواردة</div>
            <div class="text-2xl font-bold text-emerald-700">{{ number_format($totalReceipts, 2) }} ج.م</div>
        </div>
        <div class="rounded-xl bg-red-50 border border-red-200 p-6 text-center">
            <div class="text-sm text-red-600 mb-2">التدفقات الصادرة</div>
            <div class="text-2xl font-bold text-red-700">{{ number_format($totalPayments, 2) }} ج.م</div>
        </div>
        <div class="rounded-xl {{ $netCashFlow >= 0 ? 'bg-blue-50 border border-blue-200' : 'bg-orange-50 border border-orange-200' }} p-6 text-center">
            <div class="text-sm {{ $netCashFlow >= 0 ? 'text-blue-600' : 'text-orange-600' }} mb-2">صافي التدفق النقدي</div>
            <div class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-blue-700' : 'text-orange-700' }}">{{ number_format($netCashFlow, 2) }} ج.م</div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">التاريخ</th>
                        <th class="px-4 py-3 font-semibold">البيان</th>
                        <th class="px-4 py-3 font-semibold">رقم السند</th>
                        <th class="px-4 py-3 font-semibold">الطرف</th>
                        <th class="px-4 py-3 font-semibold">الخزينة / البنك</th>
                        <th class="px-4 py-3 font-semibold text-left">وارد</th>
                        <th class="px-4 py-3 font-semibold text-left">صادر</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $tx->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $tx->type === 'receipt' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $tx->type === 'receipt' ? 'قبض' : 'صرف' }}
                                </span>
                                @if($tx->payment_method)
                                    <span class="text-xs text-gray-500 mr-1">({{ $tx->payment_method }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 font-mono text-xs">{{ $tx->payment_number }}</td>
                            <td class="px-4 py-2">{{ $tx->customer?->name ?? $tx->supplier?->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $tx->treasury?->name ?? $tx->bankAccount?->account_name ?? '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono text-emerald-600">{{ $tx->type === 'receipt' ? number_format($tx->amount_in_currency, 2) : '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono text-red-600">{{ $tx->type === 'payment' ? number_format($tx->amount_in_currency, 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد معاملات في هذه الفترة</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold border-t-2 border-gray-300">
                        <td colspan="5" class="px-4 py-3 text-center">الإجمالي</td>
                        <td class="px-4 py-3 text-left font-mono text-emerald-700">{{ number_format($totalReceipts, 2) }}</td>
                        <td class="px-4 py-3 text-left font-mono text-red-700">{{ number_format($totalPayments, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>