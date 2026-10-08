<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('date');
        });

        $rows = DB::table('sales_invoices')->whereNull('due_date')->get(['id', 'date']);

        foreach ($rows as $row) {
            DB::table('sales_invoices')->where('id', $row->id)->update([
                'due_date' => date('Y-m-d', strtotime($row->date . ' +30 days')),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
