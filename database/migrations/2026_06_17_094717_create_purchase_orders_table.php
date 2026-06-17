<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('order_number', 50);
            $table->date('date');
            $table->date('expected_date');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(15);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->foreignId('payment_term_id')->nullable()->constrained('payment_terms');
            $table->enum('status', ['draft', 'confirmed', 'received', 'invoiced', 'cancelled'])->default('draft');
            $table->enum('receipt_status', ['pending', 'partial', 'received'])->default('pending');
            $table->enum('invoice_status', ['not_invoiced', 'partially_invoiced', 'fully_invoiced'])->default('not_invoiced');
            $table->string('supplier_invoice_number', 255)->nullable();
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('reference', 255)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['tenant_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
