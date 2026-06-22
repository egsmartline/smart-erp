@props(['url' => '#', 'label' => 'PDF', 'type' => 'pdf'])
<div class="inline-flex items-center gap-2 no-print" x-data>
    <a href="{{ $url }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-primary-50 hover:text-primary-600 hover:border-primary-300 transition">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17v2a2 2 0 01-2 2H5a2 2 0 01-2-2V9.414a1 1 0 01.293-.707l5.414-5.414A1 1 0 019.414 3H13"/></svg>
        {{ $label }}
    </a>
    <button @click="$root.closest('[x-data]')?.__x?.$data ? $root.closest('[x-data]').__x.$data.printModalOpen = true : window.print()" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300 transition">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        طباعة
    </button>
</div>
