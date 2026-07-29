<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل إشعار الخصم: {{ $discountNote->note_number }}</h2>
            <a href="{{ route('discount-notes.show', $discountNote) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                العودة
            </a>
        </div>
    </x-slot>

    <form action="{{ route('discount-notes.update', $discountNote) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">رقم الإشعار</label>
                    <input type="text" value="{{ $discountNote->note_number }}" readonly class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $discountNote->date->format('Y-m-d')) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">العميل <span class="text-red-500">*</span></label>
                    <select name="customer_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر العميل</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $discountNote->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">فاتورة المبيعات</label>
                    <select name="sales_invoice_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">بدون فاتورة</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ old('sales_invoice_id', $discountNote->original_invoice_id) == $invoice->id ? 'selected' : '' }}>{{ $invoice->invoice_number }} - {{ $invoice->customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">المبلغ <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $discountNote->amount) }}" step="0.01" min="0.01" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">سبب الخصم <span class="text-red-500">*</span></label>
                    <input type="text" name="reason" value="{{ old('reason', $discountNote->reason) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('notes', $discountNote->notes) }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                تحديث الإشعار
            </button>
            <a href="{{ route('discount-notes.show', $discountNote) }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>
</x-app-layout>
