<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivewireLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_layout_loads_livewire_assets(): void
    {
        $tenant = Tenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User',
            'email' => 'layout-test@example.com',
            'password' => 'secret',
            'role' => 'admin',
        ]);

        session(['current_tenant_id' => $tenant->id]);

        $response = $this->actingAs($user)->get('/journal-entries/create');

        $response->assertOk();
        $this->assertStringContainsString('Livewire Styles', $response->getContent());
        $this->assertStringContainsString('livewire.min.js', $response->getContent());
    }
}
