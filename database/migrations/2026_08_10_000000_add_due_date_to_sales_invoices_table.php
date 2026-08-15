<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('date');
        });

        DB::table('sales_invoices')
            ->whereNull('due_date')
            ->update(['due_date' => DB::raw('DATE_ADD(`date`, INTERVAL 30 DAY)')]);
    }

    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};