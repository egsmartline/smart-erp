<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE sales_delivery_notes MODIFY sales_order_id BIGINT(20) UNSIGNED NULL');
        DB::statement('ALTER TABLE sales_delivery_note_lines MODIFY sales_order_id BIGINT(20) UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE sales_delivery_notes MODIFY sales_order_id BIGINT(20) UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE sales_delivery_note_lines MODIFY sales_order_id BIGINT(20) UNSIGNED NOT NULL');
    }
};
