@extends('layouts.auth')

@section('content')
<style>
    .sidebar-green { background: linear-gradient(135deg, #047857, #059669) !important; }
</style>
<div class="flex min-h-screen" x-data="{ sidebarOpen: true }">
    {{-- Sidebar --}}
    <aside class="sidebar-green fixed right-0 top-0 bottom-0 text-white flex flex-col z-50 w-64"
        :class="{ 'w-16': !sidebarOpen }" style="transition: width 0.3s;">
        {{-- User Info --}}
        <div class="flex-shrink-0 p-4" style="border-bottom: 1px solid #10b981;">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-emerald-700 font-bold text-lg flex-shrink-0">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div x-show="sidebarOpen" class="flex-1 min-w-0">
                    <div class="text-sm font-bold truncate text-white">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-emerald-100 truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex-1 overflow-y-auto py-3 px-2">
            <ul class="space-y-1">
                {{-- الشركات --}}
                <li x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl hover:bg-emerald-500 transition-all text-right group">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap text-sm font-medium text-white">الشركات</span>
                        </div>
                        <svg x-show="sidebarOpen" class="h-4 w-4 text-emerald-200 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <ul x-show="open" x-collapse class="mt-1 mr-6 space-y-1">
                        <li>
                            <a href="{{ route('companies.manage') }}"
                                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-emerald-100 hover:text-white hover:bg-emerald-500 transition-all">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-300 flex-shrink-0"></span>
                                <span x-show="sidebarOpen">إدارة الشركات</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        {{-- Logout --}}
        <div class="flex-shrink-0 px-3 pb-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 transition-all text-white text-sm font-bold cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarOpen">تسجيل الخروج</span>
                </button>
            </form>
        </div>

        {{-- Sidebar Toggle --}}
        <div class="flex-shrink-0 px-3 pb-3 pt-3" style="border-top: 1px solid #10b981;">
            <button @click="sidebarOpen = !sidebarOpen"
                class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-xl hover:bg-emerald-500 transition-all text-emerald-100 hover:text-white">
                <svg class="h-5 w-5 transition-transform" :class="sidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <span x-show="sidebarOpen" class="text-sm">طي القائمة</span>
            </button>
        </div>
    </aside>

    {{-- Main Content --}}
    <div :class="sidebarOpen ? 'mr-64' : 'mr-16'" class="flex-1 min-h-screen bg-gradient-to-br from-blue-50 via-white to-emerald-50" style="transition: margin-right 0.3s;">
        {{-- Top Bar --}}
        <div class="sticky top-0 z-40 flex items-center justify-between bg-white/80 backdrop-blur-md border-b border-gray-200 px-6 py-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">اختر الشركة</h1>
                <p class="text-sm text-gray-500">اختر الشركة التي تريد العمل بها</p>
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-500">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ now()->format('Y/m/d') }}</span>
            </div>
        </div>

        {{-- Companies Grid --}}
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($companies as $company)
                    <form action="{{ route('switch-company', $company->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-right rounded-2xl border-2 border-gray-200 bg-white p-6 hover:border-blue-500 hover:shadow-lg hover:-translate-y-0.5 transition-all cursor-pointer group">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl shrink-0 overflow-hidden">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-100 to-blue-50 text-blue-600 group-hover:from-blue-600 group-hover:to-blue-700 group-hover:text-white transition-all">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 text-right">
                                    <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ $company->name }}</h3>
                                    @if($company->tax_number)
                                        <p class="text-xs text-gray-500 mt-0.5">رقم ضريبي: {{ $company->tax_number }}</p>
                                    @endif
                                    @if($company->phone)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $company->phone }}</p>
                                    @endif
                                </div>
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 group-hover:bg-blue-100 transition-colors">
                                    <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
