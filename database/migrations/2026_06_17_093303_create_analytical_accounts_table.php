<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytical_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('type', ['cost_center', 'profit_center', 'project', 'department']);
            $table->foreignId('parent_id')->nullable()->constrained('analytical_accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->decimal('budget_amount', 15, 2)->default(0);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytical_accounts');
    }
};
