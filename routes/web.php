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
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CashTreasuryController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\FiscalYearController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\InventoryCountController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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
    Route::resource('cash-treasuries', CashTreasuryController::class);
    Route::resource('bank-accounts', BankAccountController::class);

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

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'create'])->name('backups.create');
    Route::post('/backups/{backupLog}/restore', [BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('/backups/{backupLog}', [BackupController::class, 'destroy'])->name('backups.destroy');

    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    Route::resource('currencies', CurrencyController::class);
    Route::resource('fiscal-years', FiscalYearController::class);

    // Stock Movements
    Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');

    // Inventory Count
    Route::get('inventory-count', [InventoryCountController::class, 'index'])->name('inventory-count.index');
});
