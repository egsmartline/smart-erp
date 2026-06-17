<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('converted_order_id')->nullable()->after('converted_invoice_id');
            $table->foreign('converted_order_id')->references('id')->on('sales_orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['converted_order_id']);
            $table->dropColumn('converted_order_id');
        });
    }
};
