<?php

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'cash_treasury_id')) {
                $table->foreignId('cash_treasury_id')->nullable()->after('employee_id')
                    ->constrained('cash_treasuries')->nullOnDelete();
            }
        });

        foreach (Tenant::all() as $tenant) {
            $parent = Account::where('tenant_id', $tenant->id)->where('code', '11')->first();
            if ($parent && !Account::where('tenant_id', $tenant->id)->where('code', '1105')->exists()) {
                Account::create([
                    'tenant_id' => $tenant->id,
                    'code' => '1105',
                    'name' => 'سلف الموظفين',
                    'name_en' => 'Employee Loans',
                    'type' => 'asset',
                    'sub_type' => 'current_assets',
                    'parent_id' => $parent->id,
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'is_active' => true,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['cash_treasury_id']);
            $table->dropColumn('cash_treasury_id');
        });

        Account::where('code', '1105')->delete();
    }
};
