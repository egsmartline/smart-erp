<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('payment_number', 50);
            $table->date('date');
            $table->enum('type', ['receipt', 'payment']);
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('treasury_id')->nullable()->constrained('cash_treasuries')->onDelete('restrict');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'card', 'online']);
            $table->foreignId('currency_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->decimal('amount_in_currency', 15, 2)->nullable();
            $table->string('reference')->nullable();
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'completed', 'voided'])->default('draft');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'payment_number']);
            $table->index(['tenant_id', 'date']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'customer_id']);
            $table->index(['tenant_id', 'supplier_id']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
