<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'purchase_currency_id')) {
                $table->foreignId('purchase_currency_id')->nullable()->after('cost_price')->constrained('currencies')->nullOnDelete();
            }
            if (!Schema::hasColumn('items', 'sales_currency_id')) {
                $table->foreignId('sales_currency_id')->nullable()->after('selling_price')->constrained('currencies')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['purchase_currency_id']);
            $table->dropForeign(['sales_currency_id']);
            $table->dropColumn(['purchase_currency_id', 'sales_currency_id']);
        });
    }
};
