<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">كشف حساب العملاء</h2>
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">طباعة</button>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">العميل</label>
                <select name="customer_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">اختر العميل</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
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
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">التاريخ</th>
                        <th class="px-4 py-3 font-semibold">رقم الفاتورة</th>
                        <th class="px-4 py-3 font-semibold">العميل</th>
                        <th class="px-4 py-3 font-semibold text-left">الإجمالي</th>
                        <th class="px-4 py-3 font-semibold text-left">المدفوع</th>
                        <th class="px-4 py-3 font-semibold text-left">المتبقي</th>
                        <th class="px-4 py-3 font-semibold">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $invoice->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 font-mono text-xs">{{ $invoice->invoice_number }}</td>
                            <td class="px-4 py-2">{{ $invoice->customer->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ number_format($invoice->total, 2) }}</td>
                            <td class="px-4 py-2 text-left font-mono text-emerald-600">{{ number_format($invoice->paid_amount, 2) }}</td>
                            <td class="px-4 py-2 text-left font-mono text-red-600">{{ number_format($invoice->due_amount, 2) }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $invoice->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $invoice->payment_status === 'paid' ? 'مدفوع' : ($invoice->payment_status === 'partial' ? 'جزئي' : 'غير مدفوع') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">اختر عميلاً لعرض البيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
