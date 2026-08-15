<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_handovers', function (Blueprint $table) {
            $table->string('receiver_id_card', 100)->nullable()->after('receiver_entity');
            $table->string('receiver_id_issuer', 255)->nullable()->after('receiver_id_card');
            $table->string('receiver_address', 255)->nullable()->after('receiver_id_issuer');
        });
    }

    public function down(): void
    {
        Schema::table('document_handovers', function (Blueprint $table) {
            $table->dropColumn(['receiver_id_card', 'receiver_id_issuer', 'receiver_address']);
        });
    }
};