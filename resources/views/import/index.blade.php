<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">استيراد وتصدير البيانات</h2>
    </x-slot>

    <div class="mb-6">
        <p class="text-sm text-gray-500">استيراد البيانات من ملفات Excel أو تصدير البيانات إلى ملفات Excel</p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        {{-- العملاء --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">العملاء</h3>
                    <p class="text-sm text-gray-500">استيراد وتصدير بيانات العملاء</p>
                </div>
            </div>
            <form action="{{ route('import.do') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="type" value="customers">
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center hover:border-primary-400 transition bg-gray-50 hover:bg-primary-50/30">
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer" required>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
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
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">الموردين</h3>
                    <p class="text-sm text-gray-500">استيراد وتصدير بيانات الموردين</p>
                </div>
            </div>
            <form action="{{ route('import.do') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="type" value="suppliers">
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center hover:border-primary-400 transition bg-gray-50 hover:bg-primary-50/30">
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer" required>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
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
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">الأصناف</h3>
                    <p class="text-sm text-gray-500">استيراد وتصدير بيانات الأصناف</p>
                </div>
            </div>
            <form action="{{ route('import.do') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="type" value="items">
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center hover:border-emerald-400 transition bg-gray-50 hover:bg-emerald-50/30">
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" required>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
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
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">دليل الحسابات</h3>
                    <p class="text-sm text-gray-500">استيراد وتصدير دليل الحسابات</p>
                </div>
            </div>
            <form action="{{ route('import.do') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="type" value="accounts">
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center hover:border-primary-400 transition bg-gray-50 hover:bg-primary-50/30">
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer" required>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
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

</x-app-layout>
