<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->integer('days_net')->default(0);
            $table->integer('days_discount')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_terms');
    }
};
