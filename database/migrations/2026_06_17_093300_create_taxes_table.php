<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('code', 20)->unique();
            $table->enum('type', ['fixed', 'percentage', 'group']);
            $table->decimal('rate', 8, 4)->default(0);
            $table->enum('amount_type', ['fixed', 'percent', 'group', 'division'])->default('percent');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_included_in_price')->default(false);
            $table->foreignId('tax_group_id')->nullable()->constrained('taxes')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('purchase_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
