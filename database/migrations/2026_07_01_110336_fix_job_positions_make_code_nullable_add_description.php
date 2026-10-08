<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_positions', function ($table) {
            $table->string('description')->nullable()->after('name');
            $table->string('code')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('job_positions', function ($table) {
            $table->dropColumn('description');
            $table->string('code')->nullable(false)->change();
        });
    }
};
