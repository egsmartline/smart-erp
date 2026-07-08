<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">فاتورة مبيعات - {{ $salesInvoice->invoice_number }}</h2>
            <div class="flex items-center gap-2">
                <x-print-button url="{{ route('pdf.sales-invoice', $salesInvoice) }}" label="تحميل PDF" />
                <a href="{{ route('sales-invoices.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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
                        <h3 class="text-lg font-bold text-blue-700">فاتورة مبيعات</h3>
                        <p class="text-sm text-gray-500">Invoice # {{ $salesInvoice->invoice_number }}</p>
                    </div>
                    <div class="text-left">
                        @if($salesInvoice->status === 'draft')
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">مسودة</span>
                        @elseif($salesInvoice->status === 'posted')
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">مرحل</span>
                        @elseif($salesInvoice->status === 'voided')
                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">ملغي</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800">{{ $salesInvoice->status }}</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500">العميل</p>
                        <p class="font-medium text-gray-900">{{ $salesInvoice->customer->name ?? '-' }}</p>
                        <p class="text-sm text-gray-600">{{ $salesInvoice->customer->phone ?? '' }}</p>
                    </div>
                    <div class="text-left">
                        <p class="text-xs text-gray-500">التاريخ والمستحق</p>
                        <p class="font-medium text-gray-900">{{ $salesInvoice->date ? $salesInvoice->date->format('Y-m-d') : '-' }}</p>
                        <p class="text-sm text-gray-600">مستحق: {{ $salesInvoice->due_date ? $salesInvoice->due_date->format('Y-m-d') : '-' }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-3 py-2 font-semibold text-gray-700">#</th>
                                <th class="px-3 py-2 font-semibold text-gray-700">الصنف</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الكمية</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">السعر</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الخصم</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الضريبة</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesInvoice->lines as $index => $line)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $line->item->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->quantity, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->unit_price, 2) }}</td>
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
                                <td class="px-3 py-3 text-left font-mono">{{ number_format($salesInvoice->subtotal, 2) }}</td>
                            </tr>
                            @if($salesInvoice->discount_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الخصم</td>
                                <td class="px-3 py-3 text-left font-mono text-red-600">- {{ number_format($salesInvoice->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($salesInvoice->tax_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الضريبة</td>
                                <td class="px-3 py-3 text-left font-mono text-emerald-600">+ {{ number_format($salesInvoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-blue-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm font-bold text-gray-800">الإجمالي</td>
                                <td class="px-3 py-3 text-left font-mono text-lg font-bold text-blue-700">{{ number_format($salesInvoice->total, 2) }} {{ $salesInvoice->currency->symbol ?? '' }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($salesInvoice->notes)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500">ملاحظات</p>
                        <p class="text-sm text-gray-700">{{ $salesInvoice->notes }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-4 border-t border-gray-200 pt-4">
                    @php $curSym = $salesInvoice->currency->symbol ?? ''; @endphp
                    <div>
                        <p class="text-xs text-gray-500">الإجمالي</p>
                        <p class="text-lg font-bold font-mono text-gray-900">{{ number_format($salesInvoice->total, 2) }} {{ $curSym }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">المدفوع</p>
                        <p class="text-lg font-bold font-mono text-emerald-600">{{ number_format($salesInvoice->paid_amount, 2) }} {{ $curSym }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">المستحق</p>
                        <p class="text-lg font-bold font-mono text-red-600">{{ number_format($salesInvoice->due_amount, 2) }} {{ $curSym }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4 no-print">
                <h4 class="text-sm font-bold text-gray-700 mb-3">إجراءات</h4>
                <div class="space-y-2">
                    @if($salesInvoice->status === 'draft')
                        <form action="{{ route('sales-invoices.post', $salesInvoice) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الترحيل؟')">
                                ترحيل الفاتورة
                            </button>
                        </form>
                        <a href="{{ route('sales-invoices.edit', $salesInvoice) }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                            تعديل الفاتورة
                        </a>
                        <form action="{{ route('sales-invoices.destroy', $salesInvoice) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                حذف الفاتورة
                            </button>
                        </form>
                    @elseif($salesInvoice->status === 'posted')
                        <a href="{{ route('sales-invoices.edit', $salesInvoice) }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                            تعديل الفاتورة
                        </a>
                        <form action="{{ route('sales-invoices.void', $salesInvoice) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الإلغاء؟ سيتم إعادة المخزون.')">
                                إلغاء الفاتورة
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('sales-invoices.index') }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">معلومات الدفع</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">الحالة:</span>
                        <span class="font-medium">{{ $salesInvoice->payment_status === 'paid' ? 'مدفوع بالكامل' : ($salesInvoice->payment_status === 'partial' ? 'مدفوع جزئياً' : 'غير مدفوع') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">المدفوع:</span>
                        <span class="font-mono font-medium text-emerald-600">{{ number_format($salesInvoice->paid_amount, 2) }} {{ $salesInvoice->currency->symbol ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">المستحق:</span>
                        <span class="font-mono font-medium text-red-600">{{ number_format($salesInvoice->due_amount, 2) }} {{ $salesInvoice->currency->symbol ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
</style>
