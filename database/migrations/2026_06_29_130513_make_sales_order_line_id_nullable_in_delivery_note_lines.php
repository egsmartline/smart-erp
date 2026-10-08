<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_delivery_note_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_order_line_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales_delivery_note_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_order_line_id')->nullable(false)->change();
        });
    }
};
