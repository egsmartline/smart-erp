<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">مرتجع مشتريات - {{ $purchaseReturn->return_number }}</h2>
            <a href="{{ route('purchase-returns.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                العودة للقائمة
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6" id="printArea">
                <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-blue-700">مرتجع مشتريات</h3>
                        <p class="text-sm text-gray-500">{{ $purchaseReturn->return_number }}</p>
                    </div>
                    <div>
                        @if($purchaseReturn->status === 'draft')
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">مسودة</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">مرحل</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500">المورد</p>
                        <p class="font-medium text-gray-900">{{ $purchaseReturn->supplier->name ?? '-' }}</p>
                    </div>
                    <div class="text-left">
                        <p class="text-xs text-gray-500">فاتورة المشتريات</p>
                        <p class="font-medium text-gray-900">{{ $purchaseReturn->purchaseInvoice->invoice_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">التاريخ</p>
                        <p class="font-medium text-gray-900">{{ $purchaseReturn->date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">سبب الإرجاع</p>
                        <p class="font-medium text-gray-900">{{ $purchaseReturn->reason ?? '-' }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-3 py-2 font-semibold text-gray-700">#</th>
                                <th class="px-3 py-2 font-semibold text-gray-700">الصنف</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الكمية</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">تكلفة الوحدة</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الضريبة</th>
                                <th class="px-3 py-2 font-semibold text-gray-700 text-left">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseReturn->lines as $index => $line)
                                <tr class="border-b border-gray-100">
                                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $line->item->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->quantity, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->unit_cost, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono">{{ number_format($line->tax_amount, 2) }}</td>
                                    <td class="px-3 py-2 text-left font-mono font-medium">{{ number_format($line->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold">
                                <td colspan="4" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">المجموع الفرعي</td>
                                <td class="px-3 py-3 text-left font-mono">{{ number_format($purchaseReturn->subtotal, 2) }}</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td colspan="4" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm text-gray-600">الضريبة</td>
                                <td class="px-3 py-3 text-left font-mono text-emerald-600">+ {{ number_format($purchaseReturn->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="bg-blue-50">
                                <td colspan="4" class="px-3 py-3"></td>
                                <td class="px-3 py-3 text-left text-sm font-bold text-gray-800">الإجمالي</td>
                                <td class="px-3 py-3 text-left font-mono text-lg font-bold text-blue-700">{{ number_format($purchaseReturn->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($purchaseReturn->notes)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500">ملاحظات</p>
                        <p class="text-sm text-gray-700">{{ $purchaseReturn->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">إجراءات</h4>
                <div class="space-y-2">
                    <button @click="$root.closest('[x-data]')?.__x?.$data.printModalOpen = true" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">طباعة المرتجع</button>
                    @if($purchaseReturn->status === 'draft')
                        <form action="{{ route('purchase-returns.post', $purchaseReturn) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition cursor-pointer" onclick="return confirm('هل أنت متأكد من الترحيل؟ سيتم خصم المخزون.')">
                                ترحيل المرتجع
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('purchase-returns.index') }}" class="block w-full rounded-lg bg-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-300 transition">العودة للقائمة</a>
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
