<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart ERP - {{ $title ?? 'لوحة التحكم' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'cairo': ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #1e3a8a; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 2px; }
        .menu-item.active { background: rgba(255,255,255,0.15); border-right: 3px solid #10b981; }
        .submenu-item.active { background: rgba(255,255,255,0.1); }
        .content-area { min-height: calc(100vh - 64px); }
        .sidebar-transition { transition: width 0.3s ease, transform 0.3s ease; }
    </style>
    @stack('styles')
</head>
<body class="font-cairo bg-gray-100" x-data="{ sidebarOpen: true, mobileMenu: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="fixed right-0 top-0 h-full bg-primary-800 text-white sidebar-transition z-40 flex flex-col">

            <!-- Logo -->
            <div class="p-4 border-b border-primary-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" class="font-bold text-lg">Smart ERP</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll py-4">
                <ul class="space-y-1 px-3">

                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="menu-item flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">لوحة التحكم</span>
                        </a>
                    </li>

                    <!-- Chart of Accounts -->
                    <li>
                        <a href="{{ route('accounts.index') }}"
                            class="menu-item flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('accounts.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">شجرة الحسابات</span>
                        </a>
                    </li>

                    <!-- Journal Entries -->
                    <li>
                        <a href="{{ route('journal-entries.index') }}"
                            class="menu-item flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('journal-entries.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">القيود اليومية</span>
                        </a>
                    </li>

                    <!-- Sales Submenu -->
                    <li x-data="{ open: {{ request()->routeIs('sales-invoices.*') || request()->routeIs('quotations.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('sales-invoices.*') || request()->routeIs('quotations.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap">المبيعات</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-4 space-y-1">
                            <li>
                                <a href="{{ route('sales-invoices.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('sales-invoices.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    فواتير المبيعات
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('quotations.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('quotations.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    عروض الأسعار
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Purchases Submenu -->
                    <li x-data="{ open: {{ request()->routeIs('purchase-invoices.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('purchase-invoices.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap">المشتريات</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-4 space-y-1">
                            <li>
                                <a href="{{ route('purchase-invoices.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('purchase-invoices.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    فواتير المشتريات
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Inventory Submenu -->
                    <li x-data="{ open: {{ request()->routeIs('items.*') || request()->routeIs('item-categories.*') || request()->routeIs('item-units.*') || request()->routeIs('warehouses.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('items.*') || request()->routeIs('item-categories.*') || request()->routeIs('item-units.*') || request()->routeIs('warehouses.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap">المخزون</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-4 space-y-1">
                            <li>
                                <a href="{{ route('items.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('items.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    الأصناف
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('item-categories.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('item-categories.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    التصنيفات
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('item-units.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('item-units.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    الوحدات
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('warehouses.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('warehouses.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    المخازن
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Customers -->
                    <li>
                        <a href="{{ route('customers.index') }}"
                            class="menu-item flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('customers.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">العملاء</span>
                        </a>
                    </li>

                    <!-- Suppliers -->
                    <li>
                        <a href="{{ route('suppliers.index') }}"
                            class="menu-item flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('suppliers.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">الموردين</span>
                        </a>
                    </li>

                    <!-- Treasury & Banks -->
                    <li x-data="{ open: {{ request()->routeIs('cash-treasuries.*') || request()->routeIs('bank-accounts.*') || request()->routeIs('payments.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('cash-treasuries.*') || request()->routeIs('bank-accounts.*') || request()->routeIs('payments.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap">الخزينة والبنوك</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-4 space-y-1">
                            <li>
                                <a href="{{ route('cash-treasuries.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('cash-treasuries.*') && !request()->routeIs('payments.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    الخزينة النقدية
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bank-accounts.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('bank-accounts.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    الحسابات البنكية
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('payments.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('payments.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    سندات القبض والصرف
                                </a>
                            </li>
                        </ul>
                    </li>



                    <!-- Reports Submenu -->
                    <li x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('reports.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap">التقارير</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-4 space-y-1">
                            <li>
                                <a href="{{ route('reports.trial-balance') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.trial-balance') ? 'active' : 'hover:bg-primary-700' }}">
                                    ميزان المراجعة
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.general-ledger') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.general-ledger') ? 'active' : 'hover:bg-primary-700' }}">
                                    دفتر الأستاذ
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.income-statement') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.income-statement') ? 'active' : 'hover:bg-primary-700' }}">
                                    قائمة الدخل
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.balance-sheet') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.balance-sheet') ? 'active' : 'hover:bg-primary-700' }}">
                                    الميزانية العمومية
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.customer-statement') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.customer-statement') ? 'active' : 'hover:bg-primary-700' }}">
                                    كشف حساب العملاء
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.supplier-statement') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.supplier-statement') ? 'active' : 'hover:bg-primary-700' }}">
                                    كشف حساب الموردين
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.sales') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.sales') ? 'active' : 'hover:bg-primary-700' }}">
                                    تقارير المبيعات
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.purchases') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.purchases') ? 'active' : 'hover:bg-primary-700' }}">
                                    تقارير المشتريات
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.inventory') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.inventory') ? 'active' : 'hover:bg-primary-700' }}">
                                    تقارير المخزون
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.vat') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.vat') ? 'active' : 'hover:bg-primary-700' }}">
                                    تقرير VAT
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.cash-flow') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.cash-flow') ? 'active' : 'hover:bg-primary-700' }}">
                                    التدفقات النقدية
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Settings Submenu -->
                    <li x-data="{ open: {{ request()->routeIs('settings.*') || request()->routeIs('currencies.*') || request()->routeIs('fiscal-years.*') || request()->routeIs('backups.*') || request()->routeIs('audit-log.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('settings.*') || request()->routeIs('currencies.*') || request()->routeIs('fiscal-years.*') || request()->routeIs('backups.*') || request()->routeIs('audit-log.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap">الإعدادات</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-4 space-y-1">
                            <li>
                                <a href="{{ route('settings.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    الإعدادات العامة
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('currencies.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('currencies.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    العملات
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('fiscal-years.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('fiscal-years.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    السنوات المالية
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('backups.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('backups.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    النسخ الاحتياطي
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('audit-log.index') }}"
                                    class="submenu-item block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('audit-log.*') ? 'active' : 'hover:bg-primary-700' }}">
                                    سجل التدقيق
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <!-- Sidebar Toggle -->
            <div class="p-3 border-t border-primary-700">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-xl hover:bg-primary-700 transition-all">
                    <svg class="w-5 h-5 transition-transform" :class="sidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm">طي القائمة</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="sidebarOpen ? 'mr-64' : 'mr-20'" class="flex-1 sidebar-transition">

            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between px-6 py-3">
                    <!-- Mobile Menu -->
                    <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Page Title -->
                    <h1 class="text-lg font-bold text-gray-800">{{ $title ?? 'لوحة التحكم' }}</h1>

                    <!-- User Menu -->
                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <button class="relative p-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute top-1 left-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- User Info -->
                        <div class="flex items-center gap-3" x-data="{ open: false }">
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->role === 'admin' ? 'مدير النظام' : 'مستخدم' }}</p>
                            </div>
                            <div class="relative">
                                <button @click="open = !open" class="flex items-center">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="text-primary-700 font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                    <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        الإعدادات
                                    </a>
                                    <hr class="my-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            تسجيل الخروج
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if (session('success'))
            <div class="mx-6 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-700 hover:text-emerald-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            @endif

            @if ($errors->any())
            <div class="mx-6 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Page Content -->
            <main class="p-6 content-area">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <p>Developer by BASSAM DAWOOD {{ date('Y') }}</p>
                    <p>الإصدار 1.0.0</p>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
