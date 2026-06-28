<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل عرض أسعار</h2>
            <a href="{{ route('quotations.show', $quotation) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                العودة
            </a>
        </div>
    </x-slot>

    <form action="{{ route('quotations.update', $quotation) }}" method="POST" onsubmit="return syncInvoiceForm(this)">
        @csrf
        @method('PUT')
        @livewire('invoice-form', ['type' => 'sale', 'invoiceId' => $quotation->id, 'customerId' => $quotation->customer_id, 'showCustomerSearch' => false, 'showItemSelect' => true])

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">تاريخ العرض</label>
                <input type="date" name="date" value="{{ $quotation->date->format('Y-m-d') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">صالح حتى</label>
                <input type="date" name="valid_until" value="{{ $quotation->valid_until->format('Y-m-d') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
        </div>

        <div class="mt-4">
            <label class="mb-1 block text-sm font-medium text-gray-700">الشروط والأحكام</label>
            <textarea name="terms" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ $quotation->terms }}</textarea>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                تحديث العرض
            </button>
            <a href="{{ route('quotations.show', $quotation) }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>
</x-app-layout>
