<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">{{ $documentArchive->title }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('document-archives.download', $documentArchive) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تحميل
                </a>
                <a href="{{ route('document-archives.edit', $documentArchive) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                    تعديل
                </a>
                <a href="{{ route('document-archives.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">العودة</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">تفاصيل المستند</h3>

                @php
                    $ext = strtolower(pathinfo($documentArchive->file_name, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                    $isPdf = $ext === 'pdf';
                @endphp

                @if($isImage)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $documentArchive->file_path) }}" alt="{{ $documentArchive->title }}" class="max-w-full rounded-lg border border-gray-200">
                    </div>
                @elseif($isPdf)
                    <div class="mb-4">
                        <iframe src="{{ asset('storage/' . $documentArchive->file_path) }}" class="w-full h-[600px] rounded-lg border border-gray-200"></iframe>
                    </div>
                @else
                    <div class="mb-4 p-8 text-center bg-gray-50 rounded-lg border border-gray-200">
                        <svg class="h-16 w-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <p class="text-gray-500">لا يمكن عرض هذا النوع من الملفات مباشرة</p>
                        <a href="{{ route('document-archives.download', $documentArchive) }}" class="mt-2 inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            تحميل الملف
                        </a>
                    </div>
                @endif

                @if($documentArchive->description)
                    <div class="mt-4">
                        <h4 class="text-sm font-bold text-gray-700 mb-1">الوصف</h4>
                        <p class="text-sm text-gray-600">{{ $documentArchive->description }}</p>
                    </div>
                @endif

                @if($documentArchive->notes)
                    <div class="mt-4">
                        <h4 class="text-sm font-bold text-gray-700 mb-1">ملاحظات</h4>
                        <p class="text-sm text-gray-600">{{ $documentArchive->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">معلومات الملف</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">اسم الملف:</span>
                        <span class="font-mono text-xs font-medium text-gray-900">{{ $documentArchive->file_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">النوع:</span>
                        <span class="font-medium text-gray-900">{{ $documentArchive->file_type ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">الحجم:</span>
                        <span class="font-medium text-gray-900">{{ $documentArchive->file_size ? number_format($documentArchive->file_size / 1024, 1) . ' KB' : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">التصنيف:</span>
                        <span class="font-medium text-gray-900">{{ $documentArchive->category ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاريخ الرفع:</span>
                        <span class="font-medium text-gray-900">{{ $documentArchive->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">آخر تحديث:</span>
                        <span class="font-medium text-gray-900">{{ $documentArchive->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">تم الرفع بواسطة:</span>
                        <span class="font-medium text-gray-900">{{ $documentArchive->uploader->name ?? 'غير معروف' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-4">
                <form action="{{ route('document-archives.destroy', $documentArchive) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition cursor-pointer">
                        حذف المستند
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
