<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">لوحة التقارير</h2>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">إجمالي الأصناف</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalItems) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">العملاء</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalCustomers) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">الموردون</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalSuppliers) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">مبيعات الشهر</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalSales, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('reports.dashboard') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 group-hover:bg-blue-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">لوحة التحكم</h3>
                    <p class="text-sm text-gray-500">نظرة عامة على النظام</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.trial-balance') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 group-hover:bg-emerald-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">ميزان المراجعة</h3>
                    <p class="text-sm text-gray-500">أرصدة الحسابات</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.general-ledger') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 text-amber-600 group-hover:bg-amber-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">الأستاذ العام</h3>
                    <p class="text-sm text-gray-500">تفاصيل الحسابات</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.income-statement') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 group-hover:bg-purple-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">قائمة الدخل</h3>
                    <p class="text-sm text-gray-500">الإيرادات والمصروفات</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.balance-sheet') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100 text-red-600 group-hover:bg-red-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l3 9a5.002 5.002 0 01-6.001 0M18 7l-3 9m-6-2h6"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">الميزانية العمومية</h3>
                    <p class="text-sm text-gray-500">الأصول والخصوم</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.customer-statement') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 group-hover:bg-indigo-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">كشف حساب العميل</h3>
                    <p class="text-sm text-gray-500">المديونيات والتحصيل</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.supplier-statement') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-pink-100 text-pink-600 group-hover:bg-pink-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">كشف حساب المورد</h3>
                    <p class="text-sm text-gray-500">المدفوعات والذمم</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.sales') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-100 text-teal-600 group-hover:bg-teal-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">تقرير المبيعات</h3>
                    <p class="text-sm text-gray-500">فواتير المبيعات</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.purchases') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 text-orange-600 group-hover:bg-orange-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">تقرير المشتريات</h3>
                    <p class="text-sm text-gray-500">فواتير المشتريات</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.vat') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 group-hover:bg-cyan-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">تقرير الضريبة</h3>
                    <p class="text-sm text-gray-500">ضريبة القيمة المضافة</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.inventory') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-lime-100 text-lime-600 group-hover:bg-lime-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">تقرير المخزون</h3>
                    <p class="text-sm text-gray-500">أرصدة المخزون</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.cash-flow') }}" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 group-hover:bg-yellow-200 transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">التدفقات النقدية</h3>
                    <p class="text-sm text-gray-500">المقبوضات والمدفوعات</p>
                </div>
            </div>
        </a>
    </div>
</x-app-layout>
