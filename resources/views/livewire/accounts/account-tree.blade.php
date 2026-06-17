<div>
    <div class="mb-6 flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="mb-1 block text-sm font-medium text-gray-700">بحث</label>
            <input type="text" wire:model.live.debounce.300ms="searchTerm" placeholder="بحث بالاسم أو الكود..."
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="min-w-[180px]">
            <label class="mb-1 block text-sm font-medium text-gray-700">نوع الحساب</label>
            <select wire:model.live="selectedType" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">الكل</option>
                <option value="assets">أصول</option>
                <option value="liabilities">خصوم</option>
                <option value="equity">حقوق ملكية</option>
                <option value="revenue">إيرادات</option>
                <option value="expenses">مصروفات</option>
            </select>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-semibold text-gray-700 w-8"></th>
                        <th class="px-4 py-3 font-semibold text-gray-700">الكود</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">اسم الحساب</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">النوع</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-left">الرصيد</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">الحالة</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rootAccounts as $rootAccount)
                        @include('livewire.accounts.account-tree-row', [
                            'account' => $rootAccount,
                            'level' => 0,
                        ])
                    @endforeach

                    @if(empty($rootAccounts))
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد حسابات</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
