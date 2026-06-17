<?php

namespace App\Livewire\Shared;

use Livewire\Component;

class SearchDropdown extends Component
{
    public $value = '';
    public $label = '';
    public $placeholder = 'بحث...';
    public $options = [];
    public $filteredOptions = [];
    public $selectedOption = null;
    public $selectedId = null;
    public $name = '';
    public $required = false;
    public $disabled = false;
    public $showDropdown = false;
    public $highlightIndex = -1;

    protected $listeners = [
        'clearSelection' => 'clearSelection',
    ];

    public function mount(
        string $value = '',
        string $label = '',
        string $placeholder = 'بحث...',
        array $options = [],
        string $name = '',
        bool $required = false,
        bool $disabled = false,
        $selectedId = null
    ) {
        $this->value = $value;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->options = $options;
        $this->name = $name;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->selectedId = $selectedId;

        if ($selectedId) {
            $this->selectedOption = collect($this->options)->firstWhere('id', $selectedId);
        }

        $this->filteredOptions = $this->options;
    }

    public function updatedValue(): void
    {
        $this->search($this->value);
    }

    public function search(string $term): void
    {
        if (strlen($term) < 1) {
            $this->filteredOptions = $this->options;
            $this->showDropdown = false;
            return;
        }

        $term = strtolower($term);

        $this->filteredOptions = array_filter($this->options, function ($option) use ($term) {
            $label = strtolower($option['label'] ?? '');
            $code = strtolower($option['code'] ?? '');
            $nameEn = strtolower($option['name_en'] ?? '');

            return str_contains($label, $term)
                || str_contains($code, $term)
                || str_contains($nameEn, $term);
        });

        $this->filteredOptions = array_values($this->filteredOptions);
        $this->showDropdown = true;
        $this->highlightIndex = -1;
    }

    public function selectOption(array $option): void
    {
        $this->selectedOption = $option;
        $this->selectedId = $option['id'];
        $this->value = $option['label'] ?? '';
        $this->showDropdown = false;
        $this->highlightIndex = -1;

        $this->dispatch('optionSelected', [
            'name' => $this->name,
            'id' => $option['id'],
            'label' => $option['label'] ?? '',
            'code' => $option['code'] ?? '',
        ]);
    }

    public function clearSelection(): void
    {
        $this->selectedOption = null;
        $this->selectedId = null;
        $this->value = '';
        $this->showDropdown = false;
    }

    public function handleKeydown(string $event): void
    {
        $count = count($this->filteredOptions);

        switch ($event) {
            case 'ArrowDown':
                $this->highlightIndex = min($this->highlightIndex + 1, $count - 1);
                $this->showDropdown = true;
                break;
            case 'ArrowUp':
                $this->highlightIndex = max($this->highlightIndex - 1, -1);
                break;
            case 'Enter':
                if ($this->highlightIndex >= 0 && $this->highlightIndex < $count) {
                    $this->selectOption($this->filteredOptions[$this->highlightIndex]);
                }
                break;
            case 'Escape':
                $this->showDropdown = false;
                $this->highlightIndex = -1;
                break;
        }
    }

    public function closeDropdown(): void
    {
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.shared.search-dropdown');
    }
}
