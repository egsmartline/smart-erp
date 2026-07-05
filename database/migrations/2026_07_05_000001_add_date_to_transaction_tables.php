<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('treasury_transactions', 'date')) {
            Schema::table('treasury_transactions', function (Blueprint $table) {
                $table->date('date')->nullable()->after('amount');
            });
        }
        if (!Schema::hasColumn('bank_transactions', 'date')) {
            Schema::table('bank_transactions', function (Blueprint $table) {
                $table->date('date')->nullable()->after('amount');
            });
        }
    }

    public function down(): void
    {
        Schema::table('treasury_transactions', function (Blueprint $table) {
            $table->dropColumn('date');
        });
        Schema::table('bank_transactions', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};
