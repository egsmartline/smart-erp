<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تقرير VAT (القيمة المضافة)</h2>
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

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="text-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">تقرير القيمة المضافة (VAT)</h3>
            <p class="text-sm text-gray-500">من {{ \Carbon\Carbon::parse($dateFrom)->format('Y/m/d') }} إلى {{ \Carbon\Carbon::parse($dateTo)->format('Y/m/d') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="rounded-xl bg-blue-50 border border-blue-200 p-6 text-center">
                <div class="text-sm text-blue-600 mb-2">ضريبة المبيعات (مجمعة)</div>
                <div class="text-2xl font-bold text-blue-700">{{ number_format($salesTax, 2) }}</div>
            </div>
            <div class="rounded-xl bg-orange-50 border border-orange-200 p-6 text-center">
                <div class="text-sm text-orange-600 mb-2">ضريبة المشتريات (مستحقة)</div>
                <div class="text-2xl font-bold text-orange-700">{{ number_format($purchaseTax, 2) }}</div>
            </div>
            <div class="rounded-xl {{ $netVat >= 0 ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200' }} p-6 text-center">
                <div class="text-sm {{ $netVat >= 0 ? 'text-red-600' : 'text-emerald-600' }} mb-2">{{ $netVat >= 0 ? 'صافي VAT المستحق للدفع' : 'صافي VAT المستحق استرداده' }}</div>
                <div class="text-2xl font-bold {{ $netVat >= 0 ? 'text-red-700' : 'text-emerald-700' }}">{{ number_format(abs($netVat), 2) }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
