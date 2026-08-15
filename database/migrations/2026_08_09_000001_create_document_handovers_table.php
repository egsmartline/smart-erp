<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('handover_number', 50);
            $table->date('date');
            $table->string('receiver_name', 255)->nullable();
            $table->string('receiver_role', 255)->nullable();
            $table->string('receiver_entity', 255)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('shipment_ref', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'handover_number']);
            $table->index(['tenant_id', 'date']);
        });

        Schema::create('document_handover_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('handover_id')->constrained('document_handovers')->onDelete('cascade');
            $table->string('document_name', 255);
            $table->string('document_number', 255)->nullable();
            $table->integer('copies')->default(1);
            $table->string('notes', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_handover_items');
        Schema::dropIfExists('document_handovers');
    }
};