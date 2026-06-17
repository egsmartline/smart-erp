<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل الضريبة: {{ $tax->name }}</h2>
            <a href="{{ route('taxes.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
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
        <form action="{{ route('taxes.update', $tax) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">اسم الضريبة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $tax->name) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="name_en" class="mb-1 block text-sm font-medium text-gray-700">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" id="name_en" value="{{ old('name_en', $tax->name_en) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="code" class="mb-1 block text-sm font-medium text-gray-700">الكود <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $tax->code) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="type" class="mb-1 block text-sm font-medium text-gray-700">النوع <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="fixed" {{ old('type', $tax->type) === 'fixed' ? 'selected' : '' }}>ثابتة</option>
                        <option value="percentage" {{ old('type', $tax->type) === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                        <option value="group" {{ old('type', $tax->type) === 'group' ? 'selected' : '' }}>مجموعة</option>
                    </select>
                </div>

                <div>
                    <label for="amount_type" class="mb-1 block text-sm font-medium text-gray-700">نوع المبلغ <span class="text-red-500">*</span></label>
                    <select name="amount_type" id="amount_type" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="fixed" {{ old('amount_type', $tax->amount_type) === 'fixed' ? 'selected' : '' }}>ثابت</option>
                        <option value="percent" {{ old('amount_type', $tax->amount_type) === 'percent' ? 'selected' : '' }}>نسبة مئوية</option>
                        <option value="group" {{ old('amount_type', $tax->amount_type) === 'group' ? 'selected' : '' }}>مجموعة</option>
                        <option value="division" {{ old('amount_type', $tax->amount_type) === 'division' ? 'selected' : '' }}>قسمة</option>
                    </select>
                </div>

                <div>
                    <label for="rate" class="mb-1 block text-sm font-medium text-gray-700">النسبة / المعدل <span class="text-red-500">*</span></label>
                    <input type="number" name="rate" id="rate" value="{{ old('rate', $tax->rate) }}" step="0.01" min="0" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label for="tax_group_id" class="mb-1 block text-sm font-medium text-gray-700">مجموعة الضرائب</label>
                    <select name="tax_group_id" id="tax_group_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">بدون مجموعة</option>
                        @foreach($taxGroups as $group)
                            <option value="{{ $group->id }}" {{ old('tax_group_id', $tax->tax_group_id) == $group->id ? 'selected' : '' }}>
                                {{ $group->code }} - {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="account_id" class="mb-1 block text-sm font-medium text-gray-700">حساب المبيعات</label>
                    <select name="account_id" id="account_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الحساب</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id', $tax->account_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="purchase_account_id" class="mb-1 block text-sm font-medium text-gray-700">حساب المشتريات</label>
                    <select name="purchase_account_id" id="purchase_account_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">اختر الحساب</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('purchase_account_id', $tax->purchase_account_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="description" class="mb-1 block text-sm font-medium text-gray-700">الوصف</label>
                    <input type="text" name="description" id="description" value="{{ old('description', $tax->description) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="flex items-end gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tax->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">نشط</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $tax->is_default) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">افتراضي</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_included_in_price" value="1" {{ old('is_included_in_price', $tax->is_included_in_price) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">مضمن في السعر</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    تحديث الضريبة
                </button>
                <a href="{{ route('taxes.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
