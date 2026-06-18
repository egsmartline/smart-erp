<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">عرض أسعار - {{ $quotation->quotation_number }}</h2>
            <div class="flex items-center gap-2">
                <x-print-button url="{{ route('pdf.quotation', $quotation) }}" label="تحميل PDF" />
                <a href="{{ route('quotations.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
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
                        <h3 class="text-lg font-bold text-blue-700">عرض أسعار</h3>
                        <p class="text-sm text-gray-500">Quotation # {{ $quotation->quotation_number }}</p>
                    </div>
                    <div>
                        @if($quotation->status === 'draft')
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800">مسودة</span>
                        @elseif($quotation->status === 'sent')
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">مرسل</span>
                        @elseif($quotation->status === 'accepted')
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">مقبول</span>
                        @elseif($quotation->status === 'rejected')
                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">مرفوض</span>
                        @elseif($quotation->status === 'converted')
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-sm font-medium text-purple-800">محول</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500">العميل</p>
                        <p class="font-medium text-gray-900">{{ $quotation->customer->name ?? '-' }}</p>
                        <p class="text-sm text-gray-600">{{ $quotation->customer->phone ?? '' }}</p>
                    </div>
                    <div class="text-left">
                        <p class="text-xs text-gray-500">التاريخ والصلاحية</p>
                        <p class="font-medium text-gray-900">{{ $quotation->date->format('Y-m-d') }}</p>
                        <p class="text-sm text-gray-600">صالح حتى: {{ $quotation->valid_until->format('Y-m-d') }}</p>
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
                            @foreach($quotation->lines as $index => $line)
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
                                <td class="px-3 py-3 text-left font-mono">{{ number_format($quotation->subtotal, 2) }}</td>
                            </tr>
                            @if($quotation->discount_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الخصم</td>
                                <td class="px-3 py-3 text-left font-mono text-red-600">- {{ number_format($quotation->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($quotation->tax_amount > 0)
                            <tr class="bg-gray-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الضريبة</td>
                                <td class="px-3 py-3 text-left font-mono text-emerald-600">+ {{ number_format($quotation->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-blue-50">
                                <td colspan="5" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm font-bold text-gray-800">الإجمالي</td>
                                <td class="px-3 py-3 text-left font-mono text-lg font-bold text-blue-700">{{ number_format($quotation->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($quotation->notes)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500">ملاحظات</p>
                        <p class="text-sm text-gray-700">{{ $quotation->notes }}</p>
                    </div>
                @endif

                @if($quotation->terms)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500">الشروط والأحكام</p>
                        <p class="text-sm text-gray-700">{{ $quotation->terms }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">إجراءات</h4>
                <div class="space-y-2">
                    <button onclick="window.print()" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">طباعة العرض</button>

                    @if($quotation->status === 'draft')
                        <form action="{{ route('quotations.send', $quotation) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition cursor-pointer">إرسال العرض</button>
                        </form>
                        <a href="{{ route('quotations.edit', $quotation) }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">تعديل العرض</a>
                        <form action="{{ route('quotations.destroy', $quotation) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                حذف العرض
                            </button>
                        </form>
                    @endif

                    @if(in_array($quotation->status, ['draft', 'sent']))
                        <form action="{{ route('quotations.accept', $quotation) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-green-100 px-4 py-2 text-sm font-medium text-green-700 hover:bg-green-200 transition cursor-pointer">قبول العرض</button>
                        </form>
                        <form action="{{ route('quotations.reject', $quotation) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition cursor-pointer">رفض العرض</button>
                        </form>
                    @endif

                    @if(in_array($quotation->status, ['draft', 'sent', 'accepted']) && !$quotation->converted_to_invoice)
                        <form action="{{ route('quotations.convert', $quotation) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من التحويل إلى فاتورة مبيعات؟')">
                                تحويل إلى فاتورة مبيعات
                            </button>
                        </form>
                    @endif

                    @if($quotation->converted_to_invoice)
                        <div class="rounded-lg bg-purple-50 p-3 text-sm text-purple-800 border border-purple-200 text-center">
                            تم التحويل إلى فاتورة مبيعات
                        </div>
                    @endif

                    <a href="{{ route('quotations.index') }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">العودة للقائمة</a>
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
