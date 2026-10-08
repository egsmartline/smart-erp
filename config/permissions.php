<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Explicit route-name => permission slug
    |--------------------------------------------------------------------------
    | Keys ending in "." are treated as prefixes and match any route below them.
    | Exact matches win over prefix matches.
    */
    'routes' => [
        'settings.index' => 'view_settings',
        'settings.update' => 'edit_settings',
        'settings.update-logo' => 'edit_settings',
        'settings.reset' => 'edit_settings',

        'settings.roles.' => 'manage_roles',
        'settings.users.' => 'manage_users',

        'backups.' => 'edit_settings',
        'companies.' => 'manage_users',
        'import.index' => 'edit_settings',
        'import.do' => 'edit_settings',
        'import.export' => 'export_reports',
        'audit-log.' => 'manage_users',
        'setup.' => 'manage_users',

        'fiscal-years.close' => 'edit_settings',
        'fiscal-years.reopen' => 'edit_settings',

        'reports.customer-statement.export' => 'export_reports',
        'reports.' => 'view_reports',
        'account-statement' => 'view_reports',
        'customers.balance-report' => 'view_reports',
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource route-name prefix => permission base
    |--------------------------------------------------------------------------
    | The HTTP verb (see "verbs") is appended as "{action}_{base}".
    | Resources without an entry here are not permission-checked.
    */
    'resources' => [
        'accounts' => 'accounts',
        'journal-entries' => 'journal_entries',
        'journals' => 'journal_entries',
        'customers' => 'customers',
        'suppliers' => 'suppliers',
        'items' => 'items',
        'purchase-orders' => 'purchase_orders',
        'sales-invoices' => 'sales_invoices',
        'purchase-invoices' => 'purchase_invoices',
        'sales-returns' => 'sales_returns',
        'purchase-returns' => 'purchase_returns',
        'discount-notes' => 'discount_notes',
        'quotations' => 'quotations',
        'document-archives' => 'document_archives',
        'payments' => 'payments',
        'cash-treasuries' => 'treasury',
        'bank-accounts' => 'bank_accounts',
        'bank-statements' => 'bank_statements',
        'budgets' => 'budgets',
        'inventory-adjustments' => 'inventory_adjustments',
        'stock-transfers' => 'stock_transfers',
        'employees' => 'employees',
        'expenses' => 'expenses',
        'payroll' => 'payroll',
        'custodies' => 'custodies',
        'sales-delivery-notes' => 'delivery_notes',
        'purchase-receipt-notes' => 'receipt_notes',
        'stock-movements' => 'stock_movements',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route verb => permission action
    |--------------------------------------------------------------------------
    */
    'verbs' => [
        'index' => 'view',
        'show' => 'view',
        'create' => 'create',
        'store' => 'create',
        'edit' => 'edit',
        'update' => 'edit',
        'destroy' => 'delete',

        'post' => 'approve',
        'void' => 'edit',
        'confirm' => 'approve',
        'cancel' => 'edit',
        'close' => 'edit',
        'reopen' => 'edit',
        'approve' => 'approve',
        'reject' => 'edit',
        'receive' => 'edit',
        'invoice' => 'create',
        'done' => 'edit',
        'convert' => 'convert',
        'send' => 'edit',
        'accept' => 'edit',
        'settle-installment' => 'edit',
    ],

];
