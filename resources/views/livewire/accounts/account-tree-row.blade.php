@php
    $children = $this->getChildren($account['id']);
    $hasChildren = count($children) > 0;
    $isExpanded = $this->isExpanded($account['id']);
    $paddingLeft = $level * 24;
@endphp

<tr class="border-b border-gray-100 hover:bg-gray-50 transition {{ $level > 0 ? 'bg-gray-50/50' : '' }}">
    <td class="px-4 py-3">
        @if($hasChildren)
            <button wire:click="toggleExpand({{ $account['id'] }})"
                class="rounded p-1 text-gray-500 hover:bg-gray-200 transition cursor-pointer">
                <svg class="h-4 w-4 transform {{ $isExpanded ? 'rotate-90' : '' }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        @else
            <span class="inline-block w-6"></span>
        @endif
    </td>
    <td class="px-4 py-3">
        <span style="padding-right: {{ $paddingLeft }}px" class="font-mono text-xs font-bold text-gray-600">
            {{ $account['code'] }}
        </span>
    </td>
    <td class="px-4 py-3">
        @if($editingAccountId === $account['id'])
            <div class="flex items-center gap-2">
                <input type="text" wire:model="editName" class="rounded border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="اسم الحساب">
                <input type="text" wire:model="editNameEn" class="rounded border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="الاسم الإنجليزي">
                <button wire:click="saveEdit({{ $account['id'] }})" class="rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700 cursor-pointer">حفظ</button>
                <button wire:click="cancelEditing" class="rounded bg-gray-200 px-2 py-1 text-xs text-gray-700 hover:bg-gray-300 cursor-pointer">إلغاء</button>
            </div>
        @else
            <div class="flex items-center gap-2">
                <a href="{{ route('accounts.show', $account['id']) }}" class="font-medium text-gray-900 hover:text-blue-600">
                    {{ $account['name'] }}
                </a>
                @if(!empty($account['name_en']))
                    <span class="text-xs text-gray-500">({{ $account['name_en'] }})</span>
                @endif
            </div>
        @endif
    </td>
    <td class="px-4 py-3">
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $this->getTypeBadgeClass($account['type']) }}">
            {{ $this->getTypeName($account['type']) }}
        </span>
    </td>
    <td class="px-4 py-3 text-left font-mono text-sm {{ ($account['balance'] ?? 0) >= 0 ? 'text-gray-900' : 'text-red-600' }}">
        {{ number_format($account['balance'] ?? 0, 2) }}
    </td>
    <td class="px-4 py-3 text-center">
        <form action="{{ route('accounts.toggle-status', $account['id']) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ ($account['is_active'] ?? false) ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition cursor-pointer">
                {{ ($account['is_active'] ?? false) ? 'نشط' : 'غير نشط' }}
            </button>
        </form>
    </td>
    <td class="px-4 py-3 text-center">
        <div class="inline-flex items-center gap-1">
            @if($editingAccountId !== $account['id'])
                <button wire:click="startEditing({{ $account['id'] }})" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition cursor-pointer" title="تعديل سريع">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
            @endif
            <a href="{{ route('accounts.edit', $account['id']) }}" class="rounded p-1 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition" title="تعديل كامل">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </td>
</tr>

@if($isExpanded && $hasChildren)
    @foreach($children as $child)
        @include('livewire.accounts.account-tree-row', [
            'account' => $child,
            'level' => $level + 1,
        ])
    @endforeach
@endif

@if($addingToParentId === $account['id'])
    <tr class="bg-blue-50">
        <td class="px-4 py-3"></td>
        <td class="px-4 py-3">
            <input type="text" wire:model="newAccountCode" class="w-20 rounded border border-gray-300 px-2 py-1 text-sm font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="الكود">
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center gap-2">
                <input type="text" wire:model="newAccountName" class="rounded border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="اسم الحساب">
                <input type="text" wire:model="newAccountNameEn" class="rounded border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="الاسم الإنجليزي">
                <button wire:click="saveNewAccount" class="rounded bg-green-600 px-3 py-1 text-xs text-white hover:bg-green-700 cursor-pointer">حفظ</button>
                <button wire:click="cancelAdding" class="rounded bg-gray-200 px-3 py-1 text-xs text-gray-700 hover:bg-gray-300 cursor-pointer">إلغاء</button>
            </div>
        </td>
        <td colspan="4"></td>
    </tr>
@endif
