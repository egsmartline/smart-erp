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

        {{-- Back to Company Selection --}}
        <div class="flex-shrink-0 px-3 pt-3">
            <a href="{{ route('select-company') }}"
                class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-emerald-500 transition-all text-emerald-100 hover:text-white text-sm font-medium">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span x-show="sidebarOpen">العودة لاختيار الشركة</span>
            </a>
        </div>

        {{-- Navigation --}}
        <div class="flex-1 overflow-y-auto py-3 px-2">
            <ul class="space-y-1">
                {{-- الشركات --}}
                <li x-data="{ open: true }">
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
                            <span class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm text-emerald-100">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-300 flex-shrink-0"></span>
                                <span x-show="sidebarOpen">إدارة الشركات</span>
                            </span>
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
    <div :class="sidebarOpen ? 'mr-64' : 'mr-16'" class="flex-1 min-h-screen bg-gradient-to-br from-emerald-50 via-white to-emerald-100" style="transition: margin-right 0.3s;">
        {{-- Top Bar --}}
        <div class="sticky top-0 z-40 flex items-center justify-between bg-white/80 backdrop-blur-md border-b border-gray-200 px-6 py-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">إدارة الشركات</h1>
                <p class="text-sm text-gray-500">إدارة وتعديل الشركات المسجلة في النظام</p>
            </div>
            <a href="{{ route('companies.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة شركة جديدة
            </a>
        </div>

        {{-- Content --}}
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-sm text-right">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الشعار</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">اسم الشركة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرقم الضريبي</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الهاتف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد الإلكتروني</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العملة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($companies as $company)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-10 w-10 rounded-lg object-cover">
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-sm font-bold text-emerald-600">
                                            {{ substr($company->name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $company->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $company->tax_number ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $company->phone ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $company->email ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                        {{ $company->currency_code }}
                                    </span>
                                    @if($company->secondaryCurrency)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 mr-1">
                                            {{ $company->secondary_currency_code }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($company->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">نشطة</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">غير نشطة</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('companies.show', $company) }}" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-emerald-600 transition" title="عرض">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('companies.edit', $company) }}" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-yellow-600 transition" title="تعديل">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('companies.destroy', $company) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الشركة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-600 transition" title="حذف">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <p class="mt-2 text-sm">لا توجد شركات مسجلة</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection