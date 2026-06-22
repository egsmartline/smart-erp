<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">الرئيسية</h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">إجمالي الحسابات</div>
                    <div class="text-2xl font-bold text-gray-900">{{ \App\Models\Account::where('tenant_id', Auth::user()->tenant_id ?? 1)->count() }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">القيود اليومية</div>
                    <div class="text-2xl font-bold text-gray-900">{{ \App\Models\JournalEntry::where('tenant_id', Auth::user()->tenant_id ?? 1)->count() }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">القيود المسودة</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ \App\Models\JournalEntry::where('tenant_id', Auth::user()->tenant_id ?? 1)->where('is_posted', false)->count() }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">القيود المرحّلة</div>
                    <div class="text-2xl font-bold text-green-600">{{ \App\Models\JournalEntry::where('tenant_id', Auth::user()->tenant_id ?? 1)->where('is_posted', true)->count() }}</div>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">روابط سريعة</h3>
            <div class="space-y-3">
                <a href="{{ route('accounts.index') }}" class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">دليل الحسابات</div>
                        <div class="text-sm text-gray-500">إدارة الحسابات المحاسبية</div>
                    </div>
                </a>
                <a href="{{ route('journal-entries.create') }}" class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">قيد يومي جديد</div>
                        <div class="text-sm text-gray-500">إنشاء قيد محاسبي جديد</div>
                    </div>
                </a>
                <a href="{{ route('journal-entries.index') }}" class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">القيود اليومية</div>
                        <div class="text-sm text-gray-500">عرض وإدارة القيود</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">آخر القيود</h3>
            <div class="space-y-3">
                @forelse(\App\Models\JournalEntry::where('tenant_id', Auth::user()->tenant_id ?? 1)->with('creator')->latest()->take(5)->get() as $entry)
                    <a href="{{ route('journal-entries.show', $entry) }}" class="flex items-center justify-between rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition">
                        <div>
                            <div class="font-mono text-xs text-gray-500">{{ $entry->entry_number }}</div>
                            <div class="font-medium text-gray-900">{{ $entry->description }}</div>
                            <div class="text-xs text-gray-500">{{ $entry->date?->format('Y/m/d') }}</div>
                        </div>
                        <div class="text-left">
                            @if($entry->is_posted)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">مرحل</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center text-gray-500 py-4">لا توجد قيود بعد</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
