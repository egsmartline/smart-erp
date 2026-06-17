<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إضافة شرط دفع جديد</h2>
            <a href="{{ route('payment-terms.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                إلغاء
            </a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form action="{{ route('payment-terms.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">اسم شرط الدفع <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="مثال: دفع خلال 30 يوم">
                </div>

                <div>
                    <label for="name_en" class="mb-1 block text-sm font-medium text-gray-700">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" id="name_en" value="{{ old('name_en') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="مثال: Net 30">
                </div>

                <div>
                    <label for="discount_type" class="mb-1 block text-sm font-medium text-gray-700">نوع الخصم <span class="text-red-500">*</span></label>
                    <select name="discount_type" id="discount_type" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر النوع</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                        <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                    </select>
                </div>

                <div>
                    <label for="discount_percent" class="mb-1 block text-sm font-medium text-gray-700">نسبة الخصم (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="discount_percent" id="discount_percent" value="{{ old('discount_percent', 0) }}" step="0.01" min="0" max="100" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="days_net" class="mb-1 block text-sm font-medium text-gray-700">أيام الاستحقاق <span class="text-red-500">*</span></label>
                    <input type="number" name="days_net" id="days_net" value="{{ old('days_net', 0) }}" min="0" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="عدد الأيام">
                </div>

                <div>
                    <label for="days_discount" class="mb-1 block text-sm font-medium text-gray-700">أيام الخصم</label>
                    <input type="number" name="days_discount" id="days_discount" value="{{ old('days_discount') }}" min="0"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="عدد الأيام">
                </div>

                <div class="md:col-span-2">
                    <label for="note" class="mb-1 block text-sm font-medium text-gray-700">ملاحظات</label>
                    <textarea name="note" id="note" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="ملاحظات إضافية...">{{ old('note') }}</textarea>
                </div>

                <div class="flex items-end">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">نشط</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    حفظ شرط الدفع
                </button>
                <a href="{{ route('payment-terms.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
