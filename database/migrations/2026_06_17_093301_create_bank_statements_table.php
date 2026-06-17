<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->string('statement_number', 50);
            $table->date('date');
            $table->decimal('start_balance', 15, 2)->default(0);
            $table->decimal('end_balance', 15, 2)->default(0);
            $table->decimal('balance_difference', 15, 2)->default(0);
            $table->enum('state', ['draft', 'posted', 'reconciled'])->default('draft');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statements');
    }
};
