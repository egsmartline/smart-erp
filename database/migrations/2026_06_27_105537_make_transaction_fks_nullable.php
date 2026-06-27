<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treasury_transactions', function (Blueprint $table) {
            $table->dropForeign(['treasury_id']);
            $table->unsignedBigInteger('treasury_id')->nullable()->change();
            $table->foreign('treasury_id')->references('id')->on('cash_treasuries')->onDelete('restrict');

            $table->dropForeign(['target_treasury_id']);
            $table->unsignedBigInteger('target_treasury_id')->nullable()->change();
        });

        Schema::table('bank_transactions', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->unsignedBigInteger('bank_account_id')->nullable()->change();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('restrict');

            $table->dropForeign(['target_bank_account_id']);
            $table->unsignedBigInteger('target_bank_account_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('treasury_transactions', function (Blueprint $table) {
            $table->dropForeign(['treasury_id']);
            $table->unsignedBigInteger('treasury_id')->nullable(false)->change();
            $table->foreign('treasury_id')->references('id')->on('cash_treasuries')->onDelete('restrict');

            $table->unsignedBigInteger('target_treasury_id')->nullable(false)->change();
            $table->foreign('target_treasury_id')->references('id')->on('cash_treasuries')->onDelete('set null');
        });

        Schema::table('bank_transactions', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->unsignedBigInteger('bank_account_id')->nullable(false)->change();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('restrict');

            $table->unsignedBigInteger('target_bank_account_id')->nullable(false)->change();
            $table->foreign('target_bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
        });
    }
};
