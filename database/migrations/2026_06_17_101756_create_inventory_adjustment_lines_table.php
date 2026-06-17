<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_adjustment_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('inventory_adjustment_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('theoretical_qty', 15, 2)->default(0);
            $table->decimal('actual_qty', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('inventory_adjustment_id')->references('id')->on('inventory_adjustments')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->index(['tenant_id', 'inventory_adjustment_id'], 'ial_tenant_adj_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustment_lines');
    }
};
