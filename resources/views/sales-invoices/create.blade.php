<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">فاتورة مبيعات جديدة</h2>
            <a href="{{ route('sales-invoices.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                العودة للقائمة
            </a>
        </div>
    </x-slot>

    <form action="{{ route('sales-invoices.store') }}" method="POST">
        @csrf

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">العميل <span class="text-red-500">*</span></label>
                    <select name="customer_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العميل</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}{{ $customer->phone ? ' ('.$customer->phone.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">المخزن <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $warehouses->first()->id ?? '') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">العملة</label>
                    <select name="currency_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العملة</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ $currency->is_default ? 'selected' : '' }}>
                                {{ $currency->name }} - {{ $currency->symbol }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الفاتورة <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الاستحقاق <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="ملاحظات إضافية..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div x-data="invoiceItems()">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">أصناف الفاتورة</h3>
                    <button type="button" @click="addLine()"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700 transition cursor-pointer">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        إضافة صنف
                    </button>
                </div>

                <div class="overflow-x-auto mb-4 rounded-xl border border-gray-200">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-3 py-2 font-semibold text-gray-700 w-8">#</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 min-w-[200px]">الصنف</th>
                                <th class="px-3 py-2 font-semibold text-gray-700">الكمية</th>
                                <th class="px-3 py-2 font-semibold text-gray-700">السعر</th>
                                <th class="px-3 py-2 font-semibold text-gray-700">خصم %</th>
                                <th class="px-3 py-2 font-semibold text-gray-700">الضريبة %</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الإجمالي</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(line, index) in lines" :key="index">
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2 text-gray-500" x-text="index + 1"></td>
                                    <td class="px-3 py-2">
                                        <select :name="'lines[' + index + '][item_id]'" x-model="line.item_id" @change="selectItem(index)" required
                                            class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                            <option value="">اختر صنف</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}"
                                                    data-price="{{ $item->selling_price }}"
                                                    data-tax="{{ $item->tax_rate ?? 15 }}">
                                                    {{ $item->name }} - {{ $item->sku ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'lines[' + index + '][quantity]'" x-model="line.quantity" @input="calcLine(index)" step="0.01" min="0.01" required
                                            class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'lines[' + index + '][unit_price]'" x-model="line.unit_price" @input="calcLine(index)" step="0.01" min="0" required
                                            class="w-24 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'lines[' + index + '][discount_percent]'" x-model="line.discount_percent" @input="calcLine(index)" step="0.01" min="0" max="100"
                                            class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'lines[' + index + '][tax_rate]'" x-model="line.tax_rate" @input="calcLine(index)" step="0.01" min="0" max="100"
                                            class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="px-3 py-3 text-left font-mono text-sm font-medium text-gray-900" x-text="line.total.toFixed(2)"></td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" @click="removeLine(index)" x-show="lines.length > 1"
                                            class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 transition cursor-pointer">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <h4 class="text-sm font-bold text-gray-700 mb-3">الخصومات والشحن</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">خصم إضافي</label>
                                <input type="number" name="discount_amount" x-model="discountAmount" @input="calcTotals" step="0.01" min="0"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">مصاريف شحن</label>
                                <input type="number" name="shipping_amount" x-model="shippingAmount" @input="calcTotals" step="0.01" min="0"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <h4 class="text-sm font-bold text-gray-700 mb-3">ملخص الفاتورة</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">المجموع الفرعي</span>
                                <span class="font-mono font-medium" x-text="subtotal.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">الخصم</span>
                                <span class="font-mono font-medium text-red-600" x-text="'(- ' + totalDiscount.toFixed(2) + ')'"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">الضريبة</span>
                                <span class="font-mono font-medium text-emerald-600" x-text="'(+ ' + totalTax.toFixed(2) + ')'"></span>
                            </div>
                            <template x-if="shippingAmount > 0">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">الشحن</span>
                                    <span class="font-mono font-medium" x-text="'(+ ' + shippingAmount.toFixed(2) + ')'"></span>
                                </div>
                            </template>
                            <div class="border-t border-blue-200 pt-2 flex justify-between">
                                <span class="text-base font-bold text-gray-800">الإجمالي</span>
                                <span class="text-lg font-bold font-mono text-blue-700" x-text="grandTotal.toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <template x-if="lines.length < 1">
                    <div class="mt-4 rounded-lg bg-yellow-50 p-3 text-sm text-yellow-800 border border-yellow-200">
                        <strong>تنبيه:</strong> يجب إضافة صنف واحد على الأقل
                    </div>
                </template>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                حفظ الفاتورة
            </button>
            <a href="{{ route('sales-invoices.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>
</x-app-layout>

<script>
    function invoiceItems() {
        return {
            lines: [{
                item_id: '',
                quantity: 1,
                unit_price: 0,
                discount_percent: 0,
                tax_rate: 15,
                total: 0,
            }],
            discountAmount: 0,
            shippingAmount: 0,
            subtotal: 0,
            totalDiscount: 0,
            totalTax: 0,
            grandTotal: 0,

            addLine() {
                this.lines.push({
                    item_id: '',
                    quantity: 1,
                    unit_price: 0,
                    discount_percent: 0,
                    tax_rate: 15,
                    total: 0,
                });
            },

            removeLine(index) {
                if (this.lines.length > 1) {
                    this.lines.splice(index, 1);
                    this.calcTotals();
                }
            },

            selectItem(index) {
                let select = document.querySelector('[name="lines[' + index + '][item_id]"]');
                if (!select) return;
                let option = select.options[select.selectedIndex];
                if (!option || !option.value) return;
                let price = parseFloat(option.dataset.price) || 0;
                let tax = parseFloat(option.dataset.tax) || 15;
                this.lines[index].unit_price = price;
                this.lines[index].tax_rate = tax;
                this.lines[index].quantity = 1;
                this.calcLine(index);
            },

            calcLine(index) {
                let line = this.lines[index];
                let qty = parseFloat(line.quantity) || 0;
                let price = parseFloat(line.unit_price) || 0;
                let discPct = parseFloat(line.discount_percent) || 0;
                let taxPct = parseFloat(line.tax_rate) || 0;
                let ls = qty * price;
                let ld = ls * (discPct / 100);
                let la = ls - ld;
                let lt = la * (taxPct / 100);
                line.total = la + lt;
                this.calcTotals();
            },

            calcTotals() {
                this.subtotal = 0;
                this.totalDiscount = 0;
                this.totalTax = 0;
                for (let line of this.lines) {
                    let qty = parseFloat(line.quantity) || 0;
                    let price = parseFloat(line.unit_price) || 0;
                    let discPct = parseFloat(line.discount_percent) || 0;
                    let taxPct = parseFloat(line.tax_rate) || 0;
                    let ls = qty * price;
                    let ld = ls * (discPct / 100);
                    let la = ls - ld;
                    let lt = la * (taxPct / 100);
                    this.subtotal += ls;
                    this.totalDiscount += ld;
                    this.totalTax += lt;
                }
                let discAmt = parseFloat(this.discountAmount) || 0;
                let shipAmt = parseFloat(this.shippingAmount) || 0;
                this.grandTotal = this.subtotal - this.totalDiscount - discAmt + this.totalTax + shipAmt;
            },
        };
    }
</script>