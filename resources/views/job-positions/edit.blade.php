<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تعديل الوظيفة: {{ $position->name }}</h2>
            <a href="{{ route('job-positions.show', $position) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">إلغاء</a>
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
        <form action="{{ route('job-positions.update', $position) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">اسم الوظيفة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $position->name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="code" class="mb-1 block text-sm font-medium text-gray-700">الكود</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $position->code) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="department_id" class="mb-1 block text-sm font-medium text-gray-700">القسم <span class="text-red-500">*</span></label>
                    <select name="department_id" id="department_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id', $position->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="min_salary" class="mb-1 block text-sm font-medium text-gray-700">الحد الأدنى للراتب</label>
                    <input type="number" name="min_salary" id="min_salary" step="0.01" min="0" value="{{ old('min_salary', $position->min_salary) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="max_salary" class="mb-1 block text-sm font-medium text-gray-700">الحد الأقصى للراتب</label>
                    <input type="number" name="max_salary" id="max_salary" step="0.01" min="0" value="{{ old('max_salary', $position->max_salary) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 cursor-pointer pb-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $position->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">نشط</span>
                    </label>
                </div>
                <div class="md:col-span-3">
                    <label for="description" class="mb-1 block text-sm font-medium text-gray-700">الوصف</label>
                    <textarea name="description" id="description" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('description', $position->description) }}</textarea>
                </div>
            </div>
            <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">تحديث الوظيفة</button>
                <a href="{{ route('job-positions.show', $position) }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">إلغاء</a>
            </div>
        </form>
    </div>
</x-app-layout>
