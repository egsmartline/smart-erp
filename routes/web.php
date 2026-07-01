<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemUnitController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CashTreasuryController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\BankStatementController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\FiscalYearController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PaymentTermController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\InventoryCountController;
use App\Http\Controllers\AnalyticalAccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\InventoryAdjustmentController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\ReorderingRuleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\JobPositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\CurrencySwitcherController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\SalesDeliveryNoteController;
use App\Http\Controllers\PurchaseReceiptNoteController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/select-company', [AuthController::class, 'showSelectCompany'])->middleware('auth')->name('select-company');
Route::post('/switch-company/{companyId}', [AuthController::class, 'switchCompany'])->middleware('auth')->name('switch-company');
Route::get('/manage-companies', [CompanyController::class, 'manage'])->middleware('auth')->name('companies.manage');

Route::get('/health/db', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['status' => 'connected']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'disconnected', 'error' => $e->getMessage()], 500);
    }
})->name('health.db');

Route::get('/', function () {
    return redirect()->route('select-company');
})->middleware('auth')->name('home');

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('switch-tenant/{tenantId}', [DashboardController::class, 'switchTenant'])->name('switch-tenant');

    Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
    Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');

    Route::resource('accounts', AccountController::class);
    Route::post('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');

    Route::resource('journal-entries', JournalEntryController::class);
    Route::post('journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post');

    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('items', ItemController::class);
    Route::resource('item-categories', ItemCategoryController::class);
    Route::resource('item-units', ItemUnitController::class);
    Route::resource('warehouses', WarehouseController::class);

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('purchase-orders/{purchaseOrder}/confirm', [PurchaseOrderController::class, 'confirm'])->name('purchase-orders.confirm');
    Route::post('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::post('purchase-orders/{purchaseOrder}/invoice', [PurchaseOrderController::class, 'invoice'])->name('purchase-orders.invoice');
    Route::post('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');

    // Sales Invoices
    Route::resource('sales-invoices', SalesInvoiceController::class);
    Route::post('sales-invoices/{salesInvoice}/post', [SalesInvoiceController::class, 'post'])->name('sales-invoices.post');
    Route::post('sales-invoices/{salesInvoice}/void', [SalesInvoiceController::class, 'void'])->name('sales-invoices.void');

    // Purchase Invoices
    Route::resource('purchase-invoices', PurchaseInvoiceController::class);
    Route::post('purchase-invoices/{purchaseInvoice}/post', [PurchaseInvoiceController::class, 'post'])->name('purchase-invoices.post');
    Route::post('purchase-invoices/{purchaseInvoice}/void', [PurchaseInvoiceController::class, 'void'])->name('purchase-invoices.void');

    // Sales Returns
    Route::resource('sales-returns', SalesReturnController::class);
    Route::post('sales-returns/{salesReturn}/post', [SalesReturnController::class, 'post'])->name('sales-returns.post');

    // Purchase Returns
    Route::resource('purchase-returns', PurchaseReturnController::class);
    Route::post('purchase-returns/{purchaseReturn}/post', [PurchaseReturnController::class, 'post'])->name('purchase-returns.post');

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
    Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
    Route::post('quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
    Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');

    // API endpoints for Livewire
    Route::get('/api/customers/search', [SalesInvoiceController::class, 'searchCustomers'])->name('api.customers.search');
    Route::get('/api/suppliers/search', [PurchaseInvoiceController::class, 'searchSuppliers'])->name('api.suppliers.search');
    Route::get('/api/items/search', [SalesInvoiceController::class, 'searchItems'])->name('api.items.search');

    Route::resource('payments', PaymentController::class);
    Route::get('cash-treasuries/balances', [CashTreasuryController::class, 'balances'])->name('cash-treasuries.balances');
    Route::resource('cash-treasuries', CashTreasuryController::class);
    Route::resource('bank-accounts', BankAccountController::class);
    Route::get('transfers', [\App\Http\Controllers\TransferController::class, 'index'])->name('transfers.index');
    Route::get('transfers/create', [\App\Http\Controllers\TransferController::class, 'create'])->name('transfers.create');
    Route::post('transfers', [\App\Http\Controllers\TransferController::class, 'store'])->name('transfers.store');
    Route::get('transfers/{id}/edit', [\App\Http\Controllers\TransferController::class, 'edit'])->name('transfers.edit');
    Route::put('transfers/{id}', [\App\Http\Controllers\TransferController::class, 'update'])->name('transfers.update');
    Route::delete('transfers/{id}', [\App\Http\Controllers\TransferController::class, 'destroy'])->name('transfers.destroy');

    Route::get('/reports/trial-balance', [ReportController::class, 'trialBalance'])->name('reports.trial-balance');
    Route::get('/reports/general-ledger', [ReportController::class, 'generalLedger'])->name('reports.general-ledger');
    Route::get('/reports/income-statement', [ReportController::class, 'incomeStatement'])->name('reports.income-statement');
    Route::get('/reports/balance-sheet', [ReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
    Route::get('/reports/customer-statement', [ReportController::class, 'customerStatement'])->name('reports.customer-statement');
    Route::get('/reports/supplier-statement', [ReportController::class, 'supplierStatement'])->name('reports.supplier-statement');
    Route::get('/reports/vat', [ReportController::class, 'vatReport'])->name('reports.vat');
    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/purchases', [ReportController::class, 'purchasesReport'])->name('reports.purchases');
    Route::get('/reports/inventory', [ReportController::class, 'inventoryReport'])->name('reports.inventory');
    Route::get('/reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
    Route::get('/reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('/account-statement', [ReportController::class, 'accountStatement'])->name('reports.account-statement');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/logo', [SettingController::class, 'updateLogo'])->name('settings.update-logo');
    Route::post('/settings/reset', [SettingController::class, 'reset'])->name('settings.reset');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::post('roles/assign', [RoleController::class, 'assignRole'])->name('roles.assign');
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'create'])->name('backups.create');
    Route::post('/backups/upload', [BackupController::class, 'upload'])->name('backups.upload');
    Route::get('/backups/{backupLog}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/{backupLog}/restore', [BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('/backups/{backupLog}', [BackupController::class, 'destroy'])->name('backups.destroy');

    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    Route::resource('taxes', TaxController::class);
    Route::resource('bank-statements', BankStatementController::class);
    Route::post('bank-statements/{bankStatement}/post', [BankStatementController::class, 'post'])->name('bank-statements.post');

    Route::resource('currencies', CurrencyController::class);
    Route::resource('fiscal-years', FiscalYearController::class);
    Route::resource('journals', JournalController::class);
    Route::resource('payment-terms', PaymentTermController::class);

    // Stock Movements
    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');

    // Inventory Count
    Route::get('inventory-count', [InventoryCountController::class, 'index'])->name('inventory-count.index');

    // Analytical Accounts
    Route::resource('analytical-accounts', AnalyticalAccountController::class);

    // Budgets
    Route::resource('budgets', BudgetController::class);
    Route::post('budgets/{budget}/confirm', [BudgetController::class, 'confirm'])->name('budgets.confirm');
    Route::post('budgets/{budget}/cancel', [BudgetController::class, 'cancel'])->name('budgets.cancel');

    // Inventory Adjustments
    Route::resource('inventory-adjustments', InventoryAdjustmentController::class);
    Route::post('inventory-adjustments/{adj}/confirm', [InventoryAdjustmentController::class, 'confirm'])->name('inventory-adjustments.confirm');
    Route::post('inventory-adjustments/{adj}/cancel', [InventoryAdjustmentController::class, 'cancel'])->name('inventory-adjustments.cancel');

    // Stock Transfers
    Route::resource('stock-transfers', StockTransferController::class);
    Route::post('stock-transfers/{transfer}/confirm', [StockTransferController::class, 'confirm'])->name('stock-transfers.confirm');
    Route::post('stock-transfers/{transfer}/done', [StockTransferController::class, 'done'])->name('stock-transfers.done');
    Route::post('stock-transfers/{transfer}/cancel', [StockTransferController::class, 'cancel'])->name('stock-transfers.cancel');

    // Reordering Rules
    Route::resource('reordering-rules', ReorderingRuleController::class)->except(['show', 'edit', 'update']);

    // HR - Employees
    Route::resource('employees', EmployeeController::class);

    // HR - Departments
    Route::resource('departments', DepartmentController::class);

    // HR - Job Positions
    Route::resource('job-positions', JobPositionController::class);

    // HR - Attendance
    Route::resource('attendance', AttendanceController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('attendance/{att}/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');

    // HR - Leaves
    Route::resource('leaves', LeaveController::class)->only(['index', 'create', 'store']);
    Route::post('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

    // HR - Expenses
    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');

    // HR - Payroll
    Route::resource('payroll', PayrollController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::post('payroll/{payroll}/confirm', [PayrollController::class, 'confirm'])->name('payroll.confirm');

    // HR - Loans
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // HR - Custodies
    Route::get('custodies/{custody}/settle', [\App\Http\Controllers\CustodyController::class, 'settle'])->name('custodies.settle');
    Route::post('custodies/{custody}/settle', [\App\Http\Controllers\CustodyController::class, 'processSettlement'])->name('custodies.process-settlement');
    Route::resource('custodies', \App\Http\Controllers\CustodyController::class);

    // Currency Switcher
    Route::get('currency/switch/{currency}', [CurrencySwitcherController::class, 'switch'])->name('currency.switch');

    // Sales Delivery Notes
    Route::resource('sales-delivery-notes', SalesDeliveryNoteController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Purchase Receipt Notes
    Route::resource('purchase-receipt-notes', PurchaseReceiptNoteController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // API: Get purchase order lines for receipt note
    Route::get('api/purchase-orders/{purchaseOrder}/lines', function (\App\Models\PurchaseOrder $purchaseOrder) {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        if ($purchaseOrder->tenant_id !== $tenantId) {
            abort(403);
        }
        return response()->json($purchaseOrder->load('lines.item', 'supplier', 'warehouse'));
    })->name('api.purchase-orders.lines');

    // Companies
    Route::resource('companies', CompanyController::class);

    // Import/Export
    Route::get('import', [ImportController::class, 'index'])->name('import.index');
    Route::post('import', [ImportController::class, 'import'])->name('import.do');
    Route::get('export/{type}', [ImportController::class, 'export'])->name('import.export');

    // Trade Operations
    Route::resource('trade', TradeController::class);
    Route::post('trade/{tradeOperation}/status', [TradeController::class, 'updateStatus'])->name('trade.update-status');

    // PDF
    Route::get('pdf/sales-invoice/{invoice}', [PdfController::class, 'salesInvoice'])->name('pdf.sales-invoice');
    Route::get('pdf/purchase-invoice/{invoice}', [PdfController::class, 'purchaseInvoice'])->name('pdf.purchase-invoice');
    Route::get('pdf/purchase-order/{order}', [PdfController::class, 'purchaseOrder'])->name('pdf.purchase-order');
    Route::get('pdf/quotation/{quotation}', [PdfController::class, 'quotation'])->name('pdf.quotation');
});
