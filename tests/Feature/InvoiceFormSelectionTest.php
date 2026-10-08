<?php

namespace Tests\Feature;

use App\Livewire\InvoiceForm;
use App\Models\Item;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceFormSelectionTest extends TestCase
{
    use RefreshDatabase;

    private function seedTenantAndUser(): Tenant
    {
        $tenant = Tenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User',
            'email' => 'invoice-test@example.com',
            'password' => 'secret',
            'role' => 'admin',
        ]);

        $this->actingAs($user);
        session(['current_tenant_id' => $tenant->id]);

        return $tenant;
    }

    public function test_selecting_an_item_sets_default_quantity_and_price(): void
    {
        $tenant = $this->seedTenantAndUser();

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
        $tenant = $this->seedTenantAndUser();

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

        $component = Livewire::test(InvoiceForm::class, ['type' => 'sale', 'showItemSelect' => true])
            ->set('lines.0.item_id', $item->id)
            ->call('selectItem', $item->id, 0);

        $component->assertSet('lines.0.quantity', 1);
        $component->assertSet('lines.0.unit_price', 1500.0);
    }
}
