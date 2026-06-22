<?php

use App\Models\FiscalYear;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE journal_entries MODIFY COLUMN fiscal_year_id BIGINT UNSIGNED NULL');

        foreach (Tenant::all() as $tenant) {
            if (!FiscalYear::where('tenant_id', $tenant->id)->exists()) {
                FiscalYear::create([
                    'tenant_id' => $tenant->id,
                    'name' => '2026',
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-12-31',
                    'is_active' => true,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE journal_entries MODIFY COLUMN fiscal_year_id BIGINT UNSIGNED NOT NULL');
    }
};
