<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('secondary_currency_id')->nullable()->after('currency_code')->constrained('currencies')->nullOnDelete();
            $table->string('secondary_currency_code', 10)->nullable()->default('USD');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['secondary_currency_id']);
            $table->dropColumn(['secondary_currency_id', 'secondary_currency_code']);
        });
    }
};
