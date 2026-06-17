<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AccountTree extends Component
{
    public $expandedNodes = [];
    public $searchTerm = '';
    public $accounts = [];
    public $rootAccounts = [];
    public $editingAccountId = null;
    public $editName = '';
    public $editNameEn = '';
    public $addingToParentId = null;
    public $newAccountCode = '';
    public $newAccountName = '';
    public $newAccountNameEn = '';
    public $newAccountType = 'assets';
    public $selectedType = '';

    public function mount()
    {
        $this->loadAccounts();
    }

    public function loadAccounts(): void
    {
        $query = Account::where('tenant_id', Auth::user()->tenant_id)
            ->with('parent');

        if ($this->searchTerm) {
            $search = $this->searchTerm;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($this->selectedType) {
            $query->where('type', $this->selectedType);
        }

        $this->accounts = $query->orderBy('code')->get()->toArray();
        $this->rootAccounts = collect($this->accounts)->whereNull('parent_id')->toArray();
    }

    public function toggleExpand(int $nodeId): void
    {
        if (in_array($nodeId, $this->expandedNodes)) {
            $this->expandedNodes = array_diff($this->expandedNodes, [$nodeId]);
        } else {
            $this->expandedNodes[] = $nodeId;
        }
    }

    public function isExpanded(int $nodeId): bool
    {
        return in_array($nodeId, $this->expandedNodes);
    }

    public function getChildren(int $parentId): array
    {
        return collect($this->accounts)->where('parent_id', $parentId)->toArray();
    }

    public function getTypeName(string $type): string
    {
        return match ($type) {
            'assets' => 'أصول',
            'liabilities' => 'خصوم',
            'equity' => 'حقوق ملكية',
            'revenue' => 'إيرادات',
            'expenses' => 'مصروفات',
            default => $type,
        };
    }

    public function getTypeBadgeClass(string $type): string
    {
        return match ($type) {
            'assets' => 'bg-blue-100 text-blue-800',
            'liabilities' => 'bg-red-100 text-red-800',
            'equity' => 'bg-purple-100 text-purple-800',
            'revenue' => 'bg-green-100 text-green-800',
            'expenses' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function startEditing(int $accountId): void
    {
        $account = collect($this->accounts)->firstWhere('id', $accountId);
        if ($account) {
            $this->editingAccountId = $accountId;
            $this->editName = $account['name'];
            $this->editNameEn = $account['name_en'] ?? '';
        }
    }

    public function cancelEditing(): void
    {
        $this->editingAccountId = null;
        $this->editName = '';
        $this->editNameEn = '';
    }

    public function saveEdit(int $accountId): void
    {
        $account = Account::where('id', $accountId)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->first();

        if ($account) {
            $account->update([
                'name' => $this->editName,
                'name_en' => $this->editNameEn,
            ]);

            session()->flash('success', 'تم تحديث الحساب بنجاح');
        }

        $this->cancelEditing();
        $this->loadAccounts();
    }

    public function startAddingToParent(int $parentId): void
    {
        $this->addingToParentId = $parentId;
        $this->newAccountCode = '';
        $this->newAccountName = '';
        $this->newAccountNameEn = '';
    }

    public function cancelAdding(): void
    {
        $this->addingToParentId = null;
        $this->newAccountCode = '';
        $this->newAccountName = '';
        $this->newAccountNameEn = '';
    }

    public function saveNewAccount(): void
    {
        $validated = $this->validate([
            'newAccountCode' => 'required|string|max:10',
            'newAccountName' => 'required|string|max:255',
        ], [
            'newAccountCode.required' => 'كود الحساب مطلوب',
            'newAccountCode.max' => 'كود الحساب طويل جداً',
            'newAccountName.required' => 'اسم الحساب مطلوب',
            'newAccountName.max' => 'اسم الحساب طويل جداً',
        ]);

        $parentAccount = collect($this->accounts)->firstWhere('id', $this->addingToParentId);

        Account::create([
            'code' => $validated['newAccountCode'],
            'name' => $validated['newAccountName'],
            'name_en' => $this->newAccountNameEn,
            'type' => $parentAccount['type'] ?? 'assets',
            'sub_type' => 'sub',
            'parent_id' => $this->addingToParentId,
            'opening_balance' => 0,
            'balance' => 0,
            'is_active' => true,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        $this->cancelAdding();
        $this->loadAccounts();

        session()->flash('success', 'تم إنشاء الحساب بنجاح');
    }

    public function toggleStatus(int $accountId): void
    {
        $account = Account::where('id', $accountId)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->first();

        if ($account) {
            $account->update(['is_active' => !$account->is_active]);
            $this->loadAccounts();

            $status = $account->is_active ? 'تفعيل' : 'إلغاء تفعيل';
            session()->flash('success', "تم {$status} الحساب بنجاح");
        }
    }

    public function render()
    {
        return view('livewire.accounts.account-tree');
    }
}
