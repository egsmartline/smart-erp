<div>
    <form wire:submit.prevent="submit" class="rounded-xl bg-white shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">رقم القيد</label>
                <input type="text" value="{{ $entryNumber }}" readonly
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                <input type="date" wire:model="date" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">المرجع</label>
                <input type="text" wire:model="reference" placeholder="رقم المرجع..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
        </div>

        <div class="mb-6">
            <label class="mb-1 block text-sm font-medium text-gray-700">البيان <span class="text-red-500">*</span></label>
            <input type="text" wire:model="description" required placeholder="بيان القيد..."
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">سطور القيد</h3>
            <button type="button" wire:click="addLine"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة سطر
            </button>
        </div>

        <div class="overflow-x-auto mb-4">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-3 py-2 font-semibold text-gray-700 w-8">#</th>
                        <th class="px-3 py-2 font-semibold text-gray-700">الحساب</th>
                        <th class="px-3 py-2 font-semibold text-gray-700">البيان</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">مدين</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">دائن</th>
                        <th class="px-3 py-2 font-semibold text-gray-700 text-center w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lines as $index => $line)
                        <tr class="border-b border-gray-100">
                            <td class="px-3 py-2 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-3 py-2">
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                    <input type="text"
                                        wire:model="lines.{{ $index }}.account_name"
                                        wire:input="searchAccounts($event.target.value, {{ $index }})"
                                        @focus="open = true"
                                        placeholder="بحث عن حساب..."
                                        class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        autocomplete="off">

                                    @if($searchingLineIndex === $index && count($filteredAccounts) > 0)
                                        <div class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-60 overflow-y-auto" x-show="open">
                                            @foreach($filteredAccounts as $account)
                                                <div wire:click="selectAccount({{ $account['id'] }}, {{ $index }})"
                                                    class="cursor-pointer px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-0">
                                                    <div class="font-mono text-xs text-gray-500">{{ $account['code'] }}</div>
                                                    <div class="font-medium text-gray-900">{{ $account['name'] }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" wire:model="lines.{{ $index }}.account_id">
                                @error("lines.{$index}.account_id") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" wire:model="lines.{{ $index }}.description"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    placeholder="بيان السطر...">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" wire:model.live="lines.{{ $index }}.debit"
                                    step="0.01" min="0"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    placeholder="0.00">
                                @error("lines.{$index}.debit") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" wire:model.live="lines.{{ $index }}.credit"
                                    step="0.01" min="0"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-left font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    placeholder="0.00">
                                @error("lines.{$index}.credit") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if(count($lines) > 2)
                                    <button type="button" wire:click="removeLine({{ $index }})"
                                        class="rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 transition cursor-pointer">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="3" class="px-3 py-3 text-gray-700">الإجمالي</td>
                        <td class="px-3 py-3 text-left font-mono text-sm {{ $totalDebit > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                            {{ number_format($totalDebit, 2) }}
                        </td>
                        <td class="px-3 py-3 text-left font-mono text-sm {{ $totalCredit > 0 ? 'text-purple-600' : 'text-gray-400' }}">
                            {{ number_format($totalCredit, 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if(abs($balanceDifference) > 0.001)
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-800 border border-red-200">
                <strong>تنبيه:</strong> الفرق بين المدين والدائن: {{ number_format(abs($balanceDifference), 2) }}
                (يجب أن يكون الإجمالي متساوياً)
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-800 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                حفظ القيد
            </button>
            <a href="{{ route('journal-entries.index') }}" class="rounded-lg bg-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>
