<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->default(0)->after('barcode');
            }
            if (!Schema::hasColumn('items', 'minimum_stock')) {
                $table->decimal('minimum_stock', 15, 2)->default(0)->after('tax_rate');
            }
            if (!Schema::hasColumn('items', 'maximum_stock')) {
                $table->decimal('maximum_stock', 15, 2)->default(0)->after('minimum_stock');
            }
            if (!Schema::hasColumn('items', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
            if (!Schema::hasColumn('items', 'has_serial_numbers')) {
                $table->boolean('has_serial_numbers')->default(false)->after('has_expiry');
            }
            if (!Schema::hasColumn('items', 'has_expiry_date')) {
                $table->boolean('has_expiry_date')->default(false)->after('has_serial_numbers');
            }
            if (!Schema::hasColumn('items', 'image')) {
                $table->string('image')->nullable()->after('has_expiry_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'cost_price', 'minimum_stock', 'maximum_stock',
                'name_ar', 'has_serial_numbers', 'has_expiry_date', 'image',
            ]);
        });
    }
};
