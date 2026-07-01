<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function ($table) {
            $table->string('nationality')->nullable()->after('gender');
            $table->string('contract_type')->default('full_time')->after('hire_date');
            $table->string('bank_iban')->nullable()->after('bank_account');
            $table->string('emergency_contact_name')->nullable()->after('city');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function ($table) {
            $table->dropColumn(['nationality', 'contract_type', 'bank_iban', 'emergency_contact_name', 'emergency_contact_phone']);
        });
    }
};
