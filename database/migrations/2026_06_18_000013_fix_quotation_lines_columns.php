<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('quotation_lines', 'description')) {
                $table->text('description')->nullable()->after('item_id');
            }
            if (!Schema::hasColumn('quotation_lines', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('tax_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotation_lines', function (Blueprint $table) {
            $table->dropColumn(['description', 'subtotal']);
        });
    }
};
