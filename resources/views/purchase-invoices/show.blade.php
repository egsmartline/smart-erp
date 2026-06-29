<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">فاتورة مشتريات - {{ $purchaseInvoice->invoice_number }}</h2>
            <div class="flex items-center gap-2">
                <x-print-button url="{{ route('pdf.purchase-invoice', $purchaseInvoice) }}" label="تحميل PDF" />
                <a href="{{ route('purchase-invoices.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    العودة للقائمة
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6" id="printArea">
                <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-blue-700">فاتورة مشتريات</h3>
                        <p class="text-sm text-gray-500">Invoice # {{ $purchaseInvoice->invoice_number }}</p>
                    </div>
                    <div>
                        @if($purchaseInvoice->status === 'draft')
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">مسودة</span>
                        @elseif($purchaseInvoice->status === 'approved')
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">مرحل</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">ملغي</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500">المورد</p>
                        <p class="font-medium text-gray-900">{{ $purchaseInvoice->supplier->name ?? '-' }}</p>
                        <p class="text-sm text-gray-600">{{ $purchaseInvoice->supplier->phone ?? '' }}</p>
                    </div>
                    <div class="text-left">
                        <p class="text-xs text-gray-500">التاريخ والمستحق</p>
                        <p class="font-medium text-gray-900">{{ $purchaseInvoice->date->format('Y-m-d') }}</p>
                        <p class="text-sm text-gray-600">مستحق: {{ $purchaseInvoice->due_date ? $purchaseInvoice->due_date->format('Y-m-d') : '-' }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-3 py-2 font-semibold text-gray-700">#</th>
                                <th class="px-3 py-2 font-semibold text-gray-700">الصنف</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الكمية</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">التكلفة</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الخصم</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الضريبة</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseInvoice->lines as $index => $line)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $line->item->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->quantity, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->unit_cost, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->discount_amount, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->tax_amount, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono font-medium">{{ number_format($line->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">المجموع الفرعي</td>
                                <td class="px-3 py-3 text-left font-mono">{{ number_format($purchaseInvoice->subtotal, 2) }}</td>
                            </tr>
                            @if($purchaseInvoice->discount_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الخصم</td>
                                <td class="px-3 py-3 text-left font-mono text-red-600">- {{ number_format($purchaseInvoice->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($purchaseInvoice->tax_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الضريبة</td>
                                <td class="px-3 py-3 text-left font-mono text-emerald-600">+ {{ number_format($purchaseInvoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-blue-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm font-bold text-gray-800">الإجمالي</td>
                                <td class="px-3 py-3 text-left font-mono text-lg font-bold text-blue-700">{{ number_format($purchaseInvoice->total, 2) }} {{ $purchaseInvoice->currency->symbol ?? '' }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($purchaseInvoice->notes)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500">ملاحظات</p>
                        <p class="text-sm text-gray-700">{{ $purchaseInvoice->notes }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-4 border-t border-gray-200 pt-4">
                    @php $curSym = $purchaseInvoice->currency->symbol ?? ''; @endphp
                    <div>
                        <p class="text-xs text-gray-500">الإجمالي</p>
                        <p class="text-lg font-bold font-mono text-gray-900">{{ number_format($purchaseInvoice->total, 2) }} {{ $curSym }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">المدفوع</p>
                        <p class="text-lg font-bold font-mono text-emerald-600">{{ number_format($purchaseInvoice->paid_amount, 2) }} {{ $curSym }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">المستحق</p>
                        <p class="text-lg font-bold font-mono text-red-600">{{ number_format($purchaseInvoice->due_amount, 2) }} {{ $curSym }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">إجراءات</h4>
                <div class="space-y-2">
                    @if($purchaseInvoice->status === 'draft')
                        <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">طباعة الفاتورة</button>
                        <form action="{{ route('purchase-invoices.post', $purchaseInvoice) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الترحيل؟')">
                                ترحيل الفاتورة
                            </button>
                        </form>
                        <a href="{{ route('purchase-invoices.edit', $purchaseInvoice) }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">تعديل الفاتورة</a>
                        <form action="{{ route('purchase-invoices.destroy', $purchaseInvoice) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                حذف الفاتورة
                            </button>
                        </form>
                    @elseif($purchaseInvoice->status === 'approved')
                        <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">طباعة الفاتورة</button>
                        <form action="{{ route('purchase-invoices.void', $purchaseInvoice) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الإلغاء؟ سيتم خصم المخزون.')">
                                إلغاء الفاتورة
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('purchase-invoices.index') }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">العودة للقائمة</a>
                </div>
            </div>

            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">معلومات الدفع</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">الحالة:</span>
                        <span class="font-medium">{{ $purchaseInvoice->payment_status === 'paid' ? 'مدفوع بالكامل' : ($purchaseInvoice->payment_status === 'partial' ? 'مدفوع جزئياً' : 'غير مدفوع') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">المدفوع:</span>
                        <span class="font-mono font-medium text-emerald-600">{{ number_format($purchaseInvoice->paid_amount, 2) }} {{ $purchaseInvoice->currency->symbol ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">المستحق:</span>
                        <span class="font-mono font-medium text-red-600">{{ number_format($purchaseInvoice->due_amount, 2) }} {{ $purchaseInvoice->currency->symbol ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    @media print {
        body * { display: none !important; }
        #printArea, #printArea * { display: block !important; }
        #printArea { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>
