@extends('layouts.app')

@section('title', 'الخزائن والحسابات البنكية')
@section('page-header', 'الخزائن والحسابات البنكية')
@section('breadcrumb', 'الخزينة / الخزائن والحسابات البنكية')

@section('content')
    <div class="card">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('treasury.chests.index', ['type' => '']) }}"
                   class="btn-outline text-sm px-3 py-1.5 {{ !request('type') ? 'bg-primary-50 border-primary-200 text-primary-700' : '' }}">
                    الكل
                </a>
                <a href="{{ route('treasury.chests.index', ['type' => 'cash']) }}"
                   class="btn-outline text-sm px-3 py-1.5 {{ request('type') === 'cash' ? 'bg-primary-50 border-primary-200 text-primary-700' : '' }}">
                    خزائن نقدية
                </a>
                <a href="{{ route('treasury.chests.index', ['type' => 'bank']) }}"
                   class="btn-outline text-sm px-3 py-1.5 {{ request('type') === 'bank' ? 'bg-primary-50 border-primary-200 text-primary-700' : '' }}">
                    حسابات بنكية
                </a>
            </div>
            <div class="no-print flex items-center gap-2">
                <a href="{{ route('treasury.vouchers.create') }}?type=transfer" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    تحويل مالي
                </a>
                <button onclick="window.print()" class="btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    طباعة
                </button>
                <a href="{{ route('treasury.chests.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة خزنة
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="table-header">الاسم</th>
                        <th class="table-header">النوع</th>
                        <th class="table-header">العملة</th>
                        <th class="table-header">الرصيد</th>
                        <th class="table-header">بيانات البنك</th>
                        <th class="table-header">الحالة</th>
                        <th class="table-header">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chests as $chest)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="table-cell font-medium">{{ $chest->name }}</td>
                            <td class="table-cell">
                                @if($chest->type === 'cash')
                                    <span class="badge-info">خزنة نقدية</span>
                                @else
                                    <span class="badge-gray">حساب بنكي</span>
                                @endif
                            </td>
                            <td class="table-cell font-mono text-xs">{{ $chest->currency }}</td>
                            <td class="table-cell font-mono font-medium">{{ number_format($chest->balance, 2) }}</td>
                            <td class="table-cell text-xs">
                                @if($chest->type === 'bank')
                                    <div>{{ $chest->bank_name }}</div>
                                    <div class="text-gray-400 mt-0.5" dir="ltr">{{ $chest->account_number }}</div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                @if($chest->active)
                                    <span class="badge-success">مفعل</span>
                                @else
                                    <span class="badge-gray">غير مفعل</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('treasury.chests.edit', $chest) }}" class="text-primary-500 hover:text-primary-700 transition-colors" title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('treasury.chests.destroy', $chest) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخزنة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger-500 hover:text-danger-700 transition-colors" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">لا توجد خزائن</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
