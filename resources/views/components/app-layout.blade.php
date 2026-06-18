@props(['header' => null])

<!DOCTYPE html>
<html lang="ar" dir="rtl">
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
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-only { display: block !important; }
            .mr-64, .mr-20 { margin-right: 0 !important; }
        }
        .print-only { display: none; }
    </style>
    @stack('styles')
</head>
<body class="font-cairo bg-gray-100" x-data="{ sidebarOpen: true, mobileMenu: false }">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="fixed right-0 top-0 h-full bg-primary-800 text-white sidebar-transition z-40 flex flex-col no-print">

            {{-- Logo --}}
            <div class="flex-shrink-0 p-4 border-b border-primary-700">
                @php $company = \App\Models\Company::where('tenant_id', session('current_tenant_id'))->first(); @endphp
                <div class="flex items-center gap-3">
                    @if($company && $company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-10 w-10 rounded-xl object-cover flex-shrink-0">
                    @else
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white flex-shrink-0">
                            <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <div x-show="sidebarOpen" x-transition>
                        <div class="text-lg font-bold text-white">{{ $company->name ?? 'Smart ERP' }}</div>
                        <div class="text-xs text-primary-300">نظام المحاسبة الذكي</div>
                    </div>
                </div>
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

                    {{-- المحاسبة --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('accounts.*'), request()->routeIs('journals.*'), request()->routeIs('journal-entries.*'), request()->routeIs('payments.*'), request()->routeIs('payment-terms.*'), request()->routeIs('taxes.*'), request()->routeIs('bank-statements.*'), request()->routeIs('analytical-accounts.*'), request()->routeIs('budgets.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('accounts.*') || request()->routeIs('journals.*') || request()->routeIs('journal-entries.*') || request()->routeIs('payments.*') || request()->routeIs('payment-terms.*') || request()->routeIs('taxes.*') || request()->routeIs('bank-statements.*') || request()->routeIs('analytical-accounts.*') || request()->routeIs('budgets.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
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
                            <li><a href="{{ route('payments.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('payments.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">المدفوعات</span></a></li>
                            <li><a href="{{ route('payment-terms.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('payment-terms.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">شروط الدفع</span></a></li>
                            <li><a href="{{ route('taxes.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('taxes.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الضرائب</span></a></li>
                            <li><a href="{{ route('bank-statements.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('bank-statements.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">كشف حساب البنك</span></a></li>
                            <li><a href="{{ route('analytical-accounts.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('analytical-accounts.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الحسابات التحليلية</span></a></li>
                            <li><a href="{{ route('budgets.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('budgets.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الميزانيات</span></a></li>
                        </ul>
                    </li>

                    {{-- المبيعات --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('sales-orders.*'), request()->routeIs('sales-invoices.*'), request()->routeIs('sales-returns.*'), request()->routeIs('quotations.*'), request()->routeIs('customers.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('sales-orders.*') || request()->routeIs('sales-invoices.*') || request()->routeIs('sales-returns.*') || request()->routeIs('quotations.*') || request()->routeIs('customers.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">المبيعات</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('sales-orders.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('sales-orders.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">أوامر البيع</span></a></li>
                            <li><a href="{{ route('sales-invoices.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('sales-invoices.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">فواتير البيع</span></a></li>
                            <li><a href="{{ route('sales-returns.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('sales-returns.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">مرتجعات المبيعات</span></a></li>
                            <li><a href="{{ route('quotations.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('quotations.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">عروض الأسعار</span></a></li>
                            <li><a href="{{ route('customers.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('customers.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">العملاء</span></a></li>
                        </ul>
                    </li>

                    {{-- المشتريات --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('purchase-orders.*'), request()->routeIs('purchase-invoices.*'), request()->routeIs('purchase-returns.*'), request()->routeIs('suppliers.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('purchase-orders.*') || request()->routeIs('purchase-invoices.*') || request()->routeIs('purchase-returns.*') || request()->routeIs('suppliers.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
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
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('employees.*'), request()->routeIs('departments.*'), request()->routeIs('job-positions.*'), request()->routeIs('attendance.*'), request()->routeIs('leaves.*'), request()->routeIs('expenses.*'), request()->routeIs('payroll.*'), request()->routeIs('loans.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('employees.*') || request()->routeIs('departments.*') || request()->routeIs('job-positions.*') || request()->routeIs('attendance.*') || request()->routeIs('leaves.*') || request()->routeIs('expenses.*') || request()->routeIs('payroll.*') || request()->routeIs('loans.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">شئون العاملين</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('employees.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('employees.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الموظفون</span></a></li>
                            <li><a href="{{ route('departments.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('departments.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الأقسام</span></a></li>
                            <li><a href="{{ route('job-positions.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('job-positions.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الوظائف</span></a></li>
                            <li><a href="{{ route('attendance.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('attendance.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الحضور والانصراف</span></a></li>
                            <li><a href="{{ route('leaves.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('leaves.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الإجازات</span></a></li>
                            <li><a href="{{ route('expenses.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('expenses.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">المصروفات</span></a></li>
                            <li><a href="{{ route('payroll.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('payroll.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">الرواتب</span></a></li>
                            <li><a href="{{ route('loans.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('loans.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">السلف</span></a></li>
                        </ul>
                    </li>

                    {{-- الشركات --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('companies.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('companies.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">الشركات</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('companies.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('companies.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">إدارة الشركات</span></a></li>
                        </ul>
                    </li>

                    {{-- استيراد وتصدير --}}
                    <li>
                        <a href="{{ route('import.index') }}"
                            class="menu-item flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('import.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">استيراد وتصدير</span>
                        </a>
                    </li>

                    {{-- الإعدادات --}}
                    <li x-data="{ open: {{ in_array(true, [request()->routeIs('settings.*'), request()->routeIs('currencies.*'), request()->routeIs('fiscal-years.*'), request()->routeIs('backups.*'), request()->routeIs('audit-log.*')]) ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="menu-item w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('settings.*') || request()->routeIs('currencies.*') || request()->routeIs('fiscal-years.*') || request()->routeIs('backups.*') || request()->routeIs('audit-log.*') ? 'active' : 'hover:bg-primary-700' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium">الإعدادات</span>
                            </div>
                            <svg x-show="sidebarOpen" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                            <li><a href="{{ route('settings.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('settings.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">إعدادات الشركة</span></a></li>
                            <li><a href="{{ route('currencies.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('currencies.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">العملة</span></a></li>
                            <li><a href="{{ route('fiscal-years.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('fiscal-years.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">السنة المالية</span></a></li>
                            <li><a href="{{ route('backups.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('backups.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">النسخ الاحتياطي</span></a></li>
                            <li><a href="{{ route('audit-log.index') }}" class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm {{ request()->routeIs('audit-log.*') ? 'active' : 'hover:bg-primary-700' }}"><span class="h-1.5 w-1.5 rounded-full bg-current flex-shrink-0"></span><span x-show="sidebarOpen">سجل التدقيق</span></a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

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
                    <div class="flex items-center gap-4">
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
                    <p>Smart ERP &copy; {{ date('Y') }} - جميع الحقوق محفوظة</p>
                    <p>الإصدار 1.0.0</p>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
