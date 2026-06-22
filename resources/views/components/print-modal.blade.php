@props(['title' => 'طباعة الصفحة'])
<div x-show="printModalOpen"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm no-print"
    @keydown.escape.window="printModalOpen = false">
    <div @click.away="printModalOpen = false"
        class="mx-4 w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
        {{-- Header --}}
        <div class="bg-gradient-to-l from-primary-600 to-primary-800 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">{{ $title }}</h3>
                    <p class="text-sm text-primary-200">اختر تنسيق الطباعة المناسب</p>
                </div>
            </div>
        </div>

        {{-- Options --}}
        <div class="space-y-4 p-6">
            <p class="text-sm text-gray-600">هل تريد تضمين شعار الشركة وبياناتها في الطباعة؟</p>

            <div class="grid grid-cols-2 gap-4">
                <button @click="includeLogo = true; printModalOpen = false; document.body.classList.add('print-with-logo'); setTimeout(() => { window.print(); document.body.classList.remove('print-with-logo'); }, 200)"
                    class="group relative flex flex-col items-center gap-3 rounded-xl border-2 border-primary-200 bg-primary-50/50 p-5 transition-all hover:border-primary-500 hover:bg-primary-50 hover:shadow-md">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-primary-100 text-primary-600 group-hover:bg-primary-200 transition">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-center">
                        <span class="block text-sm font-bold text-gray-800">مع الشعار</span>
                        <span class="text-xs text-gray-500">شامل الشعار والبيانات</span>
                    </div>
                    <div class="absolute left-3 top-3 flex h-5 w-5 items-center justify-center rounded-full border-2 border-primary-300 group-hover:border-primary-600">
                        <div x-show="includeLogo" class="h-3 w-3 rounded-full bg-primary-600"></div>
                    </div>
                </button>

                <button @click="includeLogo = false; printModalOpen = false; document.body.classList.add('print-without-logo'); setTimeout(() => { window.print(); document.body.classList.remove('print-without-logo'); }, 200)"
                    class="group relative flex flex-col items-center gap-3 rounded-xl border-2 border-gray-200 bg-gray-50 p-5 transition-all hover:border-gray-400 hover:bg-gray-100 hover:shadow-md">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 text-gray-500 group-hover:bg-gray-200 transition">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="text-center">
                        <span class="block text-sm font-bold text-gray-800">بدون شعار</span>
                        <span class="text-xs text-gray-500">المحتوى فقط</span>
                    </div>
                    <div class="absolute left-3 top-3 flex h-5 w-5 items-center justify-center rounded-full border-2 border-gray-300 group-hover:border-gray-500">
                        <div x-show="!includeLogo" class="h-3 w-3 rounded-full bg-gray-500"></div>
                    </div>
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-6 py-3">
            <p class="text-xs text-gray-500">سيتم فتح نافذة الطباعة للنظام</p>
            <button @click="printModalOpen = false" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                إلغاء
            </button>
        </div>
    </div>
</div>
