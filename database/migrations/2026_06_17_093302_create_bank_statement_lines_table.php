<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_statement_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('description');
            $table->enum('payment_type', ['customer_payment', 'supplier_payment', 'bank_charge', 'interest', 'other']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance', 15, 2)->default(0);
            $table->enum('partner_type', ['customer', 'supplier', 'other'])->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->boolean('is_reconciled')->default(false);
            $table->date('reconciled_date')->nullable();
            $table->foreignId('journal_entry_line_id')->nullable()->constrained('journal_entry_lines')->nullOnDelete();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statement_lines');
    }
};
