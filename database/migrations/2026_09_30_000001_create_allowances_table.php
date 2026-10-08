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
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('allowance_number')->unique();
            $table->enum('type', ['housing', 'transport', 'communication', 'travel']);
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->enum('payment_method', ['treasury', 'bank', 'payroll'])->default('treasury');
            $table->foreignId('cash_treasury_id')->nullable()->constrained('cash_treasuries')->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->enum('status', ['pending', 'paid', 'in_payroll', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
            $table->index(['tenant_id', 'type']);
        });

        foreach (Tenant::all() as $tenant) {
            $parent = Account::where('tenant_id', $tenant->id)->where('code', '5')->first();
            if ($parent && !Account::where('tenant_id', $tenant->id)->where('code', '57')->exists()) {
                Account::create([
                    'tenant_id' => $tenant->id,
                    'code' => '57',
                    'name' => 'بدلات',
                    'name_en' => 'Allowances',
                    'type' => 'expense',
                    'sub_type' => 'other_expenses',
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
        Schema::dropIfExists('allowances');
        Account::where('code', '57')->where('type', 'expense')->delete();
    }
};
