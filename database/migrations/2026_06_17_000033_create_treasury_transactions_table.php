<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treasury_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('treasury_id')->constrained('cash_treasuries')->onDelete('restrict');
            $table->enum('type', ['in', 'out', 'transfer', 'opening']);
            $table->decimal('amount', 15, 2);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('target_treasury_id')->nullable()->constrained('cash_treasuries')->onDelete('set null');
            $table->timestamps();

            $table->index(['tenant_id', 'treasury_id']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'reference_type', 'reference_id'], 'treasury_trans_ref_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treasury_transactions');
    }
};
