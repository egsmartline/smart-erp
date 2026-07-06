<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل إذن تسليم - {{ $salesDeliveryNote->delivery_number }}</h2>
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
        <form action="{{ route('sales-delivery-notes.update', $salesDeliveryNote) }}" method="POST" id="deliveryForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="lines" id="linesInput">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="customer_id" class="mb-1 block text-sm font-medium text-gray-700">العميل <span class="text-red-500">*</span></label>
                    <select name="customer_id" id="customer_id" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العميل</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $salesDeliveryNote->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="warehouse_id" class="mb-1 block text-sm font-medium text-gray-700">المخزن <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" id="warehouse_id" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $salesDeliveryNote->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', $salesDeliveryNote->date->format('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                <textarea name="notes" id="notes" rows="2"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="ملاحظات...">{{ old('notes', $salesDeliveryNote->notes) }}</textarea>
            </div>

            {{-- Items --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-700">الأصناف</h3>
                    <button type="button" id="addItemBtn" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                        + إضافة صنف
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-2 font-semibold text-gray-600">الصنف</th>
                                <th class="px-4 py-2 font-semibold text-gray-600">الكمية</th>
                                <th class="px-4 py-2 font-semibold text-gray-600">سعر الوحدة</th>
                                <th class="px-4 py-2 font-semibold text-gray-600">الإجمالي</th>
                                <th class="px-4 py-2 font-semibold text-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6 mt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    تحديث إذن التسليم
                </button>
                <a href="{{ route('sales-delivery-notes.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let itemIndex = 0;
        const items = @json($items);
        const existingLines = @json($salesDeliveryNote->lines);

        document.getElementById('addItemBtn').addEventListener('click', function() {
            addItemRow();
        });

        function addItemRow(data) {
            const tbody = document.getElementById('itemsBody');
            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-100';
            tr.dataset.index = itemIndex;
            tr.innerHTML =
                '<td class="px-4 py-2">' +
                    '<select name="items[' + itemIndex + '][item_id]" class="item-select w-40 rounded border border-gray-300 px-2 py-1 text-sm" required>' +
                        '<option value="">اختر صنف</option>' +
                        items.map(function(i) { return '<option value="' + i.id + '"' + (data && data.item_id === i.id ? ' selected' : '') + '>' + i.name + ' (' + (i.sku || '') + ')</option>'; }).join('') +
                    '</select>' +
                '</td>' +
                '<td class="px-4 py-2"><input type="number" class="item-qty w-20 rounded border border-gray-300 px-2 py-1 text-sm" min="0.01" step="0.01" value="' + (data ? data.quantity : 1) + '" required></td>' +
                '<td class="px-4 py-2"><input type="number" class="item-price w-24 rounded border border-gray-300 px-2 py-1 text-sm" min="0" step="0.01" value="' + (data ? data.unit_price : 0) + '" required></td>' +
                '<td class="px-4 py-2 item-total font-medium">' + (data ? (data.quantity * data.unit_price).toFixed(2) : '0.00') + '</td>' +
                '<td class="px-4 py-2"><button type="button" class="remove-item rounded p-1 text-red-500 hover:bg-red-50 transition cursor-pointer" onclick="this.closest(\'tr\').remove()">✕</button></td>';
            tbody.appendChild(tr);
            itemIndex++;

            tr.querySelector('.item-qty').addEventListener('input', updateRowTotal);
            tr.querySelector('.item-price').addEventListener('input', updateRowTotal);
        }

        function updateRowTotal() {
            const tr = this.closest('tr');
            const qty = parseFloat(tr.querySelector('.item-qty').value) || 0;
            const price = parseFloat(tr.querySelector('.item-price').value) || 0;
            tr.querySelector('.item-total').textContent = (qty * price).toFixed(2);
        }

        existingLines.forEach(function(line) {
            addItemRow(line);
        });

        document.getElementById('deliveryForm').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('#itemsBody tr');
            const lines = [];

            rows.forEach(function(row) {
                const itemSelect = row.querySelector('.item-select');
                const qtyInput = row.querySelector('.item-qty');
                const priceInput = row.querySelector('.item-price');
                const itemId = parseInt(itemSelect.value);
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                if (!itemId || qty <= 0) return;
                lines.push({
                    item_id: itemId,
                    quantity: qty,
                    unit_price: price,
                    total: qty * price,
                });
            });

            if (lines.length === 0) {
                alert('الرجاء إدخال صنف واحد على الأقل');
                e.preventDefault();
                return;
            }

            document.getElementById('linesInput').value = JSON.stringify(lines);
        });
    </script>
    @endpush
</x-app-layout>
