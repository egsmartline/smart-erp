<?php

namespace App\Livewire;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JournalEntryForm extends Component
{
    public $entryId = null;
    public $entryNumber = '';
    public $date = '';
    public $description = '';
    public $reference = '';
    public $lines = [];
    public $accounts = [];
    public $totalDebit = 0;
    public $totalCredit = 0;
    public $balanceDifference = 0;
    public $accountSearchTerm = '';
    public $searchingLineIndex = null;
    public $filteredAccounts = [];

    protected $listeners = [
        'updateLineAccount' => 'updateLineAccount',
    ];

    public function mount($entryId = null, $entryNumber = '', $date = null)
    {
        $this->entryId = $entryId;
        $this->entryNumber = $entryNumber;
        $this->date = $date ?? date('Y-m-d');

        $this->accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->toArray();

        if ($entryId) {
            $this->loadEntry();
        } else {
            $this->addLine();
            $this->addLine();
        }
    }

    public function loadEntry(): void
    {
        $entry = \App\Models\JournalEntry::where('id', $this->entryId)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->with('lines')
            ->first();

        if ($entry) {
            $this->date = $entry->date;
            $this->description = $entry->description;
            $this->reference = $entry->reference ?? '';
            $this->entryNumber = $entry->entry_number;

            $this->lines = [];
            foreach ($entry->lines as $line) {
                $this->lines[] = [
                    'id' => $line->id,
                    'account_id' => $line->account_id,
                    'account_code' => $line->account->code ?? '',
                    'account_name' => $line->account->name ?? '',
                    'debit' => $line->debit,
                    'credit' => $line->credit,
                    'description' => $line->description ?? '',
                ];
            }

            $this->calculateTotals();
        }
    }

    public function addLine(): void
    {
        $this->lines[] = [
            'id' => null,
            'account_id' => null,
            'account_code' => '',
            'account_name' => '',
            'debit' => 0,
            'credit' => 0,
            'description' => '',
        ];

        $this->calculateTotals();
    }

    public function removeLine(int $index): void
    {
        if (count($this->lines) > 2) {
            unset($this->lines[$index]);
            $this->lines = array_values($this->lines);
            $this->calculateTotals();
        }
    }

    public function updateLineAccount(int $index, int $accountId): void
    {
        $account = collect($this->accounts)->firstWhere('id', $accountId);

        if ($account) {
            $this->lines[$index]['account_id'] = $account['id'];
            $this->lines[$index]['account_code'] = $account['code'];
            $this->lines[$index]['account_name'] = $account['name'];
        }
    }

    public function updatedLines($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = (int) $parts[0];
            $field = $parts[1];

            if ($field === 'debit' || $field === 'credit') {
                $this->lines[$index][$field] = (float) $this->lines[$index][$field];
                $this->calculateTotals();
            }
        }
    }

    public function calculateTotals(): void
    {
        $this->totalDebit = collect($this->lines)->sum('debit');
        $this->totalCredit = collect($this->lines)->sum('credit');
        $this->balanceDifference = $this->totalDebit - $this->totalCredit;
    }

    public function searchAccounts(string $term, int $lineIndex): void
    {
        $this->searchingLineIndex = $lineIndex;
        $this->accountSearchTerm = $term;

        if (strlen($term) >= 1) {
            $this->filteredAccounts = collect($this->accounts)
                ->filter(function ($account) use ($term) {
                    return str_contains($account['code'], $term)
                        || str_contains($account['name'], $term)
                        || str_contains($account['name_en'] ?? '', $term);
                })
                ->take(10)
                ->values()
                ->toArray();
        } else {
            $this->filteredAccounts = [];
        }
    }

    public function selectAccount(int $accountId, int $lineIndex): void
    {
        $account = collect($this->accounts)->firstWhere('id', $accountId);

        if ($account) {
            $this->lines[$lineIndex]['account_id'] = $account['id'];
            $this->lines[$lineIndex]['account_code'] = $account['code'];
            $this->lines[$lineIndex]['account_name'] = $account['name'];
        }

        $this->filteredAccounts = [];
        $this->searchingLineIndex = null;
        $this->accountSearchTerm = '';
    }

    public function getValidationRules(): array
    {
        return [
            'date' => 'required|date',
            'description' => 'required|string|max:500',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ];
    }

    public function getValidationMessages(): array
    {
        return [
            'date.required' => 'التاريخ مطلوب',
            'date.date' => 'التاريخ غير صالح',
            'description.required' => 'البيان مطلوب',
            'description.max' => 'البيان طويل جداً',
            'lines.required' => 'يجب إضافة سطور على الأقل',
            'lines.min' => 'يجب إضافة سطرين على الأقل',
            'lines.*.account_id.required' => 'يجب اختيار حساب',
            'lines.*.debit.required' => 'المدين مطلوب',
            'lines.*.debit.numeric' => 'المدين يجب أن يكون رقماً',
            'lines.*.credit.required' => 'الدائن مطلوب',
            'lines.*.credit.numeric' => 'الدائن يجب أن يكون رقماً',
        ];
    }

    public function submit(): void
    {
        $this->calculateTotals();

        if (abs($this->balanceDifference) > 0.001) {
            session()->flash('error', 'إجمالي المدين لا يساوي إجمالي الدائن');
            return;
        }

        $hasDebit = collect($this->lines)->contains('debit', '>', 0);
        $hasCredit = collect($this->lines)->contains('credit', '>', 0);

        if (!$hasDebit || !$hasCredit) {
            session()->flash('error', 'يجب أن يحتوي القيد على مدين ودائن على الأقل');
            return;
        }

        $hasEmptyAccount = collect($this->lines)->contains('account_id', null);

        if ($hasEmptyAccount) {
            session()->flash('error', 'يجب اختيار حساب لكل سطر');
            return;
        }

        $this->dispatch('submitJournalEntry', [
            'date' => $this->date,
            'description' => $this->description,
            'reference' => $this->reference,
            'lines' => $this->lines,
        ]);
    }

    public function render()
    {
        return view('livewire.journal-entry-form');
    }
}
