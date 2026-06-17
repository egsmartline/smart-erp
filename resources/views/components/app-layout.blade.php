@props(['header' => null])

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Smart ERP') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 font-['IBM_Plex_Sans_Arabic'] antialiased">
    <div class="min-h-screen">
        {{-- Sidebar --}}
        <x-layouts.sidebar />

        {{-- Main Content --}}
        <div class="mr-64">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <button class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    @if($header)
                        {{ $header }}
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">{{ date('Y/m/d') }}</span>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
