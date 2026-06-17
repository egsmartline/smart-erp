<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل الدفتر</h2>
            <a href="{{ route('journals.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للقائمة</a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">كود الدفتر</label>
                <p class="text-gray-900 text-sm">{{ $journal->code }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">اسم الدفتر</label>
                <p class="text-gray-900 text-sm">{{ $journal->name }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">النوع</label>
                <p class="text-gray-900 text-sm">
                    @php
                        $typeLabels = [
                            'sale' => 'مبيعات',
                            'purchase' => 'مشتريات',
                            'cash' => 'نقدية',
                            'bank' => 'بنكية',
                            'general' => 'عامة',
                        ];
                    @endphp
                    {{ $typeLabels[$journal->type] ?? $journal->type }}
                </p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">الحساب الافتراضي</label>
                <p class="text-gray-900 text-sm">{{ $journal->defaultAccount->name ?? '-' }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">العملة</label>
                <p class="text-gray-900 text-sm">{{ $journal->currency->name ?? '-' }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">الحالة</label>
                <p class="text-gray-900 text-sm">
                    @if($journal->is_active)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">نشط</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">غير نشط</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 border-t border-gray-200 pt-6 mt-6">
            <a href="{{ route('journals.edit', $journal->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
            <a href="{{ route('journals.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </div>
</x-app-layout>
