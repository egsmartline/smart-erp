<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <label class="mb-1 block text-sm font-medium text-gray-700">{{ $label }}</label>
    <input type="text" wire:model.live="searchTerm" @focus="open = true" placeholder="{{ $placeholder }}"
        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" autocomplete="off">
    <input type="hidden" wire:model="selectedId" {{ $required ? 'required' : '' }}>

    @if(count($results) > 0)
        <div class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-60 overflow-y-auto" x-show="open">
            @foreach($results as $item)
                <div wire:click="select({{ $item->id }})" class="cursor-pointer px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-0">
                    <div class="font-medium text-gray-900">{{ $item->name }}</div>
                    @if(isset($item->phone))
                        <div class="text-xs text-gray-500">{{ $item->phone ?? '' }}</div>
                    @endif
                    @if(isset($item->sku))
                        <div class="text-xs text-gray-500">SKU: {{ $item->sku ?? '' }} | {{ number_format($item->selling_price ?? 0, 2) }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @error($modelProperty)
        <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>
