<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إنشاء إذن تسليم</h2>
            <a href="{{ route('sales-delivery-notes.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                إلغاء
            </a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form action="{{ route('sales-delivery-notes.store') }}" method="POST" id="deliveryForm">
            @csrf
            <input type="hidden" name="lines" id="linesInput">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="sales_order_id" class="mb-1 block text-sm font-medium text-gray-700">أمر البيع <span class="text-red-500">*</span></label>
                    <select name="sales_order_id" id="sales_order_id" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر أمر البيع</option>
                        @foreach($salesOrders as $order)
                            <option value="{{ $order->id }}" data-number="{{ $order->order_number }}">
                                {{ $order->order_number }} - {{ $order->customer->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date" class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', now()->toDateString()) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                <textarea name="notes" id="notes" rows="2"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="ملاحظات...">{{ old('notes') }}</textarea>
            </div>

            {{-- Order Lines --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-700">أصناف أمر البيع</h3>
                </div>
                <div id="orderInfo" class="px-4 py-3 text-sm text-gray-500">
                    الرجاء اختيار أمر بيع لعرض الأصناف
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm" id="linesTable" style="display:none;">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-2 font-semibold text-gray-600">الصنف</th>
                                <th class="px-4 py-2 font-semibold text-gray-600">الكمية المطلوبة</th>
                                <th class="px-4 py-2 font-semibold text-gray-600">الكمية المسلمة</th>
                                <th class="px-4 py-2 font-semibold text-gray-600">الكمية للتسليم</th>
                                <th class="px-4 py-2 font-semibold text-gray-600">سعر الوحدة</th>
                                <th class="px-4 py-2 font-semibold text-gray-600">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody id="linesBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6 mt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ إذن التسليم
                </button>
                <a href="{{ route('sales-delivery-notes.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('sales_order_id').addEventListener('change', function() {
            const orderId = this.value;
            const orderInfo = document.getElementById('orderInfo');
            const linesTable = document.getElementById('linesTable');
            const linesBody = document.getElementById('linesBody');

            if (!orderId) {
                orderInfo.textContent = 'الرجاء اختيار أمر بيع لعرض الأصناف';
                orderInfo.style.display = 'block';
                linesTable.style.display = 'none';
                return;
            }

            fetch('/api/sales-orders/' + orderId + '/lines')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    orderInfo.innerHTML = '<strong>العميل:</strong> ' + (data.customer?.name || '') +
                        ' | <strong>المخزن:</strong> ' + (data.warehouse?.name || '');
                    orderInfo.style.display = 'block';
                    linesTable.style.display = 'table';
                    linesBody.innerHTML = '';

                    if (!data.lines || data.lines.length === 0) {
                        linesBody.innerHTML = '<tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">لا توجد أصناف في أمر البيع</td></tr>';
                        return;
                    }

                    data.lines.forEach(function(line) {
                        var remaining = line.quantity - (line.delivered_qty || 0);
                        if (remaining <= 0) return;

                        var tr = document.createElement('tr');
                        tr.className = 'border-b border-gray-100';
                        tr.innerHTML =
                            '<td class="px-4 py-2">' + (line.item?.name || '') + '</td>' +
                            '<td class="px-4 py-2">' + line.quantity + '</td>' +
                            '<td class="px-4 py-2">' + (line.delivered_qty || 0) + '</td>' +
                            '<td class="px-4 py-2"><input type="number" class="line-qty w-20 rounded border border-gray-300 px-2 py-1 text-sm" min="0" max="' + remaining + '" step="0.01" value="' + remaining + '" data-line-id="' + line.id + '" data-item-id="' + line.item_id + '" data-unit-price="' + line.unit_price + '" required></td>' +
                            '<td class="px-4 py-2 unit-price">' + line.unit_price + '</td>' +
                            '<td class="px-4 py-2 line-total">' + (remaining * line.unit_price).toFixed(2) + '</td>';
                        linesBody.appendChild(tr);
                    });

                    document.querySelectorAll('.line-qty').forEach(function(input) {
                        input.addEventListener('input', function() {
                            var qty = parseFloat(this.value) || 0;
                            var price = parseFloat(this.dataset.unitPrice) || 0;
                            var totalTd = this.closest('tr').querySelector('.line-total');
                            totalTd.textContent = (qty * price).toFixed(2);
                        });
                    });
                })
                .catch(function() {
                    orderInfo.textContent = 'حدث خطأ في تحميل البيانات';
                });
        });

        document.getElementById('deliveryForm').addEventListener('submit', function(e) {
            var rows = document.querySelectorAll('#linesBody tr:not([colspan])');
            var lines = [];
            var valid = true;

            rows.forEach(function(row) {
                var qtyInput = row.querySelector('.line-qty');
                if (!qtyInput) return;
                var qty = parseFloat(qtyInput.value) || 0;
                if (qty <= 0) return;

                lines.push({
                    sales_order_line_id: parseInt(qtyInput.dataset.lineId),
                    item_id: parseInt(qtyInput.dataset.itemId),
                    quantity: qty,
                    unit_price: parseFloat(qtyInput.dataset.unitPrice) || 0,
                    total: qty * (parseFloat(qtyInput.dataset.unitPrice) || 0),
                });
            });

            if (lines.length === 0) {
                alert('الرجاء إدخال كمية صالحة للتسليم على الأقل');
                e.preventDefault();
                return;
            }

            document.getElementById('linesInput').value = JSON.stringify(lines);
        });
    </script>
    @endpush
</x-app-layout>
