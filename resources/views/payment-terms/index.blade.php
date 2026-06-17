<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">شروط الدفع</h2>
            <a href="{{ route('payment-terms.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة شرط دفع
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="px-4 py-3 font-semibold">الاسم</th>
                        <th class="px-4 py-3 font-semibold">نوع الخصم</th>
                        <th class="px-4 py-3 font-semibold text-left">نسبة الخصم</th>
                        <th class="px-4 py-3 font-semibold text-center">أيام الاستحقاق</th>
                        <th class="px-4 py-3 font-semibold text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentTerms as $term)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('payment-terms.show', $term) }}" class="text-gray-900 hover:text-blue-600">{{ $term->name }}</a>
                                @if($term->name_en)
                                    <div class="text-xs text-gray-500">{{ $term->name_en }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($term->discount_type === 'percentage')
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">نسبة مئوية</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مبلغ ثابت</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-left font-mono">{{ $term->discount_percent }}%</td>
                            <td class="px-4 py-3 text-center">{{ $term->days_net }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($term->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">نشط</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">غير نشط</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('payment-terms.edit', $term) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="تعديل">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('payment-terms.destroy', $term) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف شرط الدفع هذا؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1 text-gray-500 hover:bg-red-50 hover:text-red-600 transition cursor-pointer" title="حذف">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد شروط دفع</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $paymentTerms->links() }}
        </div>
    </div>
</x-app-layout>
