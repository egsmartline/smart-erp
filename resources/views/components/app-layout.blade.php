@props(['header' => null])

<!DOCTYPE html>
<html lang="ar" dir="rtl" x-data="{
    sidebarOpen: true,
    mobileMenu: false,
    printModalOpen: false,
    includeLogo: true,
    darkMode: localStorage.getItem('darkMode') === 'true',
    dbStatus: 'checking',
    init() {
        this.checkDb();
    },
    checkDb() {
        fetch('/health/db').then(r => r.json()).then(d => { this.dbStatus = d.status === 'connected' ? 'online' : 'offline'; }).catch(() => { this.dbStatus = 'offline'; });
        setTimeout(() => this.checkDb(), 30000);
    },
    toggleDark() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
    }
}" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Smart ERP') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { 'cairo': ['Cairo', 'sans-serif'] },
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a' },
                        emerald: { 50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b' }
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
        .submenu-item.active { background: rgba(255,255,255,0.1); border-right: 2px solid #10b981; }
        .content-area { min-height: calc(100vh - 64px); }
        .sidebar-transition { transition: all 0.3s ease; }
        [x-cloak] { display: none !important; }
        .dark body, .dark .bg-gray-100 { background-color: #111827 !important; }
        .dark .bg-white { background-color: #1f2937 !important; }
        .dark .text-gray-900 { color: #f3f4f6 !important; }
        .dark .text-gray-800 { color: #e5e7eb !important; }
        .dark .text-gray-700 { color: #d1d5db !important; }
        .dark .text-gray-600 { color: #9ca3af !important; }
        .dark .text-gray-500 { color: #6b7280 !important; }
        .dark .border-gray-200 { border-color: #374151 !important; }
        .dark .border-gray-300 { border-color: #4b5563 !important; }
        .dark .shadow-sm, .dark .shadow, .dark .shadow-md, .dark .shadow-lg { box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3) !important; }
        .dark th { background-color: #374151 !important; color: #e5e7eb !important; }
        .dark td { border-color: #4b5563 !important; }
        .dark .bg-emerald-50 { background-color: #064e3b !important; color: #a7f3d0 !important; }
        .dark .bg-blue-50 { background-color: #1e3a5f !important; color: #93c5fd !important; }
        .dark .bg-red-50 { background-color: #4c1d1d !important; color: #fca5a5 !important; }
        .dark .bg-yellow-50 { background-color: #4d3c00 !important; color: #fde68a !important; }
        .dark input, .dark select, .dark textarea { background-color: #374151 !important; color: #e5e7eb !important; border-color: #4b5563 !important; }
        .dark input:focus, .dark select:focus, .dark textarea:focus { border-color: #3b82f6 !important; }
        .dark table { background-color: #1f2937 !important; }
        .dark .bg-gray-50 { background-color: #374151 !important; }
        .dark .hover\:bg-gray-50:hover { background-color: #374151 !important; }
        .dark .divide-gray-200 > * { border-color: #374151 !important; }
        .dark .hover\:bg-primary-50:hover { background-color: #1e3a5f !important; }
        .dark .bg-primary-50 { background-color: #1e3a5f !important; color: #93c5fd !important; }
        .dark .text-gray-400 { color: #9ca3af !important; }
        .dark .bg-gray-200 { background-color: #4b5563 !important; }
        .dark .ring-gray-300 { border-color: #4b5563 !important; }
        .dark .border { border-color: #374151 !important; }

        {{-- Print Styles --}}
        .print-only { display: none; }
        .print-header, .print-header-minimal { display: none; }

        {{-- Print Header with Logo --}}
        .print-header-table { width: 100%; border-collapse: collapse; }
        .print-header-logo { width: 100px; vertical-align: middle; padding: 10px; }
        .print-logo-img { max-height: 70px; max-width: 90px; object-fit: contain; }
        .print-logo-placeholder { width: 70px; height: 70px; border-radius: 12px; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: bold; }
        .print-header-info { vertical-align: middle; padding: 10px; }
        .print-company-name { font-size: 20px; font-weight: 800; color: #1e3a8a; margin-bottom: 4px; }
        .print-company-details { display: flex; flex-wrap: wrap; gap: 8px 16px; font-size: 10px; color: #4b5563; }
        .print-header-date { width: 120px; vertical-align: middle; padding: 10px; text-align: left; }
        .print-date { font-size: 14px; font-weight: 700; color: #1e3a8a; }
        .print-time { font-size: 10px; color: #6b7280; }
        .print-minimal-title { font-size: 16px; font-weight: 700; color: #2563eb; text-align: center; padding: 8px; }
        .print-minimal-date { font-size: 10px; color: #9ca3af; text-align: center; }

        @media print {
            @page { size: A4; margin: 1.5cm 1cm; }

            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; font-size: 11px; color: #1f2937; }
            .mr-64, .mr-20 { margin-right: 0 !important; }

            {{-- Print header --}}
            body.print-with-logo .print-header { display: block !important; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 2px solid #2563eb; }
            body.print-without-logo .print-header-minimal { display: block !important; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 2px solid #2563eb; }
            body.print-with-logo .print-header-minimal { display: none !important; }
            body.print-without-logo .print-header { display: none !important; }

            header, .topbar, .top-bar, [class*="topbar"], [class*="top-bar"] { display: none !important; }

            {{-- Page content layout --}}
            .content-area { min-height: auto !important; padding: 0 !important; }

            {{-- Cards/Boxes --}}
            .rounded-xl, .rounded-2xl, .rounded-lg { border-radius: 4px !important; }
            .shadow-sm, .shadow, .shadow-md, .shadow-lg, .shadow-xl { box-shadow: none !important; }
            .border { border-color: #e5e7eb !important; }

            {{-- Tables --}}
            table { width: 100%; border-collapse: collapse; margin: 4px 0; font-size: 9px; }
            thead { display: table-header-group; }
            th { background: #D4ECF8 !important; color: #1e3a8a !important; padding: 3px 4px; text-align: right; font-weight: 600; font-size: 9px; border: 1px solid #D4ECF8; }
            td { padding: 2px 4px; text-align: right; border: 1px solid #e5e7eb; font-size: 9px; }
            tr { background: transparent !important; }
            tr:hover { background: inherit !important; }

            {{-- Summary rows --}}
            tr.total-row td, tfoot td { font-weight: 700; border-top: 2px solid #2563eb; }

            {{-- Typography --}}
            h1 { font-size: 18px; font-weight: 800; color: #1e3a8a; margin-bottom: 8px; }
            h2 { font-size: 16px; font-weight: 700; color: #1e3a8a; margin-bottom: 6px; }
            h3 { font-size: 14px; font-weight: 600; color: #1e40af; margin-bottom: 4px; }
            .text-gray-900 { color: #111827 !important; }
            .text-gray-800 { color: #1f2937 !important; }
            .text-gray-700 { color: #374151 !important; }
            .text-gray-600 { color: #4b5563 !important; }
            .text-gray-500 { color: #6b7280 !important; }
            .text-gray-400 { color: #9ca3af !important; }

            {{-- Buttons / Actions --}}
            a[href] { text-decoration: none; color: inherit; }

            {{-- Grid to full width --}}
            .grid { display: block !important; }
            .grid > * { display: block !important; width: 100% !important; margin-bottom: 8px; }

            {{-- Page breaks --}}
            .page-break { page-break-before: always; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tr { page-break-inside: avoid; }

            {{-- Footer --}}
            footer { display: none !important; }
            .print-footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 5px; }

            @page { @bottom-center { content: "الصفحة " counter(page) " من " counter(pages); font-size: 8px; color: #000000; } }
        }
    </style>
    @stack('styles')
</head>
<body class="font-cairo bg-gray-100 dark:bg-gray-900">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="fixed right-0 top-0 h-full bg-primary-800 text-white sidebar-transition z-40 flex flex-col no-print">

            {{-- Logo --}}
            <div class="flex-shrink-0 p-6 border-b border-primary-700 flex justify-center">
                @php $company = \App\Models\Company::where('tenant_id', session('current_tenant_id'))->first(); @endphp
                @if($company && $company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-20 w-20 rounded-xl object-contain">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-white">
                        <svg class="h-12 w-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto sidebar-scroll py-2 px-2">
                <ul class="space-y-1">

                    {{-- Dashboard --}}
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="menu-item flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">الرئيسية</span>
                        </a>
                    </li>

                    {{-- الخزينة --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('cash-treasuries.*'), request()->routeIs('bank-accounts.*'), request()->routeIs('payments.*'), request()->routeIs('transfers.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('cash-treasuries.*') || request()->routeIs('bank-accounts.*') || request()->routeIs('payments.*') || request()->routeIs('transfers.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">الخزينة</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('cash-treasuries.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('cash-treasuries.*') && !request()->routeIs('cash-treasuries.balances') && !request()->routeIs('payments.*') && !request()->routeIs('transfers.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">إدارة الخزينة</span></a></li>
                            <li><a href="{{ route('bank-accounts.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('bank-accounts.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الحسابات البنكية</span></a></li>
                            <li><a href="{{ route('payments.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('payments.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">سندات القبض والصرف</span></a></li>
                            <li><a href="{{ route('transfers.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('transfers.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">التحويلات الداخلية</span></a></li>
                            <li><a href="{{ route('cash-treasuries.balances') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('cash-treasuries.balances') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">أرصدة الخزائن والحسابات البنكية</span></a></li>
                        </ul>
                    </li>

                    {{-- المبيعات --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('sales-invoices.*'), request()->routeIs('sales-returns.*'), request()->routeIs('quotations.*'), request()->routeIs('customers.*'), request()->routeIs('sales-delivery-notes.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('sales-invoices.*') || request()->routeIs('sales-returns.*') || request()->routeIs('quotations.*') || request()->routeIs('customers.*') || request()->routeIs('sales-delivery-notes.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">المبيعات</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('sales-invoices.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('sales-invoices.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">فواتير البيع</span></a></li>
                            <li><a href="{{ route('sales-returns.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('sales-returns.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">مرتجعات المبيعات</span></a></li>
                            <li><a href="{{ route('quotations.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('quotations.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">عروض الأسعار</span></a></li>
                            <li><a href="{{ route('sales-delivery-notes.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('sales-delivery-notes.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">إذن تسليم</span></a></li>
                            <li><a href="{{ route('customers.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('customers.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">العملاء</span></a></li>
                        </ul>
                    </li>

                    {{-- المشتريات --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('purchase-orders.*'), request()->routeIs('purchase-invoices.*'), request()->routeIs('purchase-returns.*'), request()->routeIs('suppliers.*'), request()->routeIs('purchase-receipt-notes.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('purchase-orders.*') || request()->routeIs('purchase-invoices.*') || request()->routeIs('purchase-returns.*') || request()->routeIs('suppliers.*') || request()->routeIs('purchase-receipt-notes.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">المشتريات</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('purchase-orders.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('purchase-orders.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">أوامر الشراء</span></a></li>
                            <li><a href="{{ route('purchase-invoices.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('purchase-invoices.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">فواتير الشراء</span></a></li>
                            <li><a href="{{ route('purchase-returns.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('purchase-returns.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">مرتجعات المشتريات</span></a></li>
                            <li><a href="{{ route('purchase-receipt-notes.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('purchase-receipt-notes.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">إذن استلام</span></a></li>
                            <li><a href="{{ route('suppliers.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('suppliers.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الموردين</span></a></li>
                        </ul>
                    </li>

                    {{-- المخزون --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('items.*'), request()->routeIs('warehouses.*'), request()->routeIs('stock-movements.*'), request()->routeIs('inventory-count.*'), request()->routeIs('inventory-adjustments.*'), request()->routeIs('stock-transfers.*'), request()->routeIs('reordering-rules.*'), request()->routeIs('item-categories.*'), request()->routeIs('item-units.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('items.*') || request()->routeIs('warehouses.*') || request()->routeIs('stock-movements.*') || request()->routeIs('inventory-count.*') || request()->routeIs('inventory-adjustments.*') || request()->routeIs('stock-transfers.*') || request()->routeIs('reordering-rules.*') || request()->routeIs('item-categories.*') || request()->routeIs('item-units.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">المخزون</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('items.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('items.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الأصناف</span></a></li>
                            <li><a href="{{ route('item-categories.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('item-categories.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">التصنيفات</span></a></li>
                            <li><a href="{{ route('item-units.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('item-units.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الوحدات</span></a></li>
                            <li><a href="{{ route('warehouses.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('warehouses.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">المخازن</span></a></li>
                            <li><a href="{{ route('stock-movements.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('stock-movements.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">حركات المخزون</span></a></li>
                            <li><a href="{{ route('inventory-count.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('inventory-count.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">جرد المخزون</span></a></li>
                            <li><a href="{{ route('inventory-adjustments.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('inventory-adjustments.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">تسوية المخزون</span></a></li>
                            <li><a href="{{ route('stock-transfers.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('stock-transfers.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">التحويلات الداخلية</span></a></li>
                            <li><a href="{{ route('reordering-rules.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reordering-rules.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">قواعد إعادة الطلب</span></a></li>
                        </ul>
                    </li>

                    {{-- التقارير --}}
                    <li x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('reports.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">التقارير</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('reports.trial-balance') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.trial-balance') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">ميزان المراجعة</span></a></li>
                            <li><a href="{{ route('reports.general-ledger') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.general-ledger') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">دفتر الأستاذ العام</span></a></li>
                            <li><a href="{{ route('reports.income-statement') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.income-statement') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">قائمة الدخل</span></a></li>
                            <li><a href="{{ route('reports.balance-sheet') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.balance-sheet') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الميزانية العمومية</span></a></li>
                            <li><a href="{{ route('reports.cash-flow') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.cash-flow') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">التدفقات النقدية</span></a></li>
                            <li><a href="{{ route('reports.sales') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.sales') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">تقرير المبيعات</span></a></li>
                            <li><a href="{{ route('reports.purchases') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.purchases') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">تقرير المشتريات</span></a></li>
                            <li><a href="{{ route('reports.customer-statement') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.customer-statement') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">كشف حساب العملاء</span></a></li>
                            <li><a href="{{ route('reports.supplier-statement') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.supplier-statement') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">كشف حساب الموردين</span></a></li>
                            <li><a href="{{ route('reports.vat') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.vat') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">تقرير ضريبة القيمة المضافة</span></a></li>
                            <li><a href="{{ route('reports.inventory') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.inventory') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">تقرير المخزون</span></a></li>
                        </ul>
                    </li>

                    {{-- شئون العاملين --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('employees.*'), request()->routeIs('custodies.*'), request()->routeIs('job-positions.*'), request()->routeIs('attendance.*'), request()->routeIs('leaves.*'), request()->routeIs('payroll.*'), request()->routeIs('loans.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('employees.*') || request()->routeIs('custodies.*') || request()->routeIs('job-positions.*') || request()->routeIs('attendance.*') || request()->routeIs('leaves.*') || request()->routeIs('payroll.*') || request()->routeIs('loans.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">شئون العاملين</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('employees.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('employees.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الموظفون</span></a></li>
                            <li><a href="{{ route('custodies.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('custodies.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">العهد</span></a></li>
                            <li><a href="{{ route('job-positions.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('job-positions.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الوظائف</span></a></li>
                            <li><a href="{{ route('attendance.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('attendance.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الحضور والانصراف</span></a></li>
                            <li><a href="{{ route('leaves.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('leaves.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الإجازات</span></a></li>
                            <li><a href="{{ route('payroll.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('payroll.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الرواتب</span></a></li>
                            <li><a href="{{ route('loans.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('loans.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">السلف</span></a></li>
                        </ul>
                    </li>

                    {{-- الاستيراد والتصدير --}}
                    <li>
                        <a href="{{ route('trade.index') }}"
                            class="menu-item flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('trade.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">الاستيراد والتصدير</span>
                        </a>
                    </li>

                    {{-- المحاسبة --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('accounts.*'), request()->routeIs('journals.*'), request()->routeIs('journal-entries.*'), request()->routeIs('payments.*'), request()->routeIs('payment-terms.*'), request()->routeIs('taxes.*'), request()->routeIs('bank-statements.*'), request()->routeIs('analytical-accounts.*'), request()->routeIs('budgets.*'), request()->routeIs('reports.account-statement')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('accounts.*') || request()->routeIs('journals.*') || request()->routeIs('journal-entries.*') || request()->routeIs('payment-terms.*') || request()->routeIs('taxes.*') || request()->routeIs('bank-statements.*') || request()->routeIs('analytical-accounts.*') || request()->routeIs('budgets.*') || request()->routeIs('reports.account-statement') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">المحاسبة</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('accounts.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('accounts.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">دليل الحسابات</span></a></li>
                            <li><a href="{{ route('journals.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('journals.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">يوميات القيود</span></a></li>
                            <li><a href="{{ route('journal-entries.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('journal-entries.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">القيود اليومية</span></a></li>

                            <li><a href="{{ route('payment-terms.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('payment-terms.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">شروط الدفع</span></a></li>
                            <li><a href="{{ route('taxes.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('taxes.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الضرائب</span></a></li>
                            <li><a href="{{ route('bank-statements.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('bank-statements.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">كشف حساب البنك</span></a></li>
                            <li><a href="{{ route('analytical-accounts.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('analytical-accounts.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الحسابات التحليلية</span></a></li>
                            <li><a href="{{ route('budgets.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('budgets.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الميزانيات</span></a></li>
                            <li><a href="{{ route('reports.account-statement') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('reports.account-statement') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">بيان حركة حساب</span></a></li>
                        </ul>
                    </li>

                    {{-- الإعدادات --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('settings.*'), request()->routeIs('currencies.*'), request()->routeIs('fiscal-years.*'), request()->routeIs('backups.*'), request()->routeIs('audit-log.*'), request()->routeIs('import.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('settings.*') || request()->routeIs('currencies.*') || request()->routeIs('fiscal-years.*') || request()->routeIs('backups.*') || request()->routeIs('audit-log.*') || request()->routeIs('import.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">الإعدادات</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('settings.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.*') && !request()->routeIs('settings.users.*') && !request()->routeIs('settings.roles.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">إعدادات الشركة</span></a></li>
                            <li><a href="{{ route('settings.users.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.users.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">المستخدمون</span></a></li>
                            <li><a href="{{ route('settings.roles.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.roles.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الأدوار والصلاحيات</span></a></li>
                            <li><a href="{{ route('currencies.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('currencies.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">العملة</span></a></li>
                            <li><a href="{{ route('fiscal-years.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('fiscal-years.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">السنة المالية</span></a></li>
                            <li><a href="{{ route('backups.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('backups.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">النسخ الاحتياطي</span></a></li>
                            <li><a href="{{ route('audit-log.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('audit-log.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">سجل التدقيق</span></a></li>
                            <li><a href="{{ route('import.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('import.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">استيراد وتصدير البيانات</span></a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            {{-- Back to Companies --}}
            <div class="flex-shrink-0 px-3 pb-1">
                <a href="{{ route('select-company') }}"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 transition-all text-white text-sm font-bold">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span x-show="sidebarOpen">العودة إلى تبويب الشركات</span>
                </a>
            </div>

            {{-- Logout --}}
            <div class="flex-shrink-0 px-3 pb-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 transition-all text-white text-sm font-bold cursor-pointer">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span x-show="sidebarOpen">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
            {{-- Sidebar Toggle --}}
            <div class="flex-shrink-0 p-3 border-t border-primary-700">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-xl hover:bg-primary-700 transition-all text-primary-300 hover:text-white">
                    <svg class="h-5 w-5 transition-transform" :class="sidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    <span x-show="sidebarOpen" class="text-sm">طي القائمة</span>
                </button>
            </div>
        </aside>

        {{-- Main Content --}}
        <div :class="sidebarOpen ? 'mr-64' : 'mr-20'" class="flex-1 sidebar-transition min-h-screen">
            {{-- Print headers at top of page --}}
            <div class="print-header print-only">
                @php $printCompany = \App\Models\Company::where('tenant_id', session('current_tenant_id'))->first(); @endphp
                @if($printCompany)
                <table style="width:100%; border-collapse:collapse;" dir="rtl">
                    <tr>
                        <td style="width:90px; vertical-align:middle; padding:5px;">
                            @if($printCompany->logo)
                                <img src="{{ asset('storage/' . $printCompany->logo) }}" alt="Logo" style="max-height:65px; max-width:80px; object-fit:contain; display:block;">
                            @else
                                <div style="width:60px; height:60px; border-radius:10px; background:#2563eb; color:white; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:bold;">{{ substr($printCompany->name, 0, 1) }}</div>
                            @endif
                        </td>
                        <td style="vertical-align:middle; padding:5px;">
                            <div style="font-size:16px; font-weight:800; color:#1e3a8a; margin-bottom:3px;">{{ $printCompany->name }}</div>
                            <div style="display:flex; flex-wrap:wrap; gap:4px 12px; font-size:9px; color:#4b5563;">
                                @if($printCompany->address)<span style="white-space:nowrap;">{{ $printCompany->address }}</span>@endif
                                @if($printCompany->phone)<span style="white-space:nowrap;">{{ $printCompany->phone }}</span>@endif
                                @if($printCompany->tax_number)<span style="white-space:nowrap;">رقم ضريبي: {{ $printCompany->tax_number }}</span>@endif
                                @if($printCompany->email)<span style="white-space:nowrap;">{{ $printCompany->email }}</span>@endif
                            </div>
                        </td>
                        <td style="width:100px; vertical-align:middle; padding:5px; text-align:left;">
                            <div style="font-size:12px; font-weight:700; color:#1e3a8a;">{{ now()->format('Y/m/d') }}</div>
                            <div style="font-size:9px; color:#6b7280;">{{ now()->format('h:i A') }}</div>
                        </td>
                    </tr>
                </table>
                @endif
            </div>

            <div class="print-header-minimal print-only" style="text-align:center; padding:10px;">
                <div style="font-size:14px; font-weight:700; color:#2563eb;">{{ config('app.name', 'Smart ERP') }}</div>
                <div style="font-size:9px; color:#9ca3af;">{{ now()->format('Y/m/d h:i A') }}</div>
            </div>

            {{-- Top Bar --}}
            <header class="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-4">
                        <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        @if($header)
                            {{ $header }}
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Print Button --}}
                        <button @click="printModalOpen = true" class="no-print inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-300 transition shadow-sm" title="طباعة الصفحة">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span class="hidden sm:inline">طباعة</span>
                        </button>

                        {{-- Currency Switcher --}}
                        <div class="flex items-center gap-2" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm hover:bg-gray-50 transition">
                                <span class="font-bold text-primary-600">{{ session('display_currency', 'EGP') }}</span>
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute top-full left-0 mt-1 w-36 rounded-lg border border-gray-200 bg-white shadow-lg z-50">
                                <a href="{{ route('currency.switch', ['currency' => 'EGP']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 {{ session('display_currency', 'EGP') === 'EGP' ? 'bg-primary-50 text-primary-600 font-bold' : '' }}">ج.م - جنيه مصري</a>
                                <a href="{{ route('currency.switch', ['currency' => 'USD']) }}" class="block px-4 py-2 text-sm hover:bg-gray-100 {{ session('display_currency') === 'USD' ? 'bg-primary-50 text-primary-600 font-bold' : '' }}">$ - دولار أمريكي</a>
                            </div>
                        </div>
                        {{-- Dark Mode Toggle --}}
                        <button @click="toggleDark" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-sm text-gray-600 hover:bg-primary-50 hover:text-primary-600 transition shadow-sm" title="الوضع الليلي">
                            <svg x-show="!darkMode" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <svg x-show="darkMode" class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>
                        {{-- DB Status --}}
                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2 py-1.5 shadow-sm" title="حالة الاتصال بقاعدة البيانات">
                            <span class="h-2 w-2 rounded-full" :class="dbStatus === 'online' ? 'bg-green-500' : dbStatus === 'offline' ? 'bg-red-500' : 'bg-yellow-500 animate-pulse'"></span>
                            <span class="text-xs text-gray-500" x-text="dbStatus === 'online' ? 'متصل' : dbStatus === 'offline' ? 'منقطع' : '...'"></span>
                        </div>
                        <span class="text-sm text-gray-500">{{ now()->format('Y/m/d') }}</span>
                        {{-- User Menu --}}
                        <div class="flex items-center gap-3" x-data="{ userMenu: false }">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700">
                                {{ substr(Auth::user()->name ?? 'م', 0, 1) }}
                            </div>
                            <div class="relative">
                                <button @click="userMenu = !userMenu" class="flex items-center gap-1 text-sm text-gray-700 hover:text-gray-900">
                                    <span class="font-medium">{{ Auth::user()->name ?? 'مستخدم' }}</span>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="userMenu" @click.away="userMenu = false" x-transition class="absolute left-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-lg z-50">
                                    <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">الإعدادات</a>
                                    <hr class="my-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50">تسجيل الخروج</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if (session('error'))
            <div class="mx-6 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-700 hover:text-red-900"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            </div>
            @endif

            @if (session('success'))
            <div class="mx-6 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-700 hover:text-emerald-900"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
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

            {{-- Page Content --}}
            <main class="p-6 content-area">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between text-sm text-gray-500">

                    <p>الإصدار 1.0.0</p>
                </div>
            </footer>
        </div>
        {{-- Print Modal --}}
        <x-print-modal />
    </div>

    @stack('scripts')
</body>
</html>
