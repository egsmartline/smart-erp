<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل فاتورة مبيعات</h2>
            <a href="{{ route('sales-invoices.show', $salesInvoice) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                العودة
            </a>
        </div>
    </x-slot>

    <form action="{{ route('sales-invoices.update', $salesInvoice) }}" method="POST" x-data @submit.prevent="
        let f = this; let s = (n, v) => { let e = f.querySelector('[name=&quot;'+n+'&quot;]'); if(e) e.value = v??''; };
        s('customer_id', $wire.customerId);
        let lines = $wire.lines;
        for(let i=0;i<lines.length;i++){ let l=lines[i];
            s('lines['+i+'][item_id]',l.item_id); s('lines['+i+'][description]',l.description);
            s('lines['+i+'][quantity]',l.quantity); s('lines['+i+'][unit_price]',l.unit_price);
            s('lines['+i+'][discount_percent]',l.discount_percent); s('lines['+i+'][tax_rate]',l.tax_rate);
        } f.submit();
    ">
        @csrf
        @method('PUT')
        @livewire('invoice-form', ['type' => 'sale', 'invoiceId' => $salesInvoice->id, 'warehouseId' => $salesInvoice->warehouse_id, 'showCustomerSearch' => false, 'showItemSelect' => true])

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                تحديث الفاتورة
            </button>
            <a href="{{ route('sales-invoices.show', $salesInvoice) }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>
</x-app-layout>
