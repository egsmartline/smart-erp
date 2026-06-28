<?php

namespace Tests\Feature;

use App\Livewire\InvoiceForm;
use App\Models\Item;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceFormSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_selecting_an_item_sets_default_quantity_and_price(): void
    {
        $tenant = Tenant::create(['name' => 'Test Tenant']);

        $item = Item::create([
            'tenant_id' => $tenant->id,
            'name' => 'ماتور',
            'name_ar' => 'ماتور',
            'sku' => 'MT-001',
            'cost_price' => 900,
            'selling_price' => 1500,
            'tax_rate' => 15,
            'is_active' => true,
        ]);

        $component = new \App\Livewire\InvoiceForm();
        $component->mount('sale');
        $component->selectItem($item->id, 0);

        $this->assertSame(1, $component->lines[0]['quantity']);
        $this->assertSame('1500.00', (string) $component->lines[0]['unit_price']);
    }

    public function test_invoice_form_renders_selected_price_for_the_line(): void
    {
        $tenant = Tenant::create(['name' => 'Test Tenant']);

        Item::create([
            'tenant_id' => $tenant->id,
            'name' => 'ماتور',
            'name_ar' => 'ماتور',
            'sku' => 'MT-001',
            'cost_price' => 900,
            'selling_price' => 1500,
            'tax_rate' => 15,
            'is_active' => true,
        ]);

        $component = Livewire::test(InvoiceForm::class, ['type' => 'sale', 'showItemSelect' => true])
            ->set('lines.0.item_id', 1)
            ->call('selectItem', 1, 0);

        $component->assertSet('lines.0.quantity', 1);
        $component->assertSet('lines.0.unit_price', 1500.0);
    }
}
