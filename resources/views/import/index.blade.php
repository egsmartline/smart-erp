<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">استيراد وتصدير البيانات</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    العودة
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">اختر نوع البيانات لاستيرادها من ملف Excel أو تصديرها</p>
        </div>
        <div class="flex items-center gap-3 text-sm text-gray-500">
            <span class="flex items-center gap-1"><span class="h-3 w-3 rounded-full bg-blue-500"></span> استيراد</span>
            <span class="flex items-center gap-1"><span class="h-3 w-3 rounded-full bg-emerald-500"></span> تصدير</span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        {{-- العملاء --}}
        <div class="rounded-xl border-2 border-gray-200 bg-white p-6 shadow-sm hover:border-blue-300 hover:shadow-md transition-all {{ request('type') === 'customers' ? 'border-blue-400 ring-2 ring-blue-200' : '' }}">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 shadow-sm">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">العملاء</h3>
                    <p class="text-sm text-gray-500">استيراد وتصدير بيانات العملاء</p>
                </div>
                <a href="{{ route('import.export', 'customers') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-600 transition shadow-sm" title="تحميل نموذج"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> نموذج</a>
            </div>
            <form action="{{ route('import.do') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="customers">
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center hover:border-blue-400 transition bg-gradient-to-br from-gray-50 to-blue-50/30">
                    <p class="mb-2 text-xs text-gray-400">اختيار ملف Excel للاستيراد</p>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" required>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        استيراد
                    </button>
                    <a href="{{ route('import.export', 'customers') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        تصدير
                    </a>
                </div>
            </form>
        </div>

        {{-- الموردين --}}
        <div class="rounded-xl border-2 border-gray-200 bg-white p-6 shadow-sm hover:border-blue-300 hover:shadow-md transition-all {{ request('type') === 'suppliers' ? 'border-blue-400 ring-2 ring-blue-200' : '' }}">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 shadow-sm">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">الموردين</h3>
                    <p class="text-sm text-gray-500">استيراد وتصدير بيانات الموردين</p>
                </div>
                <a href="{{ route('import.export', 'suppliers') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-600 transition shadow-sm" title="تحميل نموذج"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> نموذج</a>
            </div>
            <form action="{{ route('import.do') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="suppliers">
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center hover:border-blue-400 transition bg-gradient-to-br from-gray-50 to-blue-50/30">
                    <p class="mb-2 text-xs text-gray-400">اختيار ملف Excel للاستيراد</p>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" required>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        استيراد
                    </button>
                    <a href="{{ route('import.export', 'suppliers') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        تصدير
                    </a>
                </div>
            </form>
        </div>

        {{-- الأصناف --}}
        <div class="rounded-xl border-2 border-gray-200 bg-white p-6 shadow-sm hover:border-emerald-300 hover:shadow-md transition-all {{ request('type') === 'items' ? 'border-emerald-400 ring-2 ring-emerald-200' : '' }}">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-600 shadow-sm">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">الأصناف</h3>
                    <p class="text-sm text-gray-500">استيراد وتصدير بيانات الأصناف</p>
                </div>
                <a href="{{ route('import.export', 'items') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-600 transition shadow-sm" title="تحميل نموذج"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> نموذج</a>
            </div>
            <form action="{{ route('import.do') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="items">
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center hover:border-emerald-400 transition bg-gradient-to-br from-gray-50 to-emerald-50/30">
                    <p class="mb-2 text-xs text-gray-400">اختيار ملف Excel للاستيراد</p>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" required>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        استيراد
                    </button>
                    <a href="{{ route('import.export', 'items') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        تصدير
                    </a>
                </div>
            </form>
        </div>

        {{-- دليل الحسابات --}}
        <div class="rounded-xl border-2 border-gray-200 bg-white p-6 shadow-sm hover:border-blue-300 hover:shadow-md transition-all {{ request('type') === 'accounts' ? 'border-blue-400 ring-2 ring-blue-200' : '' }}">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 shadow-sm">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">دليل الحسابات</h3>
                    <p class="text-sm text-gray-500">استيراد وتصدير دليل الحسابات</p>
                </div>
                <a href="{{ route('import.export', 'accounts') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-600 transition shadow-sm" title="تحميل نموذج"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> نموذج</a>
            </div>
            <form action="{{ route('import.do') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="accounts">
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center hover:border-blue-400 transition bg-gradient-to-br from-gray-50 to-blue-50/30">
                    <p class="mb-2 text-xs text-gray-400">اختيار ملف Excel للاستيراد</p>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" required>
                </div>
                <div class="mt-3 flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        استيراد
                    </button>
                    <a href="{{ route('import.export', 'accounts') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        تصدير
                    </a>
                </div>
            </form>
        </div>
    </div>

@if(session('import_debug'))
<div class="mt-6 rounded-xl border border-blue-200 bg-blue-50 p-4">
    <h4 class="mb-2 text-sm font-bold text-blue-800">معلومات التصحيح (Debug)</h4>
    <div class="space-y-2 text-xs font-mono text-blue-900">
        <div>
            <span class="font-bold">الترويسة الأصلية (Headers Raw):</span>
            <pre class="mt-1 whitespace-pre-wrap rounded bg-blue-100 p-2">{{ json_encode(session('import_debug')['headers_raw'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
        </div>
        <div>
            <span class="font-bold">خريطة الأعمدة (Column Map):</span>
            <pre class="mt-1 whitespace-pre-wrap rounded bg-blue-100 p-2">{{ json_encode(session('import_debug')['colMap'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
        </div>
        <div>
            <span class="font-bold">أول صف بيانات (First Data Row):</span>
            <pre class="mt-1 whitespace-pre-wrap rounded bg-blue-100 p-2">{{ json_encode(session('import_debug')['first_data_row'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endif

</x-app-layout>