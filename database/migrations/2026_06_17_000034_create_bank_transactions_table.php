<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->constrained()->onDelete('restrict');
            $table->enum('type', ['in', 'out', 'transfer', 'opening']);
            $table->decimal('amount', 15, 2);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->string('reference_number')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('target_bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('set null');
            $table->timestamps();

            $table->index(['tenant_id', 'bank_account_id']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'reference_type', 'reference_id'], 'bank_trans_ref_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
