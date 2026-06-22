<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix item_categories: add description and sort_order
        if (!Schema::hasColumn('item_categories', 'description')) {
            Schema::table('item_categories', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
                $table->integer('sort_order')->default(0)->after('is_active');
            });
        }

        // Fix bank_accounts: rename name -> account_name, add missing columns
        if (Schema::hasColumn('bank_accounts', 'name')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->renameColumn('name', 'account_name');
                $table->decimal('current_balance', 15, 2)->default(0)->after('balance');
                $table->boolean('is_default')->default(false)->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bank_accounts', 'account_name')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->renameColumn('account_name', 'name');
                $table->dropColumn(['current_balance', 'is_default']);
            });
        }

        if (Schema::hasColumn('item_categories', 'description')) {
            Schema::table('item_categories', function (Blueprint $table) {
                $table->dropColumn(['description', 'sort_order']);
            });
        }
    }
};
