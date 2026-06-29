@extends('layouts.auth')

@section('content')
<div class="min-h-screen login-bg flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">اختر الشركة</h2>
            <p class="text-gray-500 mt-2">اختر الشركة التي تريد العمل بها</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($tenants as $tenant)
                <form action="{{ route('switch-tenant', $tenant->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-right rounded-xl border-2 border-gray-200 bg-white p-6 hover:border-blue-500 hover:shadow-lg transition-all cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors shrink-0">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="flex-1 text-right">
                                <h3 class="text-lg font-bold text-gray-800">{{ $tenant->name }}</h3>
                                @if($tenant->commercial_registration)
                                    <p class="text-xs text-gray-500 mt-0.5">سجل تجاري: {{ $tenant->commercial_registration }}</p>
                                @endif
                            </div>
                            <svg class="h-5 w-5 text-gray-300 group-hover:text-blue-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
@endsection
