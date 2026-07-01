<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_positions', function ($table) {
            $table->string('description')->nullable()->after('name');
        });
        DB::statement('ALTER TABLE job_positions MODIFY code VARCHAR(255) NULL');
    }

    public function down(): void
    {
        Schema::table('job_positions', function ($table) {
            $table->dropColumn('description');
        });
        DB::statement('ALTER TABLE job_positions MODIFY code VARCHAR(255) NOT NULL');
    }
};
