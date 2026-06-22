<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\UserPermission;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'accounts' => [
                ['slug' => 'view_accounts', 'name' => 'عرض دليل الحسابات'],
                ['slug' => 'create_accounts', 'name' => 'إضافة حساب'],
                ['slug' => 'edit_accounts', 'name' => 'تعديل حساب'],
                ['slug' => 'delete_accounts', 'name' => 'حذف حساب'],
            ],
            'customers' => [
                ['slug' => 'view_customers', 'name' => 'عرض العملاء'],
                ['slug' => 'create_customers', 'name' => 'إضافة عميل'],
                ['slug' => 'edit_customers', 'name' => 'تعديل عميل'],
                ['slug' => 'delete_customers', 'name' => 'حذف عميل'],
            ],
            'suppliers' => [
                ['slug' => 'view_suppliers', 'name' => 'عرض الموردين'],
                ['slug' => 'create_suppliers', 'name' => 'إضافة مورد'],
                ['slug' => 'edit_suppliers', 'name' => 'تعديل مورد'],
                ['slug' => 'delete_suppliers', 'name' => 'حذف مورد'],
            ],
            'items' => [
                ['slug' => 'view_items', 'name' => 'عرض الأصناف'],
                ['slug' => 'create_items', 'name' => 'إضافة صنف'],
                ['slug' => 'edit_items', 'name' => 'تعديل صنف'],
                ['slug' => 'delete_items', 'name' => 'حذف صنف'],
                ['slug' => 'view_cost_price', 'name' => 'الاطلاع على سعر التكلفة'],
            ],
            'sales_invoices' => [
                ['slug' => 'view_sales_invoices', 'name' => 'عرض فواتير البيع'],
                ['slug' => 'create_sales_invoices', 'name' => 'إضافة فاتورة بيع'],
                ['slug' => 'edit_sales_invoices', 'name' => 'تعديل فاتورة بيع'],
                ['slug' => 'delete_sales_invoices', 'name' => 'حذف فاتورة بيع'],
                ['slug' => 'approve_sales_invoices', 'name' => 'اعتماد فاتورة بيع'],
            ],
            'purchase_invoices' => [
                ['slug' => 'view_purchase_invoices', 'name' => 'عرض فواتير الشراء'],
                ['slug' => 'create_purchase_invoices', 'name' => 'إضافة فاتورة شراء'],
                ['slug' => 'edit_purchase_invoices', 'name' => 'تعديل فاتورة شراء'],
                ['slug' => 'delete_purchase_invoices', 'name' => 'حذف فاتورة شراء'],
                ['slug' => 'approve_purchase_invoices', 'name' => 'اعتماد فاتورة شراء'],
            ],
            'sales_orders' => [
                ['slug' => 'view_sales_orders', 'name' => 'عرض أوامر البيع'],
                ['slug' => 'create_sales_orders', 'name' => 'إضافة أمر بيع'],
                ['slug' => 'edit_sales_orders', 'name' => 'تعديل أمر بيع'],
                ['slug' => 'delete_sales_orders', 'name' => 'حذف أمر بيع'],
            ],
            'purchase_orders' => [
                ['slug' => 'view_purchase_orders', 'name' => 'عرض أوامر الشراء'],
                ['slug' => 'create_purchase_orders', 'name' => 'إضافة أمر شراء'],
                ['slug' => 'edit_purchase_orders', 'name' => 'تعديل أمر شراء'],
                ['slug' => 'delete_purchase_orders', 'name' => 'حذف أمر شراء'],
            ],
            'sales_returns' => [
                ['slug' => 'view_sales_returns', 'name' => 'عرض مرتجعات البيع'],
                ['slug' => 'create_sales_returns', 'name' => 'إضافة مرتجع بيع'],
                ['slug' => 'delete_sales_returns', 'name' => 'حذف مرتجع بيع'],
            ],
            'purchase_returns' => [
                ['slug' => 'view_purchase_returns', 'name' => 'عرض مرتجعات الشراء'],
                ['slug' => 'create_purchase_returns', 'name' => 'إضافة مرتجع شراء'],
                ['slug' => 'delete_purchase_returns', 'name' => 'حذف مرتجع شراء'],
            ],
            'quotations' => [
                ['slug' => 'view_quotations', 'name' => 'عرض عروض الأسعار'],
                ['slug' => 'create_quotations', 'name' => 'إضافة عرض سعر'],
                ['slug' => 'edit_quotations', 'name' => 'تعديل عرض سعر'],
                ['slug' => 'delete_quotations', 'name' => 'حذف عرض سعر'],
                ['slug' => 'convert_quotations', 'name' => 'تحويل عرض سعر'],
            ],
            'delivery_notes' => [
                ['slug' => 'view_delivery_notes', 'name' => 'عرض إذنات التسليم'],
                ['slug' => 'create_delivery_notes', 'name' => 'إضافة إذن تسليم'],
                ['slug' => 'delete_delivery_notes', 'name' => 'حذف إذن تسليم'],
            ],
            'receipt_notes' => [
                ['slug' => 'view_receipt_notes', 'name' => 'عرض إذنات الاستلام'],
                ['slug' => 'create_receipt_notes', 'name' => 'إضافة إذن استلام'],
                ['slug' => 'delete_receipt_notes', 'name' => 'حذف إذن استلام'],
            ],
            'journal_entries' => [
                ['slug' => 'view_journal_entries', 'name' => 'عرض قيود اليومية'],
                ['slug' => 'create_journal_entries', 'name' => 'إضافة قيد يومية'],
                ['slug' => 'edit_journal_entries', 'name' => 'تعديل قيد يومية'],
                ['slug' => 'delete_journal_entries', 'name' => 'حذف قيد يومية'],
                ['slug' => 'approve_journal_entries', 'name' => 'اعتماد قيد يومية'],
            ],
            'payments' => [
                ['slug' => 'view_payments', 'name' => 'عرض المدفوعات'],
                ['slug' => 'create_payments', 'name' => 'إضافة مدفوعات'],
                ['slug' => 'delete_payments', 'name' => 'حذف مدفوعات'],
            ],
            'stock' => [
                ['slug' => 'view_stock_movements', 'name' => 'عرض حركات المخزون'],
                ['slug' => 'view_stock_transfers', 'name' => 'عرض تحويلات المخزون'],
                ['slug' => 'create_stock_transfers', 'name' => 'إضافة تحويل مخزون'],
                ['slug' => 'view_inventory_adjustments', 'name' => 'عرض تسويات المخزون'],
                ['slug' => 'create_inventory_adjustments', 'name' => 'إضافة تسوية مخزون'],
            ],
            'expenses' => [
                ['slug' => 'view_expenses', 'name' => 'عرض المصروفات'],
                ['slug' => 'create_expenses', 'name' => 'إضافة مصروف'],
                ['slug' => 'delete_expenses', 'name' => 'حذف مصروف'],
            ],
            'budgets' => [
                ['slug' => 'view_budgets', 'name' => 'عرض الميزانيات'],
                ['slug' => 'create_budgets', 'name' => 'إضافة ميزانية'],
                ['slug' => 'edit_budgets', 'name' => 'تعديل ميزانية'],
                ['slug' => 'delete_budgets', 'name' => 'حذف ميزانية'],
            ],
            'bank' => [
                ['slug' => 'view_bank_accounts', 'name' => 'عرض الحسابات البنكية'],
                ['slug' => 'view_bank_statements', 'name' => 'عرض كشوفات البنك'],
                ['slug' => 'create_bank_transactions', 'name' => 'إضافة معاملة بنكية'],
            ],
            'treasury' => [
                ['slug' => 'view_treasury', 'name' => 'عرض الخزينة'],
                ['slug' => 'create_treasury_transactions', 'name' => 'إضافة معاملة خزينة'],
            ],
            'employees' => [
                ['slug' => 'view_employees', 'name' => 'عرض الموظفين'],
                ['slug' => 'create_employees', 'name' => 'إضافة موظف'],
                ['slug' => 'edit_employees', 'name' => 'تعديل موظف'],
                ['slug' => 'delete_employees', 'name' => 'حذف موظف'],
            ],
            'payroll' => [
                ['slug' => 'view_payroll', 'name' => 'عرض الرواتب'],
                ['slug' => 'create_payroll', 'name' => 'إضافة راتب'],
                ['slug' => 'approve_payroll', 'name' => 'اعتماد راتب'],
            ],
            'custodies' => [
                ['slug' => 'view_custodies', 'name' => 'عرض العهد'],
                ['slug' => 'create_custodies', 'name' => 'إضافة عهدة'],
                ['slug' => 'delete_custodies', 'name' => 'حذف عهدة'],
            ],
            'reports' => [
                ['slug' => 'view_reports', 'name' => 'عرض التقارير'],
                ['slug' => 'export_reports', 'name' => 'تصدير التقارير'],
            ],
            'settings' => [
                ['slug' => 'view_settings', 'name' => 'عرض الإعدادات'],
                ['slug' => 'edit_settings', 'name' => 'تعديل الإعدادات'],
                ['slug' => 'manage_roles', 'name' => 'إدارة الصلاحيات'],
                ['slug' => 'manage_users', 'name' => 'إدارة المستخدمين'],
            ],
        ];

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $savedPermissions = [];
            foreach ($permissions as $group => $perms) {
                foreach ($perms as $perm) {
                    $savedPermissions[$perm['slug']] = UserPermission::firstOrCreate(
                        ['tenant_id' => $tenant->id, 'slug' => $perm['slug']],
                        [
                            'name' => $perm['name'],
                            'group' => $group,
                        ]
                    );
                }
            }

            $roles = [
                'super_admin' => [
                    'name' => 'مدير النظام',
                    'name_en' => 'Super Admin',
                    'description' => 'صلاحية كاملة على جميع أجزاء النظام',
                    'is_system' => true,
                    'permissions' => array_keys($savedPermissions),
                ],
                'admin' => [
                    'name' => 'مدير',
                    'name_en' => 'Admin',
                    'description' => 'مدير الشركة - صلاحية كاملة',
                    'is_system' => true,
                    'permissions' => array_keys($savedPermissions),
                ],
                'accountant' => [
                    'name' => 'محاسب',
                    'name_en' => 'Accountant',
                    'description' => 'إدارة الحسابات والفواتير والقيود',
                    'is_system' => true,
                    'permissions' => [
                        'view_accounts', 'create_accounts', 'edit_accounts',
                        'view_customers', 'view_suppliers', 'view_cost_price',
                        'view_sales_invoices', 'create_sales_invoices', 'edit_sales_invoices', 'approve_sales_invoices',
                        'view_purchase_invoices', 'create_purchase_invoices', 'edit_purchase_invoices', 'approve_purchase_invoices',
                        'view_journal_entries', 'create_journal_entries', 'approve_journal_entries',
                        'view_payments', 'create_payments',
                        'view_bank_accounts', 'view_bank_statements', 'create_bank_transactions',
                        'view_treasury', 'create_treasury_transactions',
                        'view_expenses', 'create_expenses',
                        'view_budgets', 'create_budgets', 'edit_budgets',
                        'view_reports', 'export_reports',
                        'view_settings',
                    ],
                ],
                'warehouse_keeper' => [
                    'name' => 'أمين مستودع',
                    'name_en' => 'Warehouse Keeper',
                    'description' => 'إدارة المخزون والمستودعات',
                    'is_system' => true,
                    'permissions' => [
                        'view_items', 'create_items', 'edit_items', 'view_cost_price',
                        'view_stock_movements', 'view_stock_transfers', 'create_stock_transfers',
                        'view_inventory_adjustments', 'create_inventory_adjustments',
                        'view_delivery_notes', 'create_delivery_notes',
                        'view_receipt_notes', 'create_receipt_notes',
                        'view_sales_orders', 'view_purchase_orders',
                    ],
                ],
                'sales' => [
                    'name' => 'مندوب مبيعات',
                    'name_en' => 'Sales Representative',
                    'description' => 'إدارة المبيعات والعملاء',
                    'is_system' => true,
                    'permissions' => [
                        'view_customers', 'create_customers', 'edit_customers',
                        'view_items',
                        'view_sales_invoices', 'create_sales_invoices',
                        'view_sales_orders', 'create_sales_orders', 'edit_sales_orders',
                        'view_sales_returns', 'create_sales_returns',
                        'view_quotations', 'create_quotations', 'edit_quotations', 'convert_quotations',
                        'view_delivery_notes', 'create_delivery_notes',
                        'view_reports',
                    ],
                ],
                'purchases' => [
                    'name' => 'مشتريات',
                    'name_en' => 'Purchases',
                    'description' => 'إدارة المشتريات والموردين',
                    'is_system' => true,
                    'permissions' => [
                        'view_suppliers', 'create_suppliers', 'edit_suppliers',
                        'view_items',
                        'view_purchase_invoices', 'create_purchase_invoices',
                        'view_purchase_orders', 'create_purchase_orders', 'edit_purchase_orders',
                        'view_purchase_returns', 'create_purchase_returns',
                        'view_receipt_notes', 'create_receipt_notes',
                        'view_reports',
                    ],
                ],
            ];

            foreach ($roles as $slug => $roleData) {
                $permissionSlugs = $roleData['permissions'];
                unset($roleData['permissions']);

                $role = UserRole::firstOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => $slug],
                    $roleData
                );

                $permIds = [];
                foreach ($permissionSlugs as $pSlug) {
                    if (isset($savedPermissions[$pSlug])) {
                        $permIds[] = $savedPermissions[$pSlug]->id;
                    }
                }

                $role->permissions()->sync($permIds);
            }
        }
    }
}
