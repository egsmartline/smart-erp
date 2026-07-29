<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">التصنيفات</h2>
            <a href="{{ route('document-archives.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">العودة للأرشيف</a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">إضافة تصنيف جديد</h3>
            <form method="POST" action="{{ route('document-archives.categories-store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">الاسم <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">وصف</label>
                        <input type="text" name="description" value="{{ old('description') }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">ترتيب</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">إضافة</button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">التصنيفات الحالية</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 font-semibold text-gray-700">الاسم</th>
                                <th class="px-4 py-3 font-semibold text-gray-700">الوصف</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-center">الترتيب</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $cat)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 font-medium">{{ $cat->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $cat->description ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">{{ $cat->sort_order }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <form method="POST" action="{{ route('document-archives.categories-update', $cat) }}" class="inline-flex items-center gap-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $cat->name }}" required
                                                    class="w-24 rounded border border-gray-300 px-2 py-1 text-xs focus:border-blue-500">
                                                <input type="number" name="sort_order" value="{{ $cat->sort_order }}" min="0"
                                                    class="w-14 rounded border border-gray-300 px-2 py-1 text-xs focus:border-blue-500">
                                                <button type="submit" class="rounded p-1 text-blue-600 hover:bg-blue-50 transition cursor-pointer" title="حفظ">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('document-archives.categories-destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded p-1 text-red-500 hover:bg-red-50 transition cursor-pointer" title="حذف">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">لا توجد تصنيفات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $categories->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
