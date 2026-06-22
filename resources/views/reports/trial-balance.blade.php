<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">ميزان المراجعة</h2>
            <div class="flex items-center gap-2">
                <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    طباعة
                </button>
            </div>
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

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6" id="report-content">
        <div class="text-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">شركة Smart ERP</h3>
            <h4 class="text-md font-semibold text-gray-600">ميزان المراجعة</h4>
            <p class="text-sm text-gray-500">بتاريخ: {{ \Carbon\Carbon::parse($dateTo)->format('Y/m/d') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">كود الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">اسم الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">مدين</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">دائن</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2 font-mono text-xs">{{ $account->code }}</td>
                            <td class="px-4 py-2">{{ $account->name }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ $account->total_debit > 0 ? number_format($account->total_debit, 2) : '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ $account->total_credit > 0 ? number_format($account->total_credit, 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-300 bg-gray-100 font-bold">
                        <td colspan="2" class="px-4 py-3">الإجمالي</td>
                        <td class="px-4 py-3 text-left font-mono text-blue-700">{{ number_format($totalDebit, 2) }}</td>
                        <td class="px-4 py-3 text-left font-mono text-emerald-700">{{ number_format($totalCredit, 2) }}</td>
                    </tr>
                    @if(abs($totalDebit - $totalCredit) > 0.01)
                    <tr class="bg-red-50">
                        <td colspan="2" class="px-4 py-2 text-red-700 font-bold">فرق</td>
                        <td colspan="2" class="px-4 py-2 text-left font-mono text-red-700 font-bold">{{ number_format(abs($totalDebit - $totalCredit), 2) }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
