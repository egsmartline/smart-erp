<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">أمر بيع - {{ $salesOrder->order_number }}</h2>
            <div class="flex items-center gap-2">
                <x-print-button url="{{ route('pdf.sales-order', $salesOrder) }}" label="تحميل PDF" />
                <a href="{{ route('sales-orders.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6" id="printArea">
                <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-blue-700">أمر بيع</h3>
                        <p class="text-sm text-gray-500">{{ $salesOrder->order_number }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($salesOrder->status === 'draft')
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">مسودة</span>
                        @elseif($salesOrder->status === 'confirmed')
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">مؤكد</span>
                        @elseif($salesOrder->status === 'delivered')
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-sm font-medium text-purple-800">تم التسليم</span>
                        @elseif($salesOrder->status === 'invoiced')
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">تم الفوترة</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">ملغي</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500">العميل</p>
                        <p class="font-medium text-gray-900">{{ $salesOrder->customer->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">المخزن</p>
                        <p class="font-medium text-gray-900">{{ $salesOrder->warehouse->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">التاريخ</p>
                        <p class="font-medium text-gray-900">{{ $salesOrder->date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">التاريخ المطلوب</p>
                        <p class="font-medium text-gray-900">{{ $salesOrder->required_date ? $salesOrder->required_date->format('Y-m-d') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">المندوب</p>
                        <p class="font-medium text-gray-900">{{ $salesOrder->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">شروط الدفع</p>
                        <p class="font-medium text-gray-900">{{ $salesOrder->paymentTerm->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">حالة التسليم</p>
                        @if($salesOrder->delivery_status === 'pending')
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">قيد الانتظار</span>
                        @elseif($salesOrder->delivery_status === 'partial')
                            <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">جزئي</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">تم التسليم</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">حالة الفوترة</p>
                        @if($salesOrder->invoice_status === 'not_invoiced')
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">غير مفوتر</span>
                        @elseif($salesOrder->invoice_status === 'partially_invoiced')
                            <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">فوترة جزئية</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">مفوتر بالكامل</span>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-3 py-2 font-semibold text-gray-700">#</th>
                                <th class="px-3 py-2 font-semibold text-gray-700">الصنف</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الكمية</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">تم التسليم</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">السعر</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الخصم</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الضريبة</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesOrder->lines as $index => $line)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $line->item->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->quantity, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->delivered_qty, 2) }}</td>
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
                                <td class="px-3 py-3 text-left font-mono" colspan="2">{{ number_format($salesOrder->subtotal, 2) }}</td>
                            </tr>
                            @if($salesOrder->discount_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الخصم</td>
                                <td class="px-3 py-3 text-left font-mono text-red-600" colspan="2">- {{ number_format($salesOrder->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($salesOrder->tax_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الضريبة</td>
                                <td class="px-3 py-3 text-left font-mono text-emerald-600" colspan="2">+ {{ number_format($salesOrder->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-blue-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm font-bold text-gray-800">الإجمالي</td>
                                <td class="px-3 py-3 text-left font-mono text-lg font-bold text-blue-700" colspan="2">{{ number_format($salesOrder->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($salesOrder->notes)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500">ملاحظات</p>
                        <p class="text-sm text-gray-700">{{ $salesOrder->notes }}</p>
                    </div>
                @endif

                @if($salesOrder->terms)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500">الشروط</p>
                        <p class="text-sm text-gray-700">{{ $salesOrder->terms }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">إجراءات</h4>
                <div class="space-y-2">
                    @if($salesOrder->status === 'draft')
                        <form action="{{ route('sales-orders.confirm', $salesOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من تأكيد أمر البيع؟')">
                                تأكيد أمر البيع
                            </button>
                        </form>
                        <a href="{{ route('sales-orders.edit', $salesOrder) }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                            تعديل أمر البيع
                        </a>
                        <form action="{{ route('sales-orders.destroy', $salesOrder) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                حذف أمر البيع
                            </button>
                        </form>
                    @elseif($salesOrder->status === 'confirmed')
                        <form action="{{ route('sales-orders.deliver', $salesOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من تسليم أمر البيع؟')">
                                تسليم أمر البيع
                            </button>
                        </form>
                        <form action="{{ route('sales-orders.invoice', $salesOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من إنشاء الفاتورة؟')">
                                إنشاء فاتورة
                            </button>
                        </form>
                    @elseif($salesOrder->status === 'delivered')
                        <form action="{{ route('sales-orders.invoice', $salesOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من إنشاء الفاتورة؟')">
                                إنشاء فاتورة
                            </button>
                        </form>
                    @endif

                    @if($salesOrder->status !== 'cancelled' && $salesOrder->status !== 'invoiced')
                        <form action="{{ route('sales-orders.cancel', $salesOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-yellow-100 px-4 py-2 text-sm font-medium text-yellow-700 hover:bg-yellow-200 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من إلغاء أمر البيع؟')">
                                إلغاء أمر البيع
                            </button>
                        </form>
                    @endif

                    <button onclick="window.print()" class="w-full rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition cursor-pointer">
                        طباعة أمر البيع
                    </button>
                    <a href="{{ route('sales-orders.index') }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                        العودة للقائمة
                    </a>
                </div>
            </div>

            @if($salesOrder->invoices->count() > 0)
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">الفواتير المرتبطة</h4>
                <div class="space-y-2">
                    @foreach($salesOrder->invoices as $invoice)
                        <a href="{{ route('sales-invoices.show', $invoice) }}" class="block rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-blue-600">{{ $invoice->invoice_number }}</span>
                                <span class="font-mono text-sm">{{ number_format($invoice->total, 2) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
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
