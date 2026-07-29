<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إشعار خصم: {{ $discountNote->note_number }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('discount-notes.edit', $discountNote) }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-600 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل
                </a>
                @if($discountNote->status === 'draft')
                    <form action="{{ route('discount-notes.post', $discountNote) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الترحيل؟ سيتم إنشاء قيد محاسبي.')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition cursor-pointer">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            ترحيل
                        </button>
                    </form>
                @endif
                <form action="{{ route('discount-notes.destroy', $discountNote) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600 transition cursor-pointer">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        حذف
                    </button>
                </form>
                <a href="{{ route('discount-notes.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للقائمة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">بيانات الإشعار</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">رقم الإشعار:</span><span class="font-mono font-bold">{{ $discountNote->note_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">التاريخ:</span><span class="font-medium">{{ $discountNote->date->format('Y-m-d') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الحالة:</span>
                    @if($discountNote->status === 'draft')
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                    @elseif($discountNote->status === 'posted')
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مرحل</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">ملغي</span>
                    @endif
                </div>
                <div class="flex justify-between"><span class="text-gray-500">تاريخ الإنشاء:</span><span class="font-medium">{{ $discountNote->created_at->format('Y-m-d H:i') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">بواسطة:</span><span class="font-medium">{{ $discountNote->creator->name ?? 'نظام' }}</span></div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">العميل</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">الاسم:</span><span class="font-medium">{{ $discountNote->customer->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الهاتف:</span><span class="font-medium">{{ $discountNote->customer->phone ?? $discountNote->customer->mobile ?? '-' }}</span></div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">المبلغ</h3>
            <div class="text-center py-6">
                <div class="text-3xl font-bold text-red-600">{{ number_format($discountNote->amount, 2) }}</div>
                <div class="text-sm text-gray-500 mt-2">قيمة الخصم</div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">تفاصيل الخصم</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">فاتورة المبيعات:</span><span class="font-medium">{{ $discountNote->salesInvoice->invoice_number ?? 'بدون فاتورة' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">سبب الخصم:</span><span class="font-medium">{{ $discountNote->reason ?? '-' }}</span></div>
            @if($discountNote->notes)
                <div class="flex justify-between"><span class="text-gray-500">ملاحظات:</span><span class="font-medium">{{ $discountNote->notes }}</span></div>
            @endif
        </div>
    </div>
</x-app-layout>
