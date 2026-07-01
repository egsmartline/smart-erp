<!-- Sidebar -->
<aside class="fixed inset-y-0 right-0 z-50 w-64 bg-gray-900 text-white shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 {{ $open ?? false ? 'translate-x-0' : 'translate-x-full lg:translate-x-0' }}" dir="rtl">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 border-b border-gray-800">
        @php $company = \App\Models\Company::where('tenant_id', session('current_tenant_id'))->first(); @endphp
        @if($company && $company->logo)
            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-10 w-10 rounded-lg object-cover">
        @else
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-lg font-bold text-white">S</div>
        @endif
    </div>

    <!-- Navigation -->
    <nav class="mt-4 px-3 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 180px)">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            الرئيسية
        </a>

        <!-- الحسابات -->
        <div x-data="{ open: {{ request()->routeIs('accounts.*') || request()->routeIs('journal-entries.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    المحاسبة
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('accounts.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('accounts.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    دليل الحسابات
                </a>
                <a href="{{ route('journal-entries.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('journal-entries.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    القيود اليومية
                </a>
            </div>
        </div>

        <!-- المبيعات -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    المبيعات
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    عروض الأسعار
                </a>
                <a href="{{ route('sales-invoices.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('sales-invoices.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    فواتير البيع
                </a>
                <a href="{{ route('sales-returns.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('sales-returns.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    مرتجعات المبيعات
                </a>
                <a href="{{ route('quotations.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('quotations.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    عروض الأسعار
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('customers.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    العملاء
                </a>
            </div>
        </div>

        <!-- المشتريات -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    المشتريات
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="{{ route('purchase-invoices.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('purchase-invoices.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    فواتير الشراء
                </a>
                <a href="{{ route('purchase-returns.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('purchase-returns.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    مرتجعات المشتريات
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('suppliers.*') ? 'bg-blue-600/20 text-blue-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الموردين
                </a>
            </div>
        </div>

        <!-- المخزون -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    المخزون
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الأصناف
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    المخازن
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    حركات المخزون
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    جرد المخزون
                </a>
            </div>
        </div>

        <!-- التقارير -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    التقارير
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    ميزان المراجعة
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    قائمة الدخل
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الميزانية العمومية
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    تقرير الأرباح والخسائر
                </a>
            </div>
        </div>

        <!-- الإعدادات -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition text-gray-300 hover:bg-gray-800 hover:text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    الإعدادات
                </div>
                <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse class="mr-8 mt-1 space-y-1">
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    إعدادات الشركة
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    المستخدمون
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    الأدوار والصلاحيات
                </a>
                <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    العملة
                </a>
            </div>
        </div>
    </nav>

    <!-- User Info -->
    <div class="absolute bottom-0 left-0 right-0 border-t border-gray-800 bg-gray-900 p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold">
                {{ substr(Auth::user()->name ?? 'م', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'مستخدم' }}</div>
                <div class="text-xs text-gray-400 truncate">{{ Auth::user()->email ?? '' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm font-bold text-white transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                تسجيل الخروج
            </button>
        </form>
    </div>
</aside>
