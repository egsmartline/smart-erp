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

            <div id="invoice-items">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">أصناف الفاتورة</h3>
                    <button type="button" onclick="addLine()"
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
                        <tbody id="lines-tbody"></tbody>
                    </table>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <h4 class="text-sm font-bold text-gray-700 mb-3">الخصومات والشحن</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">خصم إضافي</label>
                                <input type="number" id="discount-amount" name="discount_amount" value="0" step="0.01" min="0" oninput="calcTotals()"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">مصاريف شحن</label>
                                <input type="number" id="shipping-amount" name="shipping_amount" value="0" step="0.01" min="0" oninput="calcTotals()"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <h4 class="text-sm font-bold text-gray-700 mb-3">ملخص الفاتورة</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">المجموع الفرعي</span>
                                <span class="font-mono font-medium" id="tot-subtotal">0.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">الخصم</span>
                                <span class="font-mono font-medium text-red-600" id="tot-discount">- 0.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">الضريبة</span>
                                <span class="font-mono font-medium text-emerald-600" id="tot-tax">+ 0.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">الشحن</span>
                                <span class="font-mono font-medium" id="tot-shipping">+ 0.00</span>
                            </div>
                            <div class="border-t border-blue-200 pt-2 flex justify-between">
                                <span class="text-base font-bold text-gray-800">الإجمالي</span>
                                <span class="text-lg font-bold font-mono text-blue-700" id="tot-grand">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
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
    var lineIdx = 0;
    var items = @json($items->map(fn($i) => [
        'id' => $i->id,
        'name' => $i->name,
        'sku' => $i->sku,
        'price' => $i->selling_price,
        'tax' => $i->tax_rate ?? 15,
    ]));

    function addLine() {
        var idx = lineIdx++;
        var tbody = document.getElementById('lines-tbody');
        var tr = document.createElement('tr');
        tr.className = 'border-b border-gray-100';
        tr.innerHTML =
            '<td class="px-3 py-2 text-gray-500">' + (idx + 1) + '</td>' +
            '<td class="px-3 py-2"><select name="lines[' + idx + '][item_id]" onchange="selectItem(this,' + idx + ')" required class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">' +
            '<option value="">اختر صنف</option>' +
            items.map(function(i) {
                return '<option value="' + i.id + '" data-price="' + i.price + '" data-tax="' + i.tax + '">' + i.name + ' - ' + (i.sku || '') + '</option>';
            }).join('') +
            '</select></td>' +
            '<td class="px-3 py-2"><input type="number" name="lines[' + idx + '][quantity]" value="1" step="0.01" min="0.01" required oninput="calcLine(' + idx + ')" class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></td>' +
            '<td class="px-3 py-2"><input type="number" name="lines[' + idx + '][unit_price]" value="0" step="0.01" min="0" required oninput="calcLine(' + idx + ')" class="w-24 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></td>' +
            '<td class="px-3 py-2"><input type="number" name="lines[' + idx + '][discount_percent]" value="0" step="0.01" min="0" max="100" oninput="calcLine(' + idx + ')" class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></td>' +
            '<td class="px-3 py-2"><input type="number" name="lines[' + idx + '][tax_rate]" value="15" step="0.01" min="0" max="100" oninput="calcLine(' + idx + ')" class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></td>' +
            '<td class="px-3 py-3 text-left font-mono text-sm font-medium text-gray-900 line-total">0.00</td>' +
            '<td class="px-3 py-2 text-center"><button type="button" onclick="removeLine(this,' + idx + ')" class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 transition cursor-pointer"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>';
        tbody.appendChild(tr);
    }

    function selectItem(select, idx) {
        var opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) return;
        var price = parseFloat(opt.dataset.price) || 0;
        var tax = parseFloat(opt.dataset.tax) || 15;
        var tr = select.closest('tr');
        tr.querySelector('[name="lines[' + idx + '][unit_price]"]').value = price;
        tr.querySelector('[name="lines[' + idx + '][tax_rate]"]').value = tax;
        tr.querySelector('[name="lines[' + idx + '][quantity]"]').value = 1;
        calcLine(idx);
    }

    function calcLine(idx) {
        var tr = document.querySelector('[name="lines[' + idx + '][quantity]"]');
        if (!tr) return;
        tr = tr.closest('tr');
        var qty = parseFloat(tr.querySelector('[name="lines[' + idx + '][quantity]"]').value) || 0;
        var price = parseFloat(tr.querySelector('[name="lines[' + idx + '][unit_price]"]').value) || 0;
        var discPct = parseFloat(tr.querySelector('[name="lines[' + idx + '][discount_percent]"]').value) || 0;
        var taxPct = parseFloat(tr.querySelector('[name="lines[' + idx + '][tax_rate]"]').value) || 0;
        var ls = qty * price;
        var ld = ls * (discPct / 100);
        var la = ls - ld;
        var lt = la * (taxPct / 100);
        tr.querySelector('.line-total').textContent = (la + lt).toFixed(2);
        calcTotals();
    }

    function removeLine(btn, idx) {
        var rows = document.querySelectorAll('#lines-tbody tr');
        if (rows.length <= 1) return;
        var tr = btn.closest('tr');
        tr.parentNode.removeChild(tr);
        calcTotals();
    }

    function calcTotals() {
        var subtotal = 0, totalDisc = 0, totalTax = 0;
        var rows = document.querySelectorAll('#lines-tbody tr');
        rows.forEach(function(tr) {
            var qty = parseFloat(tr.querySelector('input[name$="[quantity]"]').value) || 0;
            var price = parseFloat(tr.querySelector('input[name$="[unit_price]"]').value) || 0;
            var discPct = parseFloat(tr.querySelector('input[name$="[discount_percent]"]').value) || 0;
            var taxPct = parseFloat(tr.querySelector('input[name$="[tax_rate]"]').value) || 0;
            var ls = qty * price;
            var ld = ls * (discPct / 100);
            var la = ls - ld;
            var lt = la * (taxPct / 100);
            subtotal += ls;
            totalDisc += ld;
            totalTax += lt;
        });
        var discAmt = parseFloat(document.getElementById('discount-amount').value) || 0;
        var shipAmt = parseFloat(document.getElementById('shipping-amount').value) || 0;
        var grand = subtotal - totalDisc - discAmt + totalTax + shipAmt;
        document.getElementById('tot-subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('tot-discount').textContent = '- ' + (totalDisc + discAmt).toFixed(2);
        document.getElementById('tot-tax').textContent = '+ ' + totalTax.toFixed(2);
        document.getElementById('tot-shipping').textContent = '+ ' + shipAmt.toFixed(2);
        document.getElementById('tot-grand').textContent = grand.toFixed(2);
    }

    addLine();
</script>