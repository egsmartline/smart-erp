<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Currency;
use Livewire\Component;

class InvoiceForm extends Component
{
    public $type = 'sale';
    public $invoiceId = null;
    public $customerId = '';
    public $supplierId = '';
    public $warehouseId = '';
    public $date = '';
    public $dueDate = '';
    public $notes = '';
    public $discountAmount = 0;
    public $shippingAmount = 0;
    public $lines = [];
    public $subtotal = 0;
    public $totalDiscount = 0;
    public $totalTax = 0;
    public $grandTotal = 0;

    public $currencyId = '';
    public $currencies = [];

    public $customerSearch = '';
    public $supplierSearch = '';
    public $itemSearches = [];
    public $filteredCustomers = [];
    public $filteredSuppliers = [];
    public $filteredItems = [];
    public $searchingLineIndex = null;

    public $customers = [];
    public $suppliers = [];
    public $warehouses = [];
    public $allItems = [];

    public $showCustomerSearch = true;
    public $showItemSelect = false;

    public function mount($type = 'sale', $invoiceId = null, $customerId = null, $supplierId = null, $warehouseId = null, $showCustomerSearch = true, $showItemSelect = false)
    {
        $this->showCustomerSearch = $showCustomerSearch;
        $this->showItemSelect = $showItemSelect;
        $this->type = $type;
        $this->invoiceId = $invoiceId;
        $this->date = date('Y-m-d');
        $this->dueDate = date('Y-m-d', strtotime('+30 days'));

        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;

        $this->warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $this->currencies = Currency::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $defaultCurrency = $this->currencies->firstWhere('is_default', true) ?? $this->currencies->first();
        $this->currencyId = $defaultCurrency ? $defaultCurrency->id : '';

        if ($type === 'sale') {
            $this->customers = Customer::where('tenant_id', $tenantId)->where('is_active', true)->get();
            if ($customerId) $this->customerId = $customerId;
        } else {
            $this->suppliers = Supplier::where('tenant_id', $tenantId)->where('is_active', true)->get();
            if ($supplierId) $this->supplierId = $supplierId;
        }

        if ($this->showItemSelect) {
            $this->allItems = Item::where('tenant_id', $tenantId)->where('is_active', true)->get();
        }

        if ($warehouseId) {
            $this->warehouseId = $warehouseId;
        }

        if ($invoiceId) {
            $this->loadInvoice();
        } else {
            $this->addLine();
        }
    }

    public function loadInvoice(): void
    {
        if ($this->type === 'sale') {
            $invoice = \App\Models\SalesInvoice::with('lines.item')->find($this->invoiceId);
            if ($invoice) {
                $this->customerId = $invoice->customer_id;
                $this->warehouseId = $invoice->warehouse_id;
                $this->currencyId = $invoice->currency_id;
                $this->date = $invoice->date->format('Y-m-d');
                $this->dueDate = $invoice->due_date->format('Y-m-d');
                $this->notes = $invoice->notes;
                $this->discountAmount = $invoice->discount_amount;
                $this->shippingAmount = $invoice->shipping_amount;
                $this->lines = $invoice->lines->map(fn($l) => [
                    'id' => $l->id,
                    'item_id' => $l->item_id,
                    'item_name' => $l->item->name ?? '',
                    'description' => $l->description,
                    'quantity' => $l->quantity,
                    'unit_price' => $l->unit_price,
                    'discount_percent' => $l->discount_percent,
                    'tax_rate' => $l->tax_rate,
                ])->toArray();
            }
        } else {
            $invoice = \App\Models\PurchaseInvoice::with('lines.item')->find($this->invoiceId);
            if ($invoice) {
                $this->supplierId = $invoice->supplier_id;
                $this->warehouseId = $invoice->warehouse_id;
                $this->currencyId = $invoice->currency_id;
                $this->date = $invoice->date->format('Y-m-d');
                $this->dueDate = $invoice->due_date->format('Y-m-d');
                $this->notes = $invoice->notes;
                $this->discountAmount = $invoice->discount_amount;
                $this->shippingAmount = $invoice->shipping_amount;
                $this->lines = $invoice->lines->map(fn($l) => [
                    'id' => $l->id,
                    'item_id' => $l->item_id,
                    'item_name' => $l->item->name ?? '',
                    'description' => $l->description,
                    'quantity' => $l->quantity,
                    'unit_price' => $l->unit_cost,
                    'discount_percent' => $l->discount_percent,
                    'tax_rate' => $l->tax_rate,
                ])->toArray();
            }
        }

        $this->calculateTotals();
    }

    public function addLine(): void
    {
        $this->lines[] = [
            'id' => null,
            'item_id' => '',
            'item_name' => '',
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'discount_percent' => 0,
            'tax_rate' => 15,
        ];
    }

    public function removeLine(int $index): void
    {
        if (count($this->lines) > 1) {
            unset($this->lines[$index]);
            $this->lines = array_values($this->lines);
            $this->calculateTotals();
        }
    }

    public function updated($property, $value): void
    {
        if (preg_match('/^lines\.(\d+)\.item_id$/', $property, $matches)) {
            $this->selectItem((int)$value, (int)$matches[1]);
            return;
        }
        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = 0;
        $this->totalDiscount = 0;
        $this->totalTax = 0;

        foreach ($this->lines as $index => $line) {
            $lineSubtotal = ($line['quantity'] ?? 0) * ($line['unit_price'] ?? 0);
            $lineDiscount = $lineSubtotal * (($line['discount_percent'] ?? 0) / 100);
            $lineAfterDiscount = $lineSubtotal - $lineDiscount;
            $lineTax = $lineAfterDiscount * (($line['tax_rate'] ?? 0) / 100);

            $this->subtotal += $lineSubtotal;
            $this->totalDiscount += $lineDiscount;
            $this->totalTax += $lineTax;
        }

        $this->grandTotal = $this->subtotal - $this->totalDiscount - $this->discountAmount + $this->totalTax + $this->shippingAmount;
    }

    public function updatedCustomerSearch(): void
    {
        if (strlen($this->customerSearch) >= 1) {
            $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
            $this->filteredCustomers = Customer::where('tenant_id', $tenantId)->where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', "%{$this->customerSearch}%")
                      ->orWhere('name_ar', 'like', "%{$this->customerSearch}%")
                      ->orWhere('phone', 'like', "%{$this->customerSearch}%");
                })
                ->limit(10)
                ->get();
        } else {
            $this->filteredCustomers = [];
        }
    }

    public function updatedItemSearches($value, $key): void
    {
        $index = (int) $key;
        $this->searchingLineIndex = $index;

        if (strlen($value) >= 1) {
            $this->filteredItems = Item::where('is_active', true)
                ->where(function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%")
                      ->orWhere('name_ar', 'like', "%{$value}%")
                      ->orWhere('sku', 'like', "%{$value}%")
                      ->orWhere('barcode', 'like', "%{$value}%");
                })
                ->limit(10)
                ->get();
        } else {
            $this->filteredItems = [];
            $this->searchingLineIndex = null;
        }
    }

    public function selectCustomer($id): void
    {
        $customer = Customer::find($id);
        if ($customer) {
            $this->customerId = $id;
            $this->customerSearch = $customer->name;
            $this->filteredCustomers = [];
        }
    }

    public function updatedSupplierSearch(): void
    {
        if (strlen($this->supplierSearch) >= 1) {
            $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
            $this->filteredSuppliers = Supplier::where('tenant_id', $tenantId)->where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', "%{$this->supplierSearch}%")
                      ->orWhere('name_ar', 'like', "%{$this->supplierSearch}%")
                      ->orWhere('phone', 'like', "%{$this->supplierSearch}%");
                })
                ->limit(10)
                ->get();
        } else {
            $this->filteredSuppliers = [];
        }
    }

    public function selectSupplier($id): void
    {
        $supplier = Supplier::find($id);
        if ($supplier) {
            $this->supplierId = $id;
            $this->supplierSearch = $supplier->name;
            $this->filteredSuppliers = [];
        }
    }

    public function selectItem(int $itemId, int $index): void
    {
        $item = Item::find($itemId);
        if ($item) {
            $this->lines[$index]['item_id'] = $item->id;
            $this->lines[$index]['item_name'] = $item->name;
            $this->lines[$index]['description'] = $item->name_en ?? $item->name;
            $this->lines[$index]['quantity'] = 1;
            $this->lines[$index]['unit_price'] = $this->type === 'sale' ? $item->selling_price : $item->purchase_price;
            $this->lines[$index]['tax_rate'] = $item->tax_rate ?? 15;
            $this->itemSearches[$index] = $item->name ?? '';
        }

        $this->filteredItems = [];
        $this->searchingLineIndex = null;
        $this->calculateTotals();
    }

    public function clearSearch(): void
    {
        $this->filteredCustomers = [];
        $this->filteredSuppliers = [];
        $this->filteredItems = [];
        $this->searchingLineIndex = null;
    }

    public function getFormData(): array
    {
        $this->calculateTotals();

        return [
            $this->type === 'sale' ? 'customer_id' : 'supplier_id' => $this->type === 'sale' ? $this->customerId : $this->supplierId,
            'warehouse_id' => $this->warehouseId,
            'currency_id' => $this->currencyId,
            'date' => $this->date,
            'due_date' => $this->dueDate,
            'notes' => $this->notes,
            'discount_amount' => $this->discountAmount,
            'shipping_amount' => $this->shippingAmount,
            'lines' => array_map(fn($l) => [
                'item_id' => $l['item_id'],
                'description' => $l['description'] ?? '',
                'quantity' => $l['quantity'],
                'unit_price' => $l['unit_price'],
                'unit_cost' => $l['unit_price'],
                'discount_percent' => $l['discount_percent'] ?? 0,
                'tax_rate' => $l['tax_rate'] ?? 0,
            ], $this->lines),
        ];
    }

    public function render()
    {
        return view('livewire.invoice-form');
    }
}
