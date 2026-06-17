<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل العملة</h2>
            <a href="{{ route('currencies.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">العودة للقائمة</a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">اسم العملة</label>
                <p class="text-gray-900 text-sm">{{ $currency->name }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">الكود</label>
                <p class="text-gray-900 text-sm">{{ $currency->code }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">الرمز</label>
                <p class="text-gray-900 text-sm">{{ $currency->symbol }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">سعر الصرف</label>
                <p class="text-gray-900 text-sm">{{ $currency->exchange_rate }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-500">العملة الافتراضية</label>
                <p class="text-gray-900 text-sm">{{ $currency->is_default ? 'نعم' : 'لا' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 border-t border-gray-200 pt-6 mt-6">
            <a href="{{ route('currencies.edit', $currency->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">تعديل</a>
            <a href="{{ route('currencies.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </div>
</x-app-layout>
