<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_handover_items', function (Blueprint $table) {
            $table->dropColumn('document_number');
            $table->boolean('selected')->default(true)->after('handover_id');
            $table->integer('copies')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('document_handover_items', function (Blueprint $table) {
            $table->string('document_number', 255)->nullable()->after('document_name');
            $table->dropColumn('selected');
            $table->integer('copies')->default(1)->change();
        });
    }
};