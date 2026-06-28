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

    <form action="{{ route('sales-invoices.store') }}" method="POST" id="invoiceForm" x-data @submit.prevent="
        let f = this;
        let setVal = (n, v) => { let el = f.querySelector('[name=&quot;' + n + '&quot;]'); if (el) el.value = v ?? ''; };
        setVal('customer_id', $wire.customerId);
        let lines = $wire.lines;
        for (let i = 0; i < lines.length; i++) {
            let ln = lines[i];
            setVal('lines[' + i + '][item_id]', ln.item_id);
            setVal('lines[' + i + '][description]', ln.description);
            setVal('lines[' + i + '][quantity]', ln.quantity);
            setVal('lines[' + i + '][unit_price]', ln.unit_price);
            setVal('lines[' + i + '][discount_percent]', ln.discount_percent);
            setVal('lines[' + i + '][tax_rate]', ln.tax_rate);
        }
        f.submit();
    ">
        @csrf
        @livewire('invoice-form', ['type' => 'sale', 'warehouseId' => $warehouses->first()->id ?? null, 'showCustomerSearch' => false, 'showItemSelect' => true])

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                حفظ الفاتورة
            </button>
            <a href="{{ route('sales-invoices.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>
</x-app-layout>
