<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل شرط الدفع</h2>
            <a href="{{ route('payment-terms.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للقائمة</a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">اسم شرط الدفع</label>
                <p class="text-gray-900 text-sm">{{ $paymentTerm->name }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">الاسم الإنجليزي</label>
                <p class="text-gray-900 text-sm">{{ $paymentTerm->name_en ?? '-' }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">نوع الخصم</label>
                <p class="text-gray-900 text-sm">
                    @if($paymentTerm->discount_type === 'percentage')
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">نسبة مئوية</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مبلغ ثابت</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">نسبة الخصم</label>
                <p class="text-gray-900 text-sm">{{ $paymentTerm->discount_percent }}%</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">أيام الاستحقاق</label>
                <p class="text-gray-900 text-sm">{{ $paymentTerm->days_net }} يوم</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">أيام الخصم</label>
                <p class="text-gray-900 text-sm">{{ $paymentTerm->days_discount ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-500">ملاحظات</label>
                <p class="text-gray-900 text-sm">{{ $paymentTerm->note ?? '-' }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">الحالة</label>
                <p class="text-gray-900 text-sm">
                    @if($paymentTerm->is_active)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">نشط</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">غير نشط</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 border-t border-gray-200 pt-6 mt-6">
            <a href="{{ route('payment-terms.edit', $paymentTerm->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
            <a href="{{ route('payment-terms.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </div>
</x-app-layout>
