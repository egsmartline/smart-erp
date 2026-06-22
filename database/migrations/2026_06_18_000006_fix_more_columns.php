<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_units', function (Blueprint $table) {
            if (!Schema::hasColumn('item_units', 'base_unit_id')) {
                $table->foreignId('base_unit_id')->nullable()->after('conversion_factor')->constrained('item_units')->nullOnDelete();
            }
            if (!Schema::hasColumn('item_units', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
            if (!Schema::hasColumn('item_units', 'symbol')) {
                $table->string('symbol', 10)->nullable()->after('name_ar');
            }
        });

        Schema::table('warehouses', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouses', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('warehouses', 'manager_name')) {
                $table->string('manager_name')->nullable()->after('manager_id');
            }
            if (!Schema::hasColumn('warehouses', 'manager_phone')) {
                $table->string('manager_phone', 50)->nullable()->after('manager_name');
            }
            if (!Schema::hasColumn('warehouses', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('item_units', function (Blueprint $table) {
            $table->dropForeign(['base_unit_id']);
            $table->dropColumn(['base_unit_id', 'name_ar', 'symbol']);
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn(['city', 'manager_name', 'manager_phone', 'is_default']);
        });
    }
};
