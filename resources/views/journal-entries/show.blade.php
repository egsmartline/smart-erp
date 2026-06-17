<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">تفاصيل القيد: {{ $journalEntry->entry_number }}</h2>
            <div class="flex items-center gap-3">
                @if(!$journalEntry->is_posted)
                    <a href="{{ route('journal-entries.edit', $journalEntry) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        تعديل
                    </a>
                    <form action="{{ route('journal-entries.post', $journalEntry) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من ترحيل هذا القيد؟')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition cursor-pointer">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            ترحيل
                        </button>
                    </form>
                @endif
                <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    طباعة
                </button>
                <a href="{{ route('journal-entries.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                    رجوع
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">معلومات القيد</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">رقم القيد:</span>
                    <span class="font-mono font-bold text-gray-900">{{ $journalEntry->entry_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">التاريخ:</span>
                    <span class="font-medium text-gray-900">{{ $journalEntry->date }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">البيان:</span>
                    <span class="font-medium text-gray-900">{{ $journalEntry->description }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">المرجع:</span>
                    <span class="font-medium text-gray-900">{{ $journalEntry->reference ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">الحالة:</span>
                    @if($journalEntry->is_posted)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مرحل</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">أنشأه:</span>
                    <span class="font-medium text-gray-900">{{ $journalEntry->creator->name ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">الإجماليات</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <span class="text-gray-700 font-medium">إجمالي المدين:</span>
                    <span class="text-xl font-bold text-blue-600 font-mono">{{ number_format($journalEntry->total_debit, 2) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                    <span class="text-gray-700 font-medium">إجمالي الدائن:</span>
                    <span class="text-xl font-bold text-purple-600 font-mono">{{ number_format($journalEntry->total_credit, 2) }}</span>
                </div>
                <div class="flex justify-between items-center p-3 {{ $journalEntry->total_debit == $journalEntry->total_credit ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                    <span class="text-gray-700 font-medium">التوازن:</span>
                    <span class="font-bold {{ $journalEntry->total_debit == $journalEntry->total_credit ? 'text-green-600' : 'text-red-600' }}">
                        {{ $journalEntry->total_debit == $journalEntry->total_credit ? 'متوازن' : 'غير متوازن' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">سطور القيد</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">#</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">كود الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">اسم الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">البيان</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">مدين</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">دائن</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->lines as $index => $line)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">{{ $line->account->code }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $line->account->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $line->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-left font-mono text-sm {{ $line->debit > 0 ? 'text-gray-900 font-bold' : 'text-gray-400' }}">
                                {{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-left font-mono text-sm {{ $line->credit > 0 ? 'text-gray-900 font-bold' : 'text-gray-400' }}">
                                {{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="4" class="px-4 py-3 text-gray-700">الإجمالي</td>
                        <td class="px-4 py-3 text-left font-mono text-sm text-blue-600">{{ number_format($journalEntry->total_debit, 2) }}</td>
                        <td class="px-4 py-3 text-left font-mono text-sm text-purple-600">{{ number_format($journalEntry->total_credit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
