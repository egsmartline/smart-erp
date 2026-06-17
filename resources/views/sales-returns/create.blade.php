<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">مرتجع مبيعات جديد</h2>
            <a href="{{ route('sales-returns.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                العودة للقائمة
            </a>
        </div>
    </x-slot>

    <form action="{{ route('sales-returns.store') }}" method="POST" x-data="salesReturnForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">رقم المرتجع</label>
                <input type="text" value="{{ $returnNumber }}" readonly class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">العميل <span class="text-red-500">*</span></label>
                <select name="customer_id" x-model="customerId" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">اختر العميل</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">فاتورة المبيعات <span class="text-red-500">*</span></label>
                <select name="sales_invoice_id" x-model="invoiceId" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">اختر الفاتورة</option>
                    @foreach($invoices as $invoice)
                        <option value="{{ $invoice->id }}" data-customer="{{ $invoice->customer_id }}">{{ $invoice->invoice_number }} - {{ $invoice->customer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">المخزن <span class="text-red-500">*</span></label>
                <select name="warehouse_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">اختر المخزن</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">سبب الإرجاع</label>
                <input type="text" name="reason" placeholder="سبب الإرجاع..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">أصناف المرتجع</h3>
                <button type="button" @click="addLine()" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700 transition cursor-pointer">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    إضافة صنف
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-3 py-2 font-semibold text-gray-700">#</th>
                            <th class="px-3 py-2 font-semibold text-gray-700">الصنف</th>
                            <th class="px-3 py-2 font-semibold text-gray-700 text-left">الكمية</th>
                            <th class="px-3 py-2 font-semibold text-gray-700 text-left">سعر الوحدة</th>
                            <th class="px-3 py-2 font-semibold text-gray-700 text-left">الضريبة %</th>
                            <th class="px-3 py-2 font-semibold text-gray-700 text-left">الإجمالي</th>
                            <th class="px-3 py-2 font-semibold text-gray-700 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(line, index) in lines" :key="index">
                            <tr class="border-b border-gray-100">
                                <td class="px-3 py-2 text-gray-500" x-text="index + 1"></td>
                                <td class="px-3 py-2">
                                    <select :name="'lines['+index+'][item_id]'" x-model="line.item_id" required class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        <option value="">اختر الصنف</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" :name="'lines['+index+'][quantity]'" x-model.number="line.quantity" step="0.01" min="0.01" required class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" :name="'lines['+index+'][unit_price]'" x-model.number="line.unit_price" step="0.01" min="0" required class="w-24 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" :name="'lines['+index+'][tax_rate]'" x-model.number="line.tax_rate" step="0.01" min="0" max="100" class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-3 py-3 text-left font-mono text-sm font-medium text-gray-900" x-text="formatNumber(lineTotal(line))"></td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" @click="removeLine(index)" x-show="lines.length > 1" class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 transition cursor-pointer">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                حفظ المرتجع
            </button>
            <a href="{{ route('sales-returns.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>

    @php
        $selectedInvoiceLines = $selectedInvoice
            ? $selectedInvoice->lines->map(fn($l) => ['item_id' => $l->item_id, 'quantity' => $l->quantity, 'unit_price' => $l->unit_price, 'tax_rate' => $l->tax_rate ?? 15])->toArray()
            : [['item_id' => '', 'quantity' => 1, 'unit_price' => 0, 'tax_rate' => 15]];
    @endphp

    @push('scripts')
    <script>
        function salesReturnForm() {
            return {
                customerId: '{{ $selectedInvoice->customer_id ?? '' }}',
                invoiceId: '{{ $selectedInvoice->id ?? '' }}',
                lines: @json($selectedInvoiceLines),
                addLine() {
                    this.lines.push({ item_id: '', quantity: 1, unit_price: 0, tax_rate: 15 });
                },
                removeLine(index) {
                    if (this.lines.length > 1) this.lines.splice(index, 1);
                },
                lineTotal(line) {
                    const sub = (line.quantity || 0) * (line.unit_price || 0);
                    const tax = sub * ((line.tax_rate || 0) / 100);
                    return sub + tax;
                },
                formatNumber(num) {
                    return num.toFixed(2);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
