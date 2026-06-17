<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('order_number', 50);
            $table->date('date');
            $table->date('required_date')->nullable();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(15);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->foreignId('currency_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->foreignId('payment_term_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['draft', 'confirmed', 'delivered', 'invoiced', 'cancelled'])->default('draft');
            $table->enum('delivery_status', ['pending', 'partial', 'done'])->default('pending');
            $table->enum('invoice_status', ['not_invoiced', 'partial', 'invoiced'])->default('not_invoiced');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('customer_reference', 100)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'order_number']);
            $table->index(['tenant_id', 'date']);
            $table->index(['tenant_id', 'customer_id']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
