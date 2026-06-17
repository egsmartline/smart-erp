<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    @if($label)
        <label class="mb-1 block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input type="text"
            wire:model.live.debounce.300ms="value"
            @focus="open = true"
            placeholder="{{ $placeholder }}"
            {{ $disabled ? 'disabled' : '' }}
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 {{ $disabled ? 'bg-gray-100 cursor-not-allowed' : '' }}">

        @if($selectedOption)
            <button type="button" wire:click="clearSelection"
                class="absolute left-2 top-1/2 -translate-y-1/2 rounded p-1 text-gray-400 hover:text-gray-600 cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </div>

    @if($selectedOption)
        <input type="hidden" name="{{ $name }}" value="{{ $selectedId }}">
    @endif

    @if(count($filteredOptions) > 0 && !$disabled)
        <div class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-60 overflow-y-auto" x-show="open">
            @foreach($filteredOptions as $option)
                <div wire:click="selectOption({{ json_encode($option) }})"
                    class="cursor-pointer px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-0
                        {{ ($highlightIndex === $loop->index) ? 'bg-blue-50' : '' }}">
                    @if(isset($option['code']))
                        <div class="font-mono text-xs text-gray-500">{{ $option['code'] }}</div>
                    @endif
                    <div class="font-medium text-gray-900">{{ $option['label'] ?? '' }}</div>
                    @if(isset($option['subtitle']))
                        <div class="text-xs text-gray-500">{{ $option['subtitle'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
