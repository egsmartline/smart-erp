<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">العهد</h2>
            <a href="{{ route('custodies.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                عهدة جديدة
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <form method="GET" class="mb-4 flex gap-4">
            <select name="employee_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">كل الموظفين</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name ?? $emp->first_name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">كل الحالات</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>مرتجعة جزئياً</option>
                <option value="settled" {{ request('status') == 'settled' ? 'selected' : '' }}>مصفاة</option>
            </select>
            <button type="submit" class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition">بحث</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700">رقم العهدة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الموظف</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">المبلغ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">المردود</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">التاريخ</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($custodies as $custody)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $custody->custody_number }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900"><a href="{{ route('custodies.show', $custody) }}" class="hover:text-blue-600">{{ $custody->employee->full_name ?? $custody->employee->first_name ?? '-' }}</a></td>
                            <td class="px-4 py-3 text-center font-mono font-bold">{{ number_format($custody->amount, 2) }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ number_format($custody->returned_amount, 2) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $custody->date instanceof \Carbon\Carbon ? $custody->date->format('Y/m/d') : $custody->date }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $custody->account->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($custody->status == 'active')
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">نشطة</span>
                                @elseif($custody->status == 'partial')
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">مرتجعة جزئياً</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">مصفاة</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('custodies.show', $custody) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="عرض">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($custody->status !== 'settled')
                                    <a href="{{ route('custodies.settle', $custody) }}" class="rounded p-1 text-gray-500 hover:bg-green-50 hover:text-green-600 transition" title="تسوية">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </a>
                                    @endif
                                    <a href="{{ route('custodies.edit', $custody) }}" class="rounded p-1 text-gray-500 hover:bg-amber-50 hover:text-amber-600 transition" title="تعديل">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('custodies.destroy', $custody) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
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
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">لا توجد عهد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $custodies->links() }}</div>
    </div>
</x-app-layout>
