<?php

namespace Tests\Feature;

use Tests\TestCase;

class LivewireLayoutTest extends TestCase
{
    public function test_app_layout_loads_livewire_assets(): void
    {
        $content = file_get_contents(resource_path('views/components/app-layout.blade.php'));

        $this->assertStringContainsString('@livewireStyles', $content);
        $this->assertStringContainsString('@livewireScripts', $content);
    }
}
