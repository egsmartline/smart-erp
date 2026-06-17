<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل أمر بيع - {{ $salesOrder->order_number }}</h2>
            <a href="{{ route('sales-orders.show', $salesOrder) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                العودة
            </a>
        </div>
    </x-slot>

    <form action="{{ route('sales-orders.update', $salesOrder) }}" method="POST" x-data="orderForm()" x-init="init()">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
                <div class="text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات أمر البيع</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">العميل <span class="text-red-500">*</span></label>
                            <select name="customer_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">اختر العميل</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $salesOrder->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">المخزن <span class="text-red-500">*</span></label>
                            <select name="warehouse_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">اختر المخزن</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $salesOrder->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                            <input type="date" name="date" value="{{ old('date', $salesOrder->date->format('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">التاريخ المطلوب</label>
                            <input type="date" name="required_date" value="{{ old('required_date', $salesOrder->required_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">شروط الدفع</label>
                            <select name="payment_term_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">اختر شروط الدفع</option>
                                @foreach($paymentTerms as $term)
                                    <option value="{{ $term->id }}" {{ old('payment_term_id', $salesOrder->payment_term_id) == $term->id ? 'selected' : '' }}>{{ $term->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">العملة</label>
                            <select name="currency_code" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency }}" {{ old('currency_code', $salesOrder->currency_code) == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">الأصناف</h3>
                        <button type="button" @click="addLine()" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
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
                                    <th class="px-3 py-2 font-semibold text-gray-700 text-left">الخصم %</th>
                                    <th class="px-3 py-2 font-semibold text-gray-700 text-left">الضريبة %</th>
                                    <th class="px-3 py-2 font-semibold text-gray-700 text-left">الإجمالي</th>
                                    <th class="px-3 py-2 font-semibold text-gray-700 text-center">حذف</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(line, index) in lines" :key="index">
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-2" x-text="index + 1"></td>
                                        <td class="px-3 py-2">
                                            <select x-model="line.item_id" @change="updateItemPrice(index)" required class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                                <option value="">اختر الصنف</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->sku }})</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" :name="'lines[' + index + '][item_id]'" :value="line.item_id">
                                        </td>
                                        <td class="px-3 py-2 text-left">
                                            <input type="number" x-model.number="line.quantity" :name="'lines[' + index + '][quantity]'" step="0.01" min="0.01" required class="w-24 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="px-3 py-2 text-left">
                                            <input type="number" x-model.number="line.unit_price" :name="'lines[' + index + '][unit_price]'" step="0.01" min="0" required class="w-28 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="px-3 py-2 text-left">
                                            <input type="number" x-model.number="line.discount_percent" :name="'lines[' + index + '][discount_percent]'" step="0.01" min="0" max="100" class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="px-3 py-2 text-left">
                                            <input type="number" x-model.number="line.tax_rate" :name="'lines[' + index + '][tax_rate]'" step="0.01" min="0" max="100" class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="px-3 py-2 text-left font-mono font-medium" x-text="formatNumber(lineTotal(index))"></td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" @click="removeLine(index)" class="rounded p-1 text-gray-500 hover:bg-red-50 hover:text-red-600 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div x-show="lines.length === 0" class="text-center py-8 text-gray-500">
                        لا توجد أصناف مضافة. اضغط "إضافة صنف" للبدء.
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">الملخص</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">المجموع الفرعي</span>
                            <span class="font-mono" x-text="formatNumber(subtotal())"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">الخصم</span>
                            <span class="font-mono text-red-600" x-text="formatNumber(totalDiscount())"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">الضريبة</span>
                            <span class="font-mono text-emerald-600" x-text="formatNumber(totalTax())"></span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between">
                            <span class="font-bold text-gray-800">الإجمالي</span>
                            <span class="font-mono text-lg font-bold text-blue-700" x-text="formatNumber(grandTotal())"></span>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">ملاحظات وشروط</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                            <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="ملاحظات داخلية...">{{ old('notes', $salesOrder->notes) }}</textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">الشروط</label>
                            <textarea name="terms" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="شروط البيع...">{{ old('terms', $salesOrder->terms) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        تحديث أمر البيع
                    </button>
                    <a href="{{ route('sales-orders.show', $salesOrder) }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
                </div>
            </div>
        </div>
    </form>

    <script>
        function orderForm() {
            return {
                lines: [],
                items: @json($items),

                init() {
                    const existingLines = @json($salesOrder->lines);
                    this.lines = existingLines.map(line => ({
                        item_id: line.item_id,
                        quantity: parseFloat(line.quantity),
                        unit_price: parseFloat(line.unit_price),
                        discount_percent: parseFloat(line.discount_percent),
                        tax_rate: parseFloat(line.tax_rate),
                    }));
                },

                addLine() {
                    this.lines.push({
                        item_id: '',
                        quantity: 1,
                        unit_price: 0,
                        discount_percent: 0,
                        tax_rate: 0,
                    });
                },

                removeLine(index) {
                    if (this.lines.length > 1) {
                        this.lines.splice(index, 1);
                    }
                },

                updateItemPrice(index) {
                    const item = this.items.find(i => i.id == this.lines[index].item_id);
                    if (item) {
                        this.lines[index].unit_price = item.selling_price || 0;
                        this.lines[index].tax_rate = item.tax_rate || 0;
                    }
                },

                lineTotal(index) {
                    const line = this.lines[index];
                    const subtotal = line.quantity * line.unit_price;
                    const discount = subtotal * (line.discount_percent / 100);
                    const afterDiscount = subtotal - discount;
                    const tax = afterDiscount * (line.tax_rate / 100);
                    return afterDiscount + tax;
                },

                subtotal() {
                    return this.lines.reduce((sum, line) => {
                        return sum + (line.quantity * line.unit_price);
                    }, 0);
                },

                totalDiscount() {
                    return this.lines.reduce((sum, line) => {
                        const subtotal = line.quantity * line.unit_price;
                        return sum + (subtotal * (line.discount_percent / 100));
                    }, 0);
                },

                totalTax() {
                    return this.lines.reduce((sum, line) => {
                        const subtotal = line.quantity * line.unit_price;
                        const discount = subtotal * (line.discount_percent / 100);
                        const afterDiscount = subtotal - discount;
                        return sum + (afterDiscount * (line.tax_rate / 100));
                    }, 0);
                },

                grandTotal() {
                    return this.subtotal() - this.totalDiscount() + this.totalTax();
                },

                formatNumber(num) {
                    return parseFloat(num || 0).toFixed(2);
                }
            }
        }
    </script>
</x-app-layout>
