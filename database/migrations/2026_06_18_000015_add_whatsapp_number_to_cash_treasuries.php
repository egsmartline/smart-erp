<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_treasuries', function (Blueprint $table) {
            $table->string('whatsapp_number', 20)->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('cash_treasuries', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
    }
};
