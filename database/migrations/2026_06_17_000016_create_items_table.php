<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('item_units')->onDelete('set null');
            $table->foreignId('sales_account_id')->nullable()->constrained('chart_of_accounts')->onDelete('set null');
            $table->foreignId('purchase_account_id')->nullable()->constrained('chart_of_accounts')->onDelete('set null');
            $table->foreignId('inventory_account_id')->nullable()->constrained('chart_of_accounts')->onDelete('set null');
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('sku', 50)->nullable();
            $table->string('barcode', 50)->nullable();
            $table->string('type', 20)->default('product');
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('min_stock', 15, 2)->default(0);
            $table->decimal('max_stock', 15, 2)->default(0);
            $table->decimal('reorder_level', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->boolean('has_serial')->default(false);
            $table->boolean('has_batch')->default(false);
            $table->boolean('has_expiry')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'sku']);
            $table->unique(['tenant_id', 'barcode']);
            $table->index(['tenant_id', 'category_id']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
