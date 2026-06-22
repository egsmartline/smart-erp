<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">كشف حساب الموردين</h2>
            <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">طباعة</button>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-sm font-medium text-gray-700">المورد</label>
                <select name="supplier_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">اختر المورد</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $supplierId == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
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
                        <th class="px-4 py-3 font-semibold">المورد</th>
                        <th class="px-4 py-3 font-semibold text-left">الإجمالي</th>
                        <th class="px-4 py-3 font-semibold text-left">المدفوع</th>
                        <th class="px-4 py-3 font-semibold text-left">المتبقي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $invoice->invoice_date?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-4 py-2 font-mono text-xs">{{ $invoice->invoice_number }}</td>
                            <td class="px-4 py-2">{{ $invoice->supplier->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-left font-mono">{{ number_format($invoice->total, 2) }}</td>
                            <td class="px-4 py-2 text-left font-mono text-emerald-600">{{ number_format($invoice->paid_amount ?? 0, 2) }}</td>
                            <td class="px-4 py-2 text-left font-mono text-red-600">{{ number_format($invoice->due_amount ?? $invoice->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">اختر مورداً لعرض البيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
