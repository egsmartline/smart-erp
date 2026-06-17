<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">أمر شراء - {{ $purchaseOrder->order_number }}</h2>
            <a href="{{ route('purchase-orders.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                العودة للقائمة
            </a>
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
                        <h3 class="text-lg font-bold text-blue-700">أمر شراء</h3>
                        <p class="text-sm text-gray-500">{{ $purchaseOrder->order_number }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($purchaseOrder->status === 'draft')
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">مسودة</span>
                        @elseif($purchaseOrder->status === 'confirmed')
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">مؤكد</span>
                        @elseif($purchaseOrder->status === 'received')
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-sm font-medium text-purple-800">تم الاستلام</span>
                        @elseif($purchaseOrder->status === 'invoiced')
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">تم الفوترة</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">ملغي</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500">المورد</p>
                        <p class="font-medium text-gray-900">{{ $purchaseOrder->supplier->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">المخزن</p>
                        <p class="font-medium text-gray-900">{{ $purchaseOrder->warehouse->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">التاريخ</p>
                        <p class="font-medium text-gray-900">{{ $purchaseOrder->date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">التاريخ المتوقع</p>
                        <p class="font-medium text-gray-900">{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('Y-m-d') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">منشئ الأمر</p>
                        <p class="font-medium text-gray-900">{{ $purchaseOrder->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">شروط الدفع</p>
                        <p class="font-medium text-gray-900">{{ $purchaseOrder->paymentTerm->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">حالة الاستلام</p>
                        @if($purchaseOrder->receipt_status === 'pending')
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">قيد الانتظار</span>
                        @elseif($purchaseOrder->receipt_status === 'partial')
                            <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">جزئي</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">تم الاستلام</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">حالة الفوترة</p>
                        @if($purchaseOrder->invoice_status === 'not_invoiced')
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">غير مفوتر</span>
                        @elseif($purchaseOrder->invoice_status === 'partially_invoiced')
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
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">تم الاستلام</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">التكلفة</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الخصم</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الضريبة</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->lines as $index => $line)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $line->item->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->quantity, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->received_qty, 2) }}</td>
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
                                <td class="px-3 py-3 text-left font-mono" colspan="2">{{ number_format($purchaseOrder->subtotal, 2) }}</td>
                            </tr>
                            @if($purchaseOrder->discount_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الخصم</td>
                                <td class="px-3 py-3 text-left font-mono text-red-600" colspan="2">- {{ number_format($purchaseOrder->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($purchaseOrder->tax_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الضريبة</td>
                                <td class="px-3 py-3 text-left font-mono text-emerald-600" colspan="2">+ {{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-blue-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm font-bold text-gray-800">الإجمالي</td>
                                <td class="px-3 py-3 text-left font-mono text-lg font-bold text-blue-700" colspan="2">{{ number_format($purchaseOrder->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($purchaseOrder->notes)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500">ملاحظات</p>
                        <p class="text-sm text-gray-700">{{ $purchaseOrder->notes }}</p>
                    </div>
                @endif

                @if($purchaseOrder->terms)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500">الشروط</p>
                        <p class="text-sm text-gray-700">{{ $purchaseOrder->terms }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">إجراءات</h4>
                <div class="space-y-2">
                    @if($purchaseOrder->status === 'draft')
                        <form action="{{ route('purchase-orders.confirm', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من تأكيد أمر الشراء؟')">
                                تأكيد أمر الشراء
                            </button>
                        </form>
                        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                            تعديل أمر الشراء
                        </a>
                        <form action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                حذف أمر الشراء
                            </button>
                        </form>
                    @elseif($purchaseOrder->status === 'confirmed')
                        <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من استلام أمر الشراء؟')">
                                استلام أمر الشراء
                            </button>
                        </form>
                        <form action="{{ route('purchase-orders.invoice', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من إنشاء فاتورة المشتريات؟')">
                                إنشاء فاتورة مشتريات
                            </button>
                        </form>
                    @elseif($purchaseOrder->status === 'received')
                        <form action="{{ route('purchase-orders.invoice', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من إنشاء فاتورة المشتريات؟')">
                                إنشاء فاتورة مشتريات
                            </button>
                        </form>
                    @endif

                    @if($purchaseOrder->status !== 'cancelled' && $purchaseOrder->status !== 'invoiced')
                        <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-yellow-100 px-4 py-2 text-sm font-medium text-yellow-700 hover:bg-yellow-200 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من إلغاء أمر الشراء؟')">
                                إلغاء أمر الشراء
                            </button>
                        </form>
                    @endif

                    <button onclick="window.print()" class="w-full rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition cursor-pointer">
                        طباعة أمر الشراء
                    </button>
                    <a href="{{ route('purchase-orders.index') }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                        العودة للقائمة
                    </a>
                </div>
            </div>

            @if($purchaseOrder->invoices->count() > 0)
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">الفواتير المرتبطة</h4>
                <div class="space-y-2">
                    @foreach($purchaseOrder->invoices as $invoice)
                        <a href="{{ route('purchase-invoices.show', $invoice) }}" class="block rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
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
