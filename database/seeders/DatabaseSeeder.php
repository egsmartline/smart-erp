<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\Company;
use App\Models\CashTreasury;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Tenant::count() === 0) {
            $this->call(TenantSeeder::class);
        }
        $this->call(AccountSeeder::class);

        $this->call(PermissionSeeder::class);

        $tenant = Tenant::first();

        if ($tenant) {
            $this->seedCompanyData($tenant);
        }
    }

    public function seedCompanyData(Tenant $tenant): void
    {
        Company::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'name' => $tenant->name,
                'tax_number' => '300000000000003',
                'phone' => '0500000000',
                'email' => 'info@default-company.com',
                'address' => 'القاهرة، مصر',
                'is_active' => true,
            ]
        );

        Currency::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'EGP'],
            [
                'name' => 'الجنيه المصري',
                'symbol' => 'ج.م',
                'exchange_rate' => 1.00,
                'is_default' => true,
                'is_active' => true,
            ]
        );

        Currency::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'USD'],
            [
                'name' => 'الدولار الأمريكي',
                'symbol' => '$',
                'exchange_rate' => 1.00,
                'is_default' => false,
                'is_active' => true,
            ]
        );

        FiscalYear::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => date('Y')],
            [
                'start_date' => date('Y-01-01'),
                'end_date' => date('Y-12-31'),
                'is_active' => true,
                'is_closed' => false,
            ]
        );

        Warehouse::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'WH001'],
            [
                'name' => 'المخزن الرئيسي',
                'address' => 'القاهرة، مصر',
                'is_active' => true,
            ]
        );

        $defaultCurrency = Currency::where('tenant_id', $tenant->id)->where('code', 'EGP')->first();

        CashTreasury::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'T001'],
            [
                'name' => 'الخزينة الرئيسية',
                'currency_id' => $defaultCurrency->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@smart-erp.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );
    }
}
