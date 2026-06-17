<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل الحساب التحليلي</h2>
            <a href="{{ route('analytical-accounts.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">العودة للقائمة</a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('analytical-accounts.update', $analyticalAccount) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الكود <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $analyticalAccount->code) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('code')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $analyticalAccount->name) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">النوع <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $analyticalAccount->type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">الحساب الأب</label>
                    <select name="parent_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">بدون</option>
                        @foreach($parentAccounts as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $analyticalAccount->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->code }} - {{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">مبلغ الميزانية</label>
                    <input type="number" name="budget_amount" value="{{ old('budget_amount', $analyticalAccount->budget_amount) }}" step="0.01" min="0"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('budget_amount')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-6">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $analyticalAccount->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label class="text-sm font-medium text-gray-700">نشط</label>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">تحديث</button>
                <a href="{{ route('analytical-accounts.index') }}" class="rounded-lg bg-gray-200 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
