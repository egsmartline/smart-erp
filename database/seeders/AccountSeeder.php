<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            if (\App\Models\Account::where('tenant_id', $tenant->id)->exists()) {
                continue;
            }
            $this->createAccountsForTenant($tenant->id);
        }
    }

    public function createAccountsForTenant(int $tenantId): void
    {
        $accounts = [
            ['code' => '1', 'name' => 'أصول', 'name_en' => 'Assets', 'type' => 'asset', 'sub_type' => 'main', 'parent_code' => null],
            ['code' => '11', 'name' => 'أصول متداولة', 'name_en' => 'Current Assets', 'type' => 'asset', 'sub_type' => 'main', 'parent_code' => '1'],
            ['code' => '1101', 'name' => 'نقداً', 'name_en' => 'Cash', 'type' => 'asset', 'sub_type' => 'current_assets', 'parent_code' => '11'],
            ['code' => '1102', 'name' => 'بنوك', 'name_en' => 'Bank', 'type' => 'asset', 'sub_type' => 'current_assets', 'parent_code' => '11'],
            ['code' => '1103', 'name' => 'حسابات المدينة', 'name_en' => 'Accounts Receivable', 'type' => 'asset', 'sub_type' => 'current_assets', 'parent_code' => '11'],
            ['code' => '1104', 'name' => 'مخزون', 'name_en' => 'Inventory', 'type' => 'asset', 'sub_type' => 'current_assets', 'parent_code' => '11'],
            ['code' => '12', 'name' => 'أصول ثابتة', 'name_en' => 'Fixed Assets', 'type' => 'asset', 'sub_type' => 'main', 'parent_code' => '1'],
            ['code' => '1201', 'name' => 'مباني', 'name_en' => 'Buildings', 'type' => 'asset', 'sub_type' => 'fixed_assets', 'parent_code' => '12'],
            ['code' => '1202', 'name' => 'معدات', 'name_en' => 'Equipment', 'type' => 'asset', 'sub_type' => 'fixed_assets', 'parent_code' => '12'],
            ['code' => '1203', 'name' => 'سيارات', 'name_en' => 'Vehicles', 'type' => 'asset', 'sub_type' => 'fixed_assets', 'parent_code' => '12'],

            ['code' => '2', 'name' => 'خصوم', 'name_en' => 'Liabilities', 'type' => 'liability', 'sub_type' => 'main', 'parent_code' => null],
            ['code' => '21', 'name' => 'خصوم متداولة', 'name_en' => 'Current Liabilities', 'type' => 'liability', 'sub_type' => 'main', 'parent_code' => '2'],
            ['code' => '2101', 'name' => 'حسابات الدائن', 'name_en' => 'Accounts Payable', 'type' => 'liability', 'sub_type' => 'current_liabilities', 'parent_code' => '21'],
            ['code' => '2102', 'name' => 'ضريبة مستحقة', 'name_en' => 'Tax Payable', 'type' => 'liability', 'sub_type' => 'current_liabilities', 'parent_code' => '21'],
            ['code' => '2103', 'name' => 'قروض قصيرة', 'name_en' => 'Short-term Loans', 'type' => 'liability', 'sub_type' => 'current_liabilities', 'parent_code' => '21'],
            ['code' => '22', 'name' => 'خصوم طويلة', 'name_en' => 'Long-term Liabilities', 'type' => 'liability', 'sub_type' => 'main', 'parent_code' => '2'],
            ['code' => '2201', 'name' => 'قروض طويلة', 'name_en' => 'Long-term Loans', 'type' => 'liability', 'sub_type' => 'long_term_liabilities', 'parent_code' => '22'],

            ['code' => '3', 'name' => 'حقوق ملكية', 'name_en' => 'Equity', 'type' => 'equity', 'sub_type' => 'main', 'parent_code' => null],
            ['code' => '31', 'name' => 'رأس المال', 'name_en' => 'Capital', 'type' => 'equity', 'sub_type' => 'capital', 'parent_code' => '3'],
            ['code' => '32', 'name' => 'أرباح مبقاة', 'name_en' => 'Retained Earnings', 'type' => 'equity', 'sub_type' => 'retained_earnings', 'parent_code' => '3'],
            ['code' => '33', 'name' => 'ربح السنة', 'name_en' => 'Current Year Profit', 'type' => 'equity', 'sub_type' => 'current_year_profit', 'parent_code' => '3'],

            ['code' => '4', 'name' => 'إيرادات', 'name_en' => 'Revenue', 'type' => 'revenue', 'sub_type' => 'main', 'parent_code' => null],
            ['code' => '41', 'name' => 'إيرادات المبيعات', 'name_en' => 'Sales Revenue', 'type' => 'revenue', 'sub_type' => 'sales_revenue', 'parent_code' => '4'],
            ['code' => '42', 'name' => 'إيرادات الخدمات', 'name_en' => 'Service Revenue', 'type' => 'revenue', 'sub_type' => 'service_revenue', 'parent_code' => '4'],
            ['code' => '43', 'name' => 'إيرادات أخرى', 'name_en' => 'Other Revenue', 'type' => 'revenue', 'sub_type' => 'other_revenue', 'parent_code' => '4'],

            ['code' => '5', 'name' => 'مصروفات', 'name_en' => 'Expenses', 'type' => 'expense', 'sub_type' => 'main', 'parent_code' => null],
            ['code' => '51', 'name' => 'تكلفة البضاعة', 'name_en' => 'Cost of Goods', 'type' => 'expense', 'sub_type' => 'cost_of_goods', 'parent_code' => '5'],
            ['code' => '52', 'name' => 'رواتب', 'name_en' => 'Salaries', 'type' => 'expense', 'sub_type' => 'salaries', 'parent_code' => '5'],
            ['code' => '53', 'name' => 'إيجار', 'name_en' => 'Rent', 'type' => 'expense', 'sub_type' => 'rent', 'parent_code' => '5'],
            ['code' => '54', 'name' => 'مرافق', 'name_en' => 'Utilities', 'type' => 'expense', 'sub_type' => 'utilities', 'parent_code' => '5'],
            ['code' => '55', 'name' => 'إهلاك', 'name_en' => 'Depreciation', 'type' => 'expense', 'sub_type' => 'depreciation', 'parent_code' => '5'],
            ['code' => '56', 'name' => 'مصروفات أخرى', 'name_en' => 'Other Expenses', 'type' => 'expense', 'sub_type' => 'other_expenses', 'parent_code' => '5'],
        ];

        $createdAccounts = [];

        foreach ($accounts as $accountData) {
            $parentCode = $accountData['parent_code'];
            unset($accountData['parent_code']);

            $accountData['tenant_id'] = $tenantId;
            $accountData['opening_balance'] = 0;
            $accountData['is_active'] = true;

            if ($parentCode && isset($createdAccounts[$parentCode])) {
                $accountData['parent_id'] = $createdAccounts[$parentCode]->id;
            }

            $createdAccounts[$accountData['code']] = Account::create($accountData);
        }
    }
}
