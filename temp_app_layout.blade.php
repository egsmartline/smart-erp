<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SMART LINE ERP') ظ¤ {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans bg-surface text-gray-900 antialiased">
    
<div class="min-h-screen">
        @auth
            {{-- Top Navigation --}}
            <nav class="no-print bg-white/80 backdrop-blur-lg border-b border-gray-200/50 fixed top-0 left-0 right-0 z-50" dir="ltr">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center gap-3" dir="rtl">
                            <button onclick="toggleSidebar()" class="p-2 rounded-apple-sm hover:bg-gray-100 transition-colors">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                                @php $logo = Auth::user()->company?->logo; @endphp
                                @if($logo)
                                    <img src="{{ asset('storage/app/public/' . $logo) }}" alt="╪┤╪╣╪د╪▒" class="h-8 w-auto max-w-[32px] object-contain">
                                @else
                                    <span class="w-8 h-8 bg-violet-600 rounded-apple-sm flex items-center justify-center flex-none">
                                        <span class="text-white font-bold text-sm">SL</span>
                                    </span>
                                @endif
                                <span class="text-lg font-semibold text-blue-700 hidden sm:block">SMART LINE</span>
                                <span class="text-[10px] text-gray-400 hidden sm:block -mt-1">Developed by Bassam Dawood 2026</span>
                            </a>
                        </div>
                        <div class="flex items-center gap-4" dir="rtl">
                            {{-- DB Connection Indicator --}}
                            <div class="flex items-center gap-1.5 text-xs" title="╪ص╪د┘╪ر ╪د┘╪د╪ز╪╡╪د┘ ╪ذ┘é╪د╪╣╪»╪ر ╪د┘╪ذ┘è╪د┘╪د╪ز">
                                <span id="dbIndicator" class="w-2 h-2 rounded-full inline-block bg-amber-400"></span>
                                <span id="dbText" class="text-gray-400 hidden sm:inline">╪ش╪د╪▒┘è ╪د┘┘╪ص╪╡</span>
                            </div>

                            {{-- Dark Mode Toggle --}}
                            <div onclick="toggleDarkMode()"
                                 class="p-2 rounded-apple-sm hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                                 title="╪د┘┘ê╪╢╪╣ ╪د┘┘┘è┘┘è">
                                <svg id="moonIcon" class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <svg id="sunIcon" class="w-5 h-5 text-amber-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>

                            <a href="#" class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors" title="╪د┘╪ز┘╪ذ┘è┘ç╪د╪ز">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @php $alertCount = \App\Models\Alert::where('company_id', auth()->user()->company_id)->where('is_read', false)->count(); @endphp
                                @if($alertCount > 0)
                                    <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-danger text-white text-xs rounded-full flex items-center justify-center font-medium">{{ $alertCount > 9 ? '9+' : $alertCount }}</span>
                                @endif
                            </a>
                            <div class="flex items-center gap-3 pr-3 border-r border-gray-200">
                                <div class="text-left">
                                    <p class="text-xs text-gray-400">{{ auth()->user()->company?->name }}</p>
                                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400">{{ auth()->user()->roles->first()?->display_name ?? '┘à╪│╪ز╪«╪»┘à' }}</p>
                                </div>
                                <div class="w-9 h-9 bg-violet-100 rounded-full flex items-center justify-center">
                                    <span class="text-violet-600 font-medium text-sm">{{ substr(auth()->user()->name, 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {{-- Sidebar + Main flex container --}}
            <div class="flex pt-16 min-h-screen">
                {{-- Mobile overlay backdrop --}}
                <div id="sidebarOverlay"
                     onclick="closeSidebar()"
                     class="no-print fixed inset-0 bg-black/30 z-30 lg:hidden hidden">
                </div>

                {{-- Sidebar --}}
                <aside id="sidebar" class="flex-none bg-violet-950 border-l border-violet-900/30 transition-[width,transform] duration-75 overflow-x-hidden h-[calc(100vh-4rem)] z-40
                              max-lg:fixed max-lg:top-16 max-lg:bottom-0 max-lg:right-0 max-lg:w-64 max-lg:shadow-apple-lg max-lg:translate-x-full lg:w-64 no-print">
                    <div class="w-64 overflow-y-auto h-full">
                        <nav class="p-4 space-y-1" x-init="$el.querySelectorAll('a').forEach(a => a.addEventListener('click', () => { if (window.innerWidth < 1024) closeSidebar(); }))">
                            <x-nav-item href="{{ route('dashboard') }}" icon="chart-pie" label="┘┘ê╪ص╪ر ╪د┘╪ز╪ص┘â┘à" active="{{ request()->routeIs('dashboard') }}"/>

                            <div x-data="{ open: {{ request()->routeIs('general-ledger.*') ? 'true' : 'false' }} }" class="space-y-1">
                                <button @click="open = !open" class="w-full flex items-center justify-between pr-3 py-2.5 text-xs font-semibold text-violet-300 uppercase tracking-wider hover:text-white transition-colors">
                                    <span>╪د┘╪ص╪│╪د╪ذ╪د╪ز ╪د┘╪╣╪د┘à╪ر</span>
                                    <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                                    <x-nav-item href="{{ route('general-ledger.accounts.index') }}" icon="book-open" label="╪»┘┘è┘ ╪د┘╪ص╪│╪د╪ذ╪د╪ز" active="{{ request()->routeIs('general-ledger.accounts.*') }}"/>
                                    <x-nav-item href="{{ route('general-ledger.journal-entries.index') }}" icon="edit" label="┘é┘è┘ê╪» ╪د┘┘è┘ê┘à┘è╪ر" active="{{ request()->routeIs('general-ledger.journal-entries.*') }}"/>
                                    <x-nav-item href="{{ route('general-ledger.ledger.index') }}" icon="table" label="╪»┘╪ز╪▒ ╪د┘╪ث╪│╪ز╪د╪░" active="{{ request()->routeIs('general-ledger.ledger.*') }}"/>
                                    <x-nav-item href="{{ route('general-ledger.trial-balance') }}" icon="scale" label="┘à┘è╪▓╪د┘ ╪د┘┘à╪▒╪د╪ش╪╣╪ر" active="{{ request()->routeIs('general-ledger.trial-balance') }}"/>
                                    <x-nav-item href="{{ route('general-ledger.journals.index') }}" icon="collection" label="╪»┘╪د╪ز╪▒ ╪د┘┘è┘ê┘à┘è╪ر" active="{{ request()->routeIs('general-ledger.journals.*') }}"/>
                                    <x-nav-item href="{{ route('general-ledger.reports.aged-receivable') }}" icon="users" label="╪ث╪╣┘à╪د╪▒ ╪د┘╪░┘à┘à ╪د┘┘à╪»┘è┘╪ر" active="{{ request()->routeIs('general-ledger.reports.aged-receivable') }}"/>
                                    <x-nav-item href="{{ route('general-ledger.reports.aged-payable') }}" icon="user-group" label="╪ث╪╣┘à╪د╪▒ ╪د┘╪░┘à┘à ╪د┘╪»╪د╪خ┘╪ر" active="{{ request()->routeIs('general-ledger.reports.aged-payable') }}"/>
                                    <x-nav-item href="{{ route('general-ledger.reports.partner-ledger') }}" icon="clipboard-list" label="╪»┘╪ز╪▒ ╪ث╪│╪ز╪د╪░ ╪د┘╪╖╪▒┘" active="{{ request()->routeIs('general-ledger.reports.partner-ledger') }}"/>
                                    <x-nav-item href="{{ route('general-ledger.reports.account-statement') }}" icon="clipboard-list" label="╪ذ┘è╪د┘ ╪ص╪▒┘â╪ر ╪ص╪│╪د╪ذ" active="{{ request()->routeIs('general-ledger.reports.account-statement') }}"/>
                                    <x-nav-item href="{{ route('general-ledger.fiscal-years.index') }}" icon="calendar" label="╪د┘╪│┘┘ê╪د╪ز ╪د┘┘à╪د┘┘è╪ر" active="{{ request()->routeIs('general-ledger.fiscal-years.*') }}"/>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('treasury.*') ? 'true' : 'false' }} }" class="space-y-1">
                                <div class="pt-4 pb-2">
                                    <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-violet-300 uppercase tracking-wider hover:text-white transition-colors">
                                        <span>╪د┘╪«╪▓┘è┘╪ر</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                                    <x-nav-item href="{{ route('treasury.dashboard') }}" icon="chart-pie" label="┘┘ê╪ص╪ر ╪د┘╪ز╪ص┘â┘à" active="{{ request()->routeIs('treasury.dashboard') }}"/>
                                    <x-nav-item href="{{ route('treasury.chests.index') }}" icon="banknotes" label="╪د┘╪«╪▓╪د╪خ┘ ┘ê╪د┘╪ص╪│╪د╪ذ╪د╪ز ╪د┘╪ذ┘┘â┘è╪ر" active="{{ request()->routeIs('treasury.chests.*') }}"/>
                                    <x-nav-item href="{{ route('treasury.vouchers.index') }}" icon="credit-card" label="╪د┘╪│┘╪»╪د╪ز" active="{{ request()->routeIs('treasury.vouchers.*') }}"/>
                                    <x-nav-item href="{{ route('treasury.reconciliation.index') }}" icon="switch-horizontal" label="╪ز╪│┘ê┘è╪ر ╪د┘┘┘ê╪د╪ز┘è╪▒" active="{{ request()->routeIs('treasury.reconciliation.*') }}"/>
                                    <x-nav-item href="{{ route('treasury.reports.movements') }}" icon="chart-bar" label="╪ز┘é╪▒┘è╪▒ ╪ص╪▒┘â╪د╪ز ╪د┘╪«╪▓┘è┘╪ر" active="{{ request()->routeIs('treasury.reports.movements') }}"/>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('sales.*') ? 'true' : 'false' }} }" class="space-y-1">
                                <div class="pt-4 pb-2">
                                    <button @click="open = !open" class="w-full flex items-center justify-between pr-3 py-2.5 text-xs font-semibold text-violet-300 uppercase tracking-wider hover:text-white transition-colors">
                                        <span>╪د┘┘à╪ذ┘è╪╣╪د╪ز</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                                    <x-nav-item href="{{ route('sales.dashboard') }}" icon="chart-pie" label="┘┘ê╪ص╪ر ╪د┘╪ز╪ص┘â┘à" active="{{ request()->routeIs('sales.dashboard') }}"/>
                                    <x-nav-item href="{{ route('sales.quotations.index') }}" icon="document-text" label="╪╣╪▒┘ê╪╢ ╪د┘╪ث╪│╪╣╪د╪▒" active="{{ request()->routeIs('sales.quotations.*') }}"/>
                                    <x-nav-item href="{{ route('sales.delivery-notes.index') }}" icon="truck" label="╪ث╪░┘ê┘ ╪د┘╪ز╪│┘┘è┘à" active="{{ request()->routeIs('sales.delivery-notes.*') }}"/>
                                    <x-nav-item href="{{ route('sales.invoices.index') }}" icon="shopping-cart" label="┘┘ê╪د╪ز┘è╪▒ ╪د┘┘à╪ذ┘è╪╣╪د╪ز" active="{{ request()->routeIs('sales.invoices.*') }}"/>
                                    <x-nav-item href="{{ route('sales.returns.index') }}" icon="refresh" label="┘à╪▒╪ز╪ش╪╣╪د╪ز ╪د┘┘à╪ذ┘è╪╣╪د╪ز" active="{{ request()->routeIs('sales.returns.*') }}"/>
                                    <x-nav-item href="{{ route('sales.partners.index') }}" icon="users" label="╪د┘╪╣┘à┘╪د╪ة" active="{{ request()->routeIs('sales.partners.*') && !request()->routeIs('sales.partner-groups.*') }}"/>
                                    <x-nav-item href="{{ route('sales.partner-groups.index') }}" icon="tag" label="╪د┘┘à╪ش┘à┘ê╪╣╪د╪ز" active="{{ request()->routeIs('sales.partner-groups.*') }}"/>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('purchases.*') ? 'true' : 'false' }} }" class="space-y-1">
                                <div class="pt-4 pb-2">
                                    <button @click="open = !open" class="w-full flex items-center justify-between pr-3 py-2.5 text-xs font-semibold text-violet-300 uppercase tracking-wider hover:text-white transition-colors">
                                        <span>╪د┘┘à╪┤╪ز╪▒┘è╪د╪ز</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                                    <x-nav-item href="{{ route('purchases.dashboard') }}" icon="chart-pie" label="┘┘ê╪ص╪ر ╪د┘╪ز╪ص┘â┘à" active="{{ request()->routeIs('purchases.dashboard') }}"/>
                                    <x-nav-item href="{{ route('purchases.invoices.index') }}" icon="receipt-tax" label="┘┘ê╪د╪ز┘è╪▒ ╪د┘┘à╪┤╪ز╪▒┘è╪د╪ز" active="{{ request()->routeIs('purchases.invoices.*') }}"/>
                                    <x-nav-item href="{{ route('purchases.receipt-notes.index') }}" icon="save" label="╪ث╪░┘ê┘ ╪د┘╪د╪│╪ز┘╪د┘à" active="{{ request()->routeIs('purchases.receipt-notes.*') }}"/>
                                    <x-nav-item href="{{ route('purchases.returns.index') }}" icon="refresh" label="┘à╪▒╪ز╪ش╪╣╪د╪ز ╪د┘┘à╪┤╪ز╪▒┘è╪د╪ز" active="{{ request()->routeIs('purchases.returns.*') }}"/>
                                    <x-nav-item href="{{ route('sales.partners.index') }}" icon="users" label="╪د┘┘à┘ê╪▒╪»┘è┘" active="{{ request()->routeIs('sales.partners.*') && !request()->routeIs('sales.partner-groups.*') }}"/>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('inventory.*') ? 'true' : 'false' }} }" class="space-y-1">
                                <div class="pt-4 pb-2">
                                    <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-violet-300 uppercase tracking-wider hover:text-white transition-colors">
                                        <span>╪د┘┘à╪«╪▓┘ê┘</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                                    <x-nav-item href="{{ route('inventory.dashboard') }}" icon="chart-pie" label="┘┘ê╪ص╪ر ╪د┘╪ز╪ص┘â┘à" active="{{ request()->routeIs('inventory.dashboard') }}"/>
                                    <x-nav-item href="{{ route('inventory.items.index') }}" icon="cube" label="╪د┘╪ث╪╡┘╪د┘" active="{{ request()->routeIs('inventory.items.*') }}"/>
                                    <x-nav-item href="{{ route('inventory.categories.index') }}" icon="tag" label="╪د┘╪ز╪╡┘┘è┘╪د╪ز" active="{{ request()->routeIs('inventory.categories.*') }}"/>
                                    <x-nav-item href="{{ route('inventory.warehouses.index') }}" icon="home" label="╪د┘┘à╪«╪د╪▓┘" active="{{ request()->routeIs('inventory.warehouses.*') }}"/>
                                    <x-nav-item href="{{ route('inventory.stock-adjustments.index') }}" icon="adjustments" label="╪ز╪│┘ê┘è╪د╪ز ╪د┘┘à╪«╪▓┘ê┘" active="{{ request()->routeIs('inventory.stock-adjustments.*') }}"/>
                                    <x-nav-item href="{{ route('inventory.stock-transfers.index') }}" icon="switch-horizontal" label="╪ث╪░┘ê┘ ╪د┘╪ز╪ص┘ê┘è┘" active="{{ request()->routeIs('inventory.stock-transfers.*') }}"/>
                                    <x-nav-item href="{{ route('inventory.valuation') }}" icon="currency-dollar" label="╪ز┘é┘è┘è┘à ╪د┘┘à╪«╪▓┘ê┘" active="{{ request()->routeIs('inventory.valuation') }}"/>
                                    <x-nav-item href="{{ route('inventory.item-movements.index') }}" icon="switch-horizontal" label="╪ص╪▒┘â╪ر ╪د┘┘à╪«╪▓┘ê┘" active="{{ request()->routeIs('inventory.item-movements.*') }}"/>
                                    <x-nav-item href="{{ route('inventory.units.index') }}" icon="scale" label="╪د┘┘ê╪ص╪»╪د╪ز" active="{{ request()->routeIs('inventory.units.*') }}"/>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('hr.*') ? 'true' : 'false' }} }" class="space-y-1">
                                <div class="pt-4 pb-2">
                                    <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-violet-300 uppercase tracking-wider hover:text-white transition-colors">
                                        <span>╪┤╪ج┘ê┘ ╪د┘╪╣╪د┘à┘┘è┘</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                                    <x-nav-item href="{{ route('hr.dashboard') }}" icon="chart-pie" label="┘┘ê╪ص╪ر ╪د┘╪ز╪ص┘â┘à" active="{{ request()->routeIs('hr.dashboard') }}"/>
                                    <x-nav-item href="{{ route('hr.employees.index') }}" icon="users" label="╪د┘┘à┘ê╪╕┘┘è┘" active="{{ request()->routeIs('hr.employees.*') }}"/>
                                    <x-nav-item href="{{ route('hr.attendance.index') }}" icon="check-circle" label="╪د┘╪ص╪╢┘ê╪▒ ┘ê╪د┘╪د┘╪╡╪▒╪د┘" active="{{ request()->routeIs('hr.attendance.*') }}"/>
                                    <x-nav-item href="{{ route('hr.leaves.index') }}" icon="calendar" label="╪د┘╪ح╪ش╪د╪▓╪د╪ز" active="{{ request()->routeIs('hr.leaves.*') }}"/>
                                    <x-nav-item href="{{ route('hr.overtime.index') }}" icon="clock" label="╪د┘╪╣┘à┘ ╪د┘╪ح╪╢╪د┘┘è" active="{{ request()->routeIs('hr.overtime.*') }}"/>
                                    <x-nav-item href="{{ route('hr.payroll.index') }}" icon="currency-dollar" label="╪د┘┘à╪▒╪ز╪ذ╪د╪ز" active="{{ request()->routeIs('hr.payroll.*') }}"/>
                                    <x-nav-item href="{{ route('hr.job-titles.index') }}" icon="badge-check" label="╪د┘┘à╪│┘à┘è╪د╪ز ╪د┘┘ê╪╕┘è┘┘è╪ر" active="{{ request()->routeIs('hr.job-titles.*') }}"/>
                                    <x-nav-item href="{{ route('hr.departments.index') }}" icon="folder" label="╪د┘╪ث┘é╪│╪د┘à" active="{{ request()->routeIs('hr.departments.*') }}"/>
                                    <x-nav-item href="{{ route('hr.loans.index') }}" icon="credit-card" label="╪د┘╪│┘┘" active="{{ request()->routeIs('hr.loans.*') }}"/>
                                    <x-nav-item href="{{ route('hr.advances.index') }}" icon="briefcase" label="╪د┘╪╣┘ç╪»" active="{{ request()->routeIs('hr.advances.*') }}"/>
                                    <div class="pt-3 mt-2 border-t border-violet-800/30">
                                        <p class="text-[10px] text-violet-400 pr-3 mb-1 font-semibold tracking-wider uppercase">╪ز┘é╪د╪▒┘è╪▒</p>
                                        <x-nav-item href="{{ route('hr.reports.employee-summary') }}" icon="document-text" label="┘à┘╪«╪╡ ╪د┘┘à┘ê╪╕┘┘è┘" active="{{ request()->routeIs('hr.reports.employee-summary') }}"/>
                                        <x-nav-item href="{{ route('hr.reports.attendance') }}" icon="chart-bar" label="╪ز┘é╪▒┘è╪▒ ╪د┘╪ص╪╢┘ê╪▒" active="{{ request()->routeIs('hr.reports.attendance') }}"/>
                                        <x-nav-item href="{{ route('hr.reports.leave-balance') }}" icon="clipboard-list" label="╪ث╪▒╪╡╪»╪ر ╪د┘╪ح╪ش╪د╪▓╪د╪ز" active="{{ request()->routeIs('hr.reports.leave-balance') }}"/>
                                        <x-nav-item href="{{ route('hr.reports.payroll-summary') }}" icon="trending-up" label="┘à┘╪«╪╡ ╪د┘┘à╪▒╪ز╪ذ╪د╪ز" active="{{ request()->routeIs('hr.reports.payroll-summary') }}"/>
                                    </div>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }" class="space-y-1">
                                <div class="pt-4 pb-2">
                                    <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-violet-300 uppercase tracking-wider hover:text-white transition-colors">
                                        <span>╪د┘╪ز┘é╪د╪▒┘è╪▒</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                                    <x-nav-item href="{{ route('reports.income-statement') }}" icon="chart-bar" label="┘é╪د╪خ┘à╪ر ╪د┘╪»╪«┘" active="{{ request()->routeIs('reports.income-statement') }}"/>
                                    <x-nav-item href="{{ route('reports.balance-sheet') }}" icon="banknotes" label="╪د┘┘à┘è╪▓╪د┘┘è╪ر ╪د┘╪╣┘à┘ê┘à┘è╪ر" active="{{ request()->routeIs('reports.balance-sheet') }}"/>
                                    <x-nav-item href="{{ route('reports.cash-flow') }}" icon="currency-dollar" label="╪د┘╪ز╪»┘┘é╪د╪ز ╪د┘┘┘é╪»┘è╪ر" active="{{ request()->routeIs('reports.cash-flow') }}"/>
                                    <x-nav-item href="{{ route('reports.sales-analysis') }}" icon="trending-up" label="╪ز╪ص┘┘è┘ ╪د┘┘à╪ذ┘è╪╣╪د╪ز" active="{{ request()->routeIs('reports.sales-analysis') }}"/>
                                    <x-nav-item href="{{ route('reports.vat-return') }}" icon="receipt-tax" label="╪د┘╪ح┘é╪▒╪د╪▒ ╪د┘╪╢╪▒┘è╪ذ┘è" active="{{ request()->routeIs('reports.vat-return') }}"/>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('settings.*') || request()->routeIs('profile.*') ? 'true' : 'false' }} }" class="space-y-1">
                                <div class="pt-4 pb-2">
                                    <button @click="open = !open" class="w-full flex items-center justify-between text-xs font-semibold text-violet-300 uppercase tracking-wider hover:text-white transition-colors">
                                        <span>╪د┘╪ح╪╣╪»╪د╪»╪د╪ز</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                                    <x-nav-item href="{{ route('settings.dashboard') }}" icon="adjustments" label="┘┘ê╪ص╪ر ╪د┘╪ح╪╣╪»╪د╪»╪د╪ز" active="{{ request()->routeIs('settings.dashboard') }}"/>
                                    @can('users.view')
                                    <x-nav-item href="{{ route('settings.users.index') }}" icon="users" label="╪د┘┘à╪│╪ز╪«╪»┘à┘è┘" active="{{ request()->routeIs('settings.users.*') }}"/>
                                    <x-nav-item href="{{ route('settings.roles.index') }}" icon="shield-check" label="╪د┘╪╡┘╪د╪ص┘è╪د╪ز" active="{{ request()->routeIs('settings.roles.*') }}"/>
                                    @endcan
                                    <x-nav-item href="{{ route('settings.company') }}" icon="office-building" label="╪ذ┘è╪د┘╪د╪ز ╪د┘╪┤╪▒┘â╪ر" active="{{ request()->routeIs('settings.company') }}"/>
                                    <x-nav-item href="{{ route('settings.companies.index') }}" icon="switch-horizontal" label="╪ح╪»╪د╪▒╪ر ╪د┘╪┤╪▒┘â╪د╪ز" active="{{ request()->routeIs('settings.companies.*') }}"/>
                                    <x-nav-item href="{{ route('settings.hr') }}" icon="briefcase" label="╪ح╪╣╪»╪د╪»╪د╪ز ╪┤╪ج┘ê┘ ╪د┘╪╣╪د┘à┘┘è┘" active="{{ request()->routeIs('settings.hr') }}"/>
                                    <x-nav-item href="{{ route('profile.show') }}" icon="user" label="╪د┘┘à┘┘ ╪د┘╪┤╪«╪╡┘è" active="{{ request()->routeIs('profile.*') }}"/>
                                </div>
                            </div>

                            <div class="pt-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-violet-300 hover:text-red-400 hover:bg-red-900/20 rounded-apple-sm transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>╪ز╪│╪ش┘è┘ ╪د┘╪«╪▒┘ê╪ش</span>
                                    </button>
                                </form>
                            </div>
                        </nav>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="flex-1 min-w-0">
                    <div class="p-4 sm:p-6 lg:p-8 pb-16">
                        {{-- Page Header --}}
                        @hasSection('page-header')
                            <div class="mb-6">
                                <h1 class="text-2xl font-semibold text-gray-900">@yield('page-header')</h1>
                                @hasSection('breadcrumb')
                                    <div class="mt-1 text-sm text-gray-400">@yield('breadcrumb')</div>
                                @endif
                            </div>
                        @endif

                        {{-- Flash Messages --}}
                        @if(session('success'))
                            <div class="mb-4 p-4 bg-success/10 border border-success/20 text-success rounded-apple text-sm animate-slide-up">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="mb-4 p-4 bg-danger/10 border border-danger/20 text-danger rounded-apple text-sm animate-slide-up">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if(session('warning'))
                            <div class="mb-4 p-4 bg-warning/10 border border-warning/20 text-warning rounded-apple text-sm animate-slide-up">
                                {{ session('warning') }}
                            </div>
                        @endif
                        @if(session('import_errors'))
                            <div class="mb-4 p-4 bg-warning/10 border border-warning/20 text-warning rounded-apple text-sm animate-slide-up">
                                <p class="font-medium mb-1">╪ث╪«╪╖╪د╪ة ╪د┘╪د╪│╪ز┘è╪▒╪د╪»:</p>
                                <ul class="list-disc list-inside">
                                    @foreach(session('import_errors') as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </main>
            </div>
        @endauth

        @guest
            @yield('content')
        @endguest
    </div>

    @stack('scripts')

<style media="print">
    @page {
        margin: 2cm;
        @top-left { content: none; }
        @top-center { content: none; }
        @top-right { content: none; }
        @bottom-left { content: none; }
        @bottom-center { content: none; }
        @bottom-right { content: none; }
    }
    *, *::before, *::after { background: transparent !important; box-shadow: none !important; text-shadow: none !important; }
    body { background: white !important; }
    .no-print { display: none !important; }
    main, main > div { display: block !important; }
    [class*="pt-16"] { padding-top: 0 !important; }
    .card { border: none !important; box-shadow: none !important; padding: 6px !important; margin-bottom: 6px !important; }
    h1, [class*="text-2xl"], [class*="text-3xl"] { font-size: 14px !important; margin: 0 0 2px 0 !important; }
    .stat-label { font-size: 9px !important; }
    .font-mono { font-size: 10px !important; }
    table, tr, td, th { page-break-inside: avoid !important; }
    .table-cell { padding: 3px 6px !important; font-size: 9px !important; }
    .table-header { padding: 4px 6px !important; font-size: 9px !important; }
    .grid { gap: 4px !important; margin-bottom: 4px !important; }
    .badge-success, .badge-danger, .badge-warning, .badge-info { font-size: 8px !important; padding: 2px 4px !important; }
    [class*="breadcrumb"], [class*="mt-1"]:not(.table-cell):not(.font-mono) { display: none !important; }
    .mb-6, .mb-4, .mb-8 { margin-bottom: 4px !important; }
    .p-4, .p-6, .p-8 { padding: 4px !important; }
    .py-2, .py-3, .py-4 { padding-top: 2px !important; padding-bottom: 2px !important; }
    .px-4, .px-6 { padding-left: 4px !important; padding-right: 4px !important; }
    .gap-4, .gap-6 { gap: 4px !important; }
    a { text-decoration: none !important; }
    svg { display: none !important; }
    .print-footer { display: block !important; position: fixed; bottom: 8px; left: 0; right: 0; text-align: center; font-size: 8px; color: #999; }
</style>
<div class="print-footer" style="display:none">Developed by Bassam Dawood 2026</div>

<script>
(function() {
    // Dark mode
    var dm = localStorage.getItem('darkMode') === 'true';
    var html = document.documentElement;
    var moon = document.getElementById('moonIcon');
    var sun = document.getElementById('sunIcon');

    function applyDark(enabled) {
        html.classList.toggle('dark', enabled);
        if (moon) moon.classList.toggle('hidden', enabled);
        if (sun) sun.classList.toggle('hidden', !enabled);
    }

    applyDark(dm);

    // DB health check
    var indicator = document.getElementById('dbIndicator');
    var textEl = document.getElementById('dbText');

    function checkDb() {
        fetch('/_ping').then(function(r) {
            if (r.ok) {
                if (indicator) { indicator.className = 'w-2 h-2 rounded-full inline-block bg-emerald-500 animate-pulse'; }
                if (textEl) textEl.textContent = '┘à╪ز╪╡┘';
            } else {
                if (indicator) { indicator.className = 'w-2 h-2 rounded-full inline-block bg-red-500'; }
                if (textEl) textEl.textContent = '┘à┘┘╪╡┘';
            }
        }).catch(function() {
            if (indicator) { indicator.className = 'w-2 h-2 rounded-full inline-block bg-red-500'; }
            if (textEl) textEl.textContent = '┘à┘┘╪╡┘';
        });
    }

    checkDb();
    setInterval(checkDb, 30000);
})();

function toggleDarkMode() {
    var enabled = localStorage.getItem('darkMode') !== 'true';
    localStorage.setItem('darkMode', enabled);
    document.documentElement.classList.toggle('dark', enabled);
    var moon = document.getElementById('moonIcon');
    var sun = document.getElementById('sunIcon');
    if (moon) moon.classList.toggle('hidden', enabled);
    if (sun) sun.classList.toggle('hidden', !enabled);
}

function toggleSidebar() {
    var s = document.getElementById('sidebar');
    if (!s) return;
    var w = window.innerWidth;
    if (w >= 1024) {
        var open = s.classList.contains('lg:w-64');
        s.classList.toggle('lg:w-64', !open);
        s.classList.toggle('lg:w-0', open);
    } else {
        var o = document.getElementById('sidebarOverlay');
        if (o) {
            var open = o.classList.contains('hidden');
            o.classList.toggle('hidden', !open);
            s.classList.toggle('max-lg:translate-x-0', !open);
            s.classList.toggle('max-lg:translate-x-full', open);
        }
    }
}

function closeSidebar() {
    var s = document.getElementById('sidebar');
    var o = document.getElementById('sidebarOverlay');
    if (o) o.classList.add('hidden');
    if (s) {
        s.classList.remove('max-lg:translate-x-0');
        s.classList.add('max-lg:translate-x-full');
    }
}
</script>
</body>
</html>
