<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('draft','sent','confirmed','received','invoiced','cancelled') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('draft','sent','confirmed','received','cancelled') NOT NULL DEFAULT 'draft'");
    }
};
