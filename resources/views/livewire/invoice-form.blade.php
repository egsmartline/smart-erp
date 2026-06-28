<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        @if($showCustomerSearch)
        <div class="relative" wire:key="customer-search" x-data="{ open: false }" @click.outside="open = false; $wire.filteredCustomers = []; $wire.filteredSuppliers = []">
            <label class="mb-1 block text-sm font-medium text-gray-700">
                {{ $type === 'sale' ? 'العميل' : 'المورد' }} <span class="text-red-500">*</span>
            </label>
            @if($type === 'sale')
                <input type="text" wire:model.live="customerSearch" @focus="open = true"
                    placeholder="بحث عن عميل..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" autocomplete="off">
                <input type="hidden" name="customer_id">
                @if(count($filteredCustomers) > 0)
                    <div class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-60 overflow-y-auto" x-show="open">
                        @foreach($filteredCustomers as $customer)
                            <div wire:click="selectCustomer({{ $customer->id }})" class="cursor-pointer px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100">
                                <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $customer->phone ?? '' }} | {{ $customer->email ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <input type="text" wire:model.live="supplierSearch" @focus="open = true"
                    placeholder="بحث عن مورد..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" autocomplete="off">
                <input type="hidden" name="supplier_id">
                @if(count($filteredSuppliers) > 0)
                    <div class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-60 overflow-y-auto" x-show="open">
                        @foreach($filteredSuppliers as $supplier)
                            <div wire:click="selectSupplier({{ $supplier->id }})" class="cursor-pointer px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100">
                                <div class="font-medium text-gray-900">{{ $supplier->name }}</div>
                                <div class="text-xs text-gray-500">{{ $supplier->phone ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
        @else
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">{{ $type === 'sale' ? 'العميل' : 'المورد' }} <span class="text-red-500">*</span></label>
            <select name="customer_id" wire:model="customerId" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">اختر {{ $type === 'sale' ? 'العميل' : 'المورد' }}</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}{{ $customer->phone ? ' ('.$customer->phone.')' : '' }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">المخزن <span class="text-red-500">*</span></label>
            <select name="warehouse_id" wire:model="warehouseId" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">اختر المخزن</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">العملة <span class="text-red-500">*</span></label>
            <select name="currency_id" wire:model="currencyId" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">اختر العملة</option>
                @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}">{{ $currency->name }} - {{ $currency->symbol }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الفاتورة <span class="text-red-500">*</span></label>
            <input type="date" name="date" wire:model="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ الاستحقاق <span class="text-red-500">*</span></label>
            <input type="date" name="due_date" wire:model="dueDate" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
            <input type="text" name="notes" wire:model="notes" placeholder="ملاحظات إضافية..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-800">أصناف الفاتورة</h3>
        <button type="button" wire:click="addLine" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700 transition cursor-pointer">
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
                @foreach($lines as $index => $line)
                    <tr class="border-b border-gray-100" wire:key="line-row-{{ $index }}">
                        <td class="px-3 py-2 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-3 py-2">
                            @if($showItemSelect)
                                <select wire:model.live="lines.{{ $index }}.item_id" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="">اختر صنف</option>
                                    @foreach($allItems as $item)
                                        <option value="{{ $item->id }}" {{ ($line['item_id'] ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} - {{ $item->sku ?? '' }} | @if($type === 'sale') سعر البيع: {{ number_format($item->selling_price ?? 0, 2) }} @else سعر التكلفة: {{ number_format($item->purchase_price ?? 0, 2) }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="relative" wire:key="item-search-{{ $index }}" x-data="{ open: false }" @click.outside="open = false; if($wire.searchingLineIndex === {{ $index }}) { $wire.filteredItems = []; $wire.searchingLineIndex = null }">
                                    <input type="text" wire:model.live="itemSearches.{{ $index }}" @focus="open = true"
                                        placeholder="بحث عن صنف..." class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" autocomplete="off">
                                    @if($searchingLineIndex === $index && count($filteredItems) > 0)
                                        <div class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-60 overflow-y-auto" x-show="open">
                                            @foreach($filteredItems as $item)
                                                <div wire:click="selectItem({{ $item->id }}, {{ $index }})" class="cursor-pointer px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100">
                                                    <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $item->sku ?? '' }} | سعر البيع: {{ number_format($item->selling_price, 2) }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" name="lines[{{ $index }}][item_id]">
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" wire:model.live="lines.{{ $index }}.quantity" value="{{ $line['quantity'] ?? 1 }}" step="0.01" min="0.01" wire:key="qty-{{ $index }}-{{ $line['quantity'] ?? 1 }}"
                                class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" wire:model.live="lines.{{ $index }}.unit_price" value="{{ $line['unit_price'] ?? 0 }}" step="0.01" min="0" wire:key="price-{{ $index }}-{{ $line['unit_price'] ?? 0 }}"
                                class="w-24 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" wire:model.live="lines.{{ $index }}.discount_percent" step="0.01" min="0" max="100"
                                class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" wire:model.live="lines.{{ $index }}.tax_rate" step="0.01" min="0" max="100"
                                class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="px-3 py-3 text-left font-mono text-sm font-medium text-gray-900">
                            @php
                                $ls = ($line['quantity'] ?? 0) * ($line['unit_price'] ?? 0);
                                $ld = $ls * (($line['discount_percent'] ?? 0) / 100);
                                $lt = ($ls - $ld) * (($line['tax_rate'] ?? 0) / 100);
                                $lf = $ls - $ld + $lt;
                            @endphp
                            {{ number_format($lf, 2) }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if(count($lines) > 1)
                                <button type="button" wire:click="removeLine({{ $index }})" class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 transition cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
            <h4 class="text-sm font-bold text-gray-700 mb-3">الخصومات والشحن</h4>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">خصم إضافي</label>
                    <input type="number" name="discount_amount" wire:model.live="discountAmount" step="0.01" min="0"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">مصاريف شحن</label>
                    <input type="number" name="shipping_amount" wire:model.live="shippingAmount" step="0.01" min="0"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
            <h4 class="text-sm font-bold text-gray-700 mb-3">ملخص الفاتورة</h4>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">المجموع الفرعي</span>
                    <span class="font-mono font-medium">{{ number_format($subtotal, 2) }} {{ $currencies->firstWhere('id', $currencyId)?->symbol ?? '' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">الخصم</span>
                    <span class="font-mono font-medium text-red-600">- {{ number_format($totalDiscount + $discountAmount, 2) }} {{ $currencies->firstWhere('id', $currencyId)?->symbol ?? '' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">الضريبة</span>
                    <span class="font-mono font-medium text-emerald-600">+ {{ number_format($totalTax, 2) }} {{ $currencies->firstWhere('id', $currencyId)?->symbol ?? '' }}</span>
                </div>
                @if($shippingAmount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">الشحن</span>
                    <span class="font-mono font-medium">+ {{ number_format($shippingAmount, 2) }} {{ $currencies->firstWhere('id', $currencyId)?->symbol ?? '' }}</span>
                </div>
                @endif
                <div class="border-t border-blue-200 pt-2 flex justify-between">
                    <span class="text-base font-bold text-gray-800">الإجمالي</span>
                    <span class="text-lg font-bold font-mono text-blue-700">{{ number_format($grandTotal, 2) }} {{ $currencies->firstWhere('id', $currencyId)?->symbol ?? '' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if(count($lines) < 1)
        <div class="mt-4 rounded-lg bg-yellow-50 p-3 text-sm text-yellow-800 border border-yellow-200">
            <strong>تنبيه:</strong> يجب إضافة صنف واحد على الأقل
        </div>
    @endif

    {{-- Hidden inputs for form POST (synced from $wire before submit) --}}
    @foreach($lines as $index => $line)
        <input type="hidden" name="lines[{{ $index }}][item_id]">
        <input type="hidden" name="lines[{{ $index }}][description]">
        <input type="hidden" name="lines[{{ $index }}][quantity]">
        <input type="hidden" name="lines[{{ $index }}][unit_price]">
        <input type="hidden" name="lines[{{ $index }}][discount_percent]">
        <input type="hidden" name="lines[{{ $index }}][tax_rate]">
    @endforeach
</div>

<script>
    window.syncInvoiceForm = function(form) {
        let s = function(name, val) {
            let el = form.querySelector('[name="' + name + '"]');
            if (el) el.value = val ?? '';
        };
        s('customer_id', $wire.customerId);
        s('supplier_id', $wire.supplierId);
        let lines = $wire.lines;
        for (let i = 0; i < lines.length; i++) {
            let ln = lines[i];
            s('lines[' + i + '][item_id]', ln.item_id);
            s('lines[' + i + '][description]', ln.description);
            s('lines[' + i + '][quantity]', ln.quantity);
            s('lines[' + i + '][unit_price]', ln.unit_price);
            s('lines[' + i + '][discount_percent]', ln.discount_percent);
            s('lines[' + i + '][tax_rate]', ln.tax_rate);
        }
        return true;
    };
</script>
