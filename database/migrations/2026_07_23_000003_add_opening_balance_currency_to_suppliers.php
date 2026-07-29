<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('opening_balance_currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete()
                ->after('opening_balance_type');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['opening_balance_currency_id']);
            $table->dropColumn('opening_balance_currency_id');
        });
    }
};
