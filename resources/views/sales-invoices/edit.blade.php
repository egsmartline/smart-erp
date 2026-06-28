<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل فاتورة مبيعات - {{ $salesInvoice->invoice_number }}</h2>
            <a href="{{ route('sales-invoices.show', $salesInvoice) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                العودة
            </a>
        </div>
    </x-slot>

    <form action="{{ route('sales-invoices.update', $salesInvoice) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">العميل <span class="text-red-500">*</span></label>
                    <select name="customer_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العميل</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $salesInvoice->customer_id == $customer->id ? 'selected' : '' }}>
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
                            <option value="{{ $warehouse->id }}" {{ $salesInvoice->warehouse_id == $warehouse->id ? 'selected' : '' }}>
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
                            <option value="{{ $currency->id }}" {{ $salesInvoice->currency_id == $currency->id ? 'selected' : '' }}>
                                {{ $currency->name }} - {{ $currency->symbol }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الفاتورة <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $salesInvoice->date?->format('Y-m-d') ?? date('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الاستحقاق <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" value="{{ old('due_date', $salesInvoice->due_date ?? date('Y-m-d', strtotime('+30 days'))) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <input type="text" name="notes" value="{{ old('notes', $salesInvoice->notes) }}" placeholder="ملاحظات إضافية..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div id="items-app">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">أصناف الفاتورة</h3>
                    <button type="button" id="btn-add-line"
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
                                <input type="number" id="discount-amount" name="discount_amount" value="{{ old('discount_amount', $salesInvoice->discount_amount ?? '0') }}" step="0.01" min="0"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-600">مصاريف شحن</label>
                                <input type="number" id="shipping-amount" name="shipping_amount" value="0" step="0.01" min="0"
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
                تحديث الفاتورة
            </button>
            <a href="{{ route('sales-invoices.show', $salesInvoice) }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>
</x-app-layout>

<script>
(function() {
    var lineIdx = 0;
    var itemsData = <?php echo json_encode($items->map(fn($i) => [
        'id' => $i->id,
        'name' => $i->name,
        'sku' => $i->sku,
        'price' => $i->selling_price,
        'tax' => $i->tax_rate ?? 15,
    ])->values()->all()); ?>;

    var existingLines = <?php echo json_encode($salesInvoice->lines->map(fn($l) => [
        'item_id' => $l->item_id,
        'quantity' => $l->quantity,
        'unit_price' => $l->unit_price,
        'discount_percent' => $l->discount_percent,
        'tax_rate' => $l->tax_rate,
    ])->all()); ?>;

    var tbody = document.getElementById('lines-tbody');
    if (!tbody) return;

    function addLine(data) {
        data = data || {};
        var idx = lineIdx++;
        var tr = document.createElement('tr');
        tr.className = 'border-b border-gray-100';
        tr.dataset.idx = idx;

        var td1 = document.createElement('td');
        td1.className = 'px-3 py-2 text-gray-500';
        td1.textContent = idx + 1;

        var td2 = document.createElement('td');
        td2.className = 'px-3 py-2';
        var select = document.createElement('select');
        select.name = 'lines[' + idx + '][item_id]';
        select.required = true;
        select.className = 'w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500';
        var emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = 'اختر صنف';
        select.appendChild(emptyOpt);
        for (var i = 0; i < itemsData.length; i++) {
            var item = itemsData[i];
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.dataset.price = item.price;
            opt.dataset.tax = item.tax;
            opt.textContent = (item.name || '') + ' - ' + (item.sku || '');
            if (data.item_id && String(item.id) === String(data.item_id)) opt.selected = true;
            select.appendChild(opt);
        }
        td2.appendChild(select);

        var makeInput = function(name, val, extra) {
            var inp = document.createElement('input');
            inp.type = 'number';
            inp.name = 'lines[' + idx + '][' + name + ']';
            inp.value = val;
            inp.step = '0.01';
            inp.className = 'w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500';
            if (extra) {
                for (var k in extra) inp[k] = extra[k];
            }
            return inp;
        };

        var td3 = document.createElement('td');
        td3.className = 'px-3 py-2';
        var inpQty = makeInput('quantity', data.quantity || '1', { min: '0.01' });
        inpQty.required = true;
        td3.appendChild(inpQty);

        var td4 = document.createElement('td');
        td4.className = 'px-3 py-2';
        var inpPrice = makeInput('unit_price', data.unit_price || '0', { min: '0' });
        inpPrice.required = true;
        inpPrice.className = inpPrice.className.replace('w-20', 'w-24');
        td4.appendChild(inpPrice);

        var td5 = document.createElement('td');
        td5.className = 'px-3 py-2';
        var inpDisc = makeInput('discount_percent', data.discount_percent || '0', { min: '0', max: '100' });
        inpDisc.className = inpDisc.className.replace('w-20', 'w-16');
        td5.appendChild(inpDisc);

        var td6 = document.createElement('td');
        td6.className = 'px-3 py-2';
        var inpTax = makeInput('tax_rate', data.tax_rate || '15', { min: '0', max: '100' });
        inpTax.className = inpTax.className.replace('w-20', 'w-16');
        td6.appendChild(inpTax);

        var td7 = document.createElement('td');
        td7.className = 'px-3 py-3 text-left font-mono text-sm font-medium text-gray-900';
        td7.id = 'total-' + idx;
        td7.textContent = '0.00';

        var td8 = document.createElement('td');
        td8.className = 'px-3 py-2 text-center';
        var delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 transition cursor-pointer btn-del-line';
        delBtn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        td8.appendChild(delBtn);

        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);
        tr.appendChild(td4);
        tr.appendChild(td5);
        tr.appendChild(td6);
        tr.appendChild(td7);
        tr.appendChild(td8);
        tbody.appendChild(tr);
    }

    function getRow(el) {
        while (el && el.tagName !== 'TR') el = el.parentElement;
        return el;
    }

    function calcRow(idx) {
        var tr = tbody.querySelector('[data-idx="' + idx + '"]');
        if (!tr) return;
        var qty = parseFloat(tr.querySelector('[name$="[quantity]"]').value) || 0;
        var price = parseFloat(tr.querySelector('[name$="[unit_price]"]').value) || 0;
        var discPct = parseFloat(tr.querySelector('[name$="[discount_percent]"]').value) || 0;
        var taxPct = parseFloat(tr.querySelector('[name$="[tax_rate]"]').value) || 0;
        var ls = qty * price;
        var ld = ls * (discPct / 100);
        var la = ls - ld;
        var lt = la * (taxPct / 100);
        var totalEl = document.getElementById('total-' + idx);
        if (totalEl) totalEl.textContent = (la + lt).toFixed(2);
        calcTotals();
    }

    function calcTotals() {
        var subtotal = 0, totalDisc = 0, totalTax = 0;
        var rows = tbody.querySelectorAll('tr');
        for (var i = 0; i < rows.length; i++) {
            var tr = rows[i];
            var qty = parseFloat(tr.querySelector('[name$="[quantity]"]').value) || 0;
            var price = parseFloat(tr.querySelector('[name$="[unit_price]"]').value) || 0;
            var discPct = parseFloat(tr.querySelector('[name$="[discount_percent]"]').value) || 0;
            var taxPct = parseFloat(tr.querySelector('[name$="[tax_rate]"]').value) || 0;
            var ls = qty * price;
            var ld = ls * (discPct / 100);
            var la = ls - ld;
            var lt = la * (taxPct / 100);
            subtotal += ls;
            totalDisc += ld;
            totalTax += lt;
        }
        var discAmt = parseFloat(document.getElementById('discount-amount').value) || 0;
        var shipAmt = parseFloat(document.getElementById('shipping-amount').value) || 0;
        var grand = subtotal - totalDisc - discAmt + totalTax + shipAmt;
        document.getElementById('tot-subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('tot-discount').textContent = '- ' + (totalDisc + discAmt).toFixed(2);
        document.getElementById('tot-tax').textContent = '+ ' + totalTax.toFixed(2);
        document.getElementById('tot-shipping').textContent = '+ ' + shipAmt.toFixed(2);
        document.getElementById('tot-grand').textContent = grand.toFixed(2);
    }

    document.getElementById('btn-add-line').addEventListener('click', function() {
        addLine();
    });

    document.getElementById('items-app').addEventListener('change', function(e) {
        var target = e.target;
        if (target.tagName === 'SELECT' && target.name.indexOf('[item_id]') > -1) {
            var tr = getRow(target);
            if (!tr) return;
            var idx = parseInt(tr.dataset.idx);
            var opt = target.options[target.selectedIndex];
            if (opt && opt.value) {
                var price = parseFloat(opt.dataset.price) || 0;
                var tax = parseFloat(opt.dataset.tax) || 15;
                tr.querySelector('[name$="[unit_price]"]').value = price;
                tr.querySelector('[name$="[tax_rate]"]').value = tax;
                tr.querySelector('[name$="[quantity]"]').value = '1';
                calcRow(idx);
            }
        }
    });

    document.getElementById('items-app').addEventListener('input', function(e) {
        var target = e.target;
        if (target.tagName === 'INPUT' && target.type === 'number') {
            var tr = getRow(target);
            if (!tr) return;
            var idx = parseInt(tr.dataset.idx);
            calcRow(idx);
        }
    });

    document.getElementById('discount-amount').addEventListener('input', calcTotals);
    document.getElementById('shipping-amount').addEventListener('input', calcTotals);

    document.getElementById('items-app').addEventListener('click', function(e) {
        var target = e.target;
        if (target.classList.contains('btn-del-line') || target.closest('.btn-del-line')) {
            var btn = target.classList.contains('btn-del-line') ? target : target.closest('.btn-del-line');
            var tr = getRow(btn);
            if (!tr) return;
            if (tbody.querySelectorAll('tr').length <= 1) return;
            tr.parentNode.removeChild(tr);
            calcTotals();
        }
    });

    for (var i = 0; i < existingLines.length; i++) {
        addLine(existingLines[i]);
    }
    if (existingLines.length === 0) addLine();
})();
</script>
