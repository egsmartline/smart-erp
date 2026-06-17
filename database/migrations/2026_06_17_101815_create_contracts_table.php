<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->date('date_from');
            $table->date('date_to')->nullable()->comment('null = open-ended');
            $table->enum('contract_type', ['permanent', 'temporary', 'part_time', 'internship']);
            $table->decimal('gross_salary', 15, 2);
            $table->json('benefits')->nullable();
            $table->json('deductions')->nullable();
            $table->integer('probation_period_days')->default(90);
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
