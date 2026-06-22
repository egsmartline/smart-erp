<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">إذن استلام</h2>
            <a href="{{ route('purchase-receipt-notes.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إنشاء إذن استلام
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">رقم الإذن</th>
                        <th class="px-4 py-3 font-semibold">التاريخ</th>
                        <th class="px-4 py-3 font-semibold">أمر الشراء</th>
                        <th class="px-4 py-3 font-semibold">المورد</th>
                        <th class="px-4 py-3 font-semibold text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receiptNotes as $note)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('purchase-receipt-notes.show', $note) }}" class="text-gray-900 hover:text-blue-600">{{ $note->receipt_number }}</a>
                            </td>
                            <td class="px-4 py-3">{{ $note->date->format('Y/m/d') }}</td>
                            <td class="px-4 py-3">{{ $note->purchaseOrder->order_number ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $note->supplier->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($note->status === 'confirmed')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مؤكد</span>
                                @elseif($note->status === 'draft')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مسودة</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">ملغي</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('purchase-receipt-notes.show', $note) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="عرض">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm7 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد إذنات استلام</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $receiptNotes->links() }}
        </div>
    </div>
</x-app-layout>
