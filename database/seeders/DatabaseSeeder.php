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
        $this->call([
            TenantSeeder::class,
            AccountSeeder::class,
        ]);

        $tenant = Tenant::first();

        if ($tenant) {
            $this->seedCompanyData($tenant);
        }
    }

    private function seedCompanyData(Tenant $tenant): void
    {
        Company::create([
            'tenant_id' => $tenant->id,
            'name' => $tenant->name,
            'tax_number' => '300000000000003',
            'phone' => '0500000000',
            'email' => 'info@default-company.com',
            'address' => 'القاهرة، مصر',
            'is_active' => true,
        ]);

        Currency::create([
            'tenant_id' => $tenant->id,
            'code' => 'EGP',
            'name' => 'الجنيه المصري',
            'symbol' => 'ج.م',
            'exchange_rate' => 1.00,
            'is_default' => true,
            'is_active' => true,
        ]);

        Currency::create([
            'tenant_id' => $tenant->id,
            'code' => 'USD',
            'name' => 'الدولار الأمريكي',
            'symbol' => '$',
            'exchange_rate' => 50.00,
            'is_default' => false,
            'is_active' => true,
        ]);

        FiscalYear::create([
            'tenant_id' => $tenant->id,
            'name' => date('Y'),
            'start_date' => date('Y-01-01'),
            'end_date' => date('Y-12-31'),
            'is_active' => true,
            'is_closed' => false,
        ]);

        Warehouse::create([
            'tenant_id' => $tenant->id,
            'name' => 'المخزن الرئيسي',
            'code' => 'WH001',
            'address' => 'القاهرة، مصر',
            'is_active' => true,
        ]);

        $defaultCurrency = Currency::where('tenant_id', $tenant->id)->where('code', 'EGP')->first();

        CashTreasury::create([
            'tenant_id' => $tenant->id,
            'name' => 'الخزينة الرئيسية',
            'code' => 'T001',
            'currency_id' => $defaultCurrency->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@smart-erp.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }
}
