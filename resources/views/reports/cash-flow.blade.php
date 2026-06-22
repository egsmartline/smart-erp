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
            <div class="text-2xl font-bold text-emerald-700">{{ number_format($receipts, 2) }} ج.م</div>
        </div>
        <div class="rounded-xl bg-red-50 border border-red-200 p-6 text-center">
            <div class="text-sm text-red-600 mb-2">التدفقات الصادرة</div>
            <div class="text-2xl font-bold text-red-700">{{ number_format($payments, 2) }} ج.م</div>
        </div>
        <div class="rounded-xl {{ $netCashFlow >= 0 ? 'bg-blue-50 border border-blue-200' : 'bg-orange-50 border border-orange-200' }} p-6 text-center">
            <div class="text-sm {{ $netCashFlow >= 0 ? 'text-blue-600' : 'text-orange-600' }} mb-2">صافي التدفق النقدي</div>
            <div class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-blue-700' : 'text-orange-700' }}">{{ number_format($netCashFlow, 2) }} ج.م</div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="text-center py-6">
            <div class="text-sm text-gray-500 mb-4">من {{ \Carbon\Carbon::parse($dateFrom)->format('Y/m/d') }} إلى {{ \Carbon\Carbon::parse($dateTo)->format('Y/m/d') }}</div>
            <div class="flex items-center justify-center gap-8">
                <div>
                    <div class="text-4xl font-bold {{ $netCashFlow >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($netCashFlow, 2) }}</div>
                    <div class="text-sm text-gray-500 mt-2">صافي التدفق النقدي</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
