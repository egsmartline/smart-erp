<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('entry_number', 50);
            $table->date('date');
            $table->text('description');
            $table->string('reference')->nullable();
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('restrict');
            $table->boolean('is_posted')->default(false);
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('posted_at')->nullable();
            $table->boolean('is_adjusting')->default(false);
            $table->string('type')->default('general');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'entry_number']);
            $table->index(['tenant_id', 'date']);
            $table->index(['tenant_id', 'fiscal_year_id']);
            $table->index(['tenant_id', 'is_posted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
