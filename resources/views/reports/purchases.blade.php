<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تقرير المشتريات</h2>
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">طباعة</button>
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
        <div class="rounded-xl bg-blue-50 border border-blue-200 p-6 text-center">
            <div class="text-sm text-blue-600 mb-2">إجمالي المشتريات</div>
            <div class="text-2xl font-bold text-blue-700">{{ number_format($totalPurchases, 2) }} ج.م</div>
        </div>
        <div class="rounded-xl bg-orange-50 border border-orange-200 p-6 text-center">
            <div class="text-sm text-orange-600 mb-2">إجمالي الضريبة</div>
            <div class="text-2xl font-bold text-orange-700">{{ number_format($totalTax, 2) }} ج.م</div>
        </div>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-6 text-center">
            <div class="text-sm text-emerald-600 mb-2">عدد الفواتير</div>
            <div class="text-2xl font-bold text-emerald-700">{{ $invoices->count() }}</div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">التاريخ</th>
                        <th class="px-4 py-3 font-semibold">رقم الفاتورة</th>
                        <th class="px-4 py-3 font-semibold">المورد</th>
                        <th class="px-4 py-3 font-semibold text-left">المجموع</th>
                        <th class="px-4 py-3 font-semibold text-left">الضريبة</th>
                        <th class="px-4 py-3 font-semibold text-left">الإجمالي</th>
                        <th class="px-4 py-3 font-semibold">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $invoice->invoice_date?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-4 py-2 font-mono text-xs">{{ $invoice->invoice_number }}</td>
                            <td class="px-4 py-2">{{ $invoice->supplier->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ number_format($invoice->subtotal ?? $invoice->total, 2) }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ number_format($invoice->tax_amount ?? 0, 2) }}</td>
                            <td class="px-4 py-2 text-left font-mono font-bold">{{ number_format($invoice->total, 2) }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $invoice->status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $invoice->status === 'posted' ? 'مرحل' : 'مسودة' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد فواتير</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
