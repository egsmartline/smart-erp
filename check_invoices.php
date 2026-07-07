<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SalesInvoice;
use App\Models\StockMovement;
use App\Models\ItemWarehouse;

$invoices = SalesInvoice::where('status', 'posted')->orderBy('id')->get();
echo 'Total posted invoices: ' . $invoices->count() . PHP_EOL;

foreach ($invoices as $inv) {
    $movements = StockMovement::where('reference_type', SalesInvoice::class)
        ->where('reference_id', $inv->id)->count();
    echo '#' . $inv->id . ' ' . $inv->invoice_number . ' lines=' . $inv->lines()->count() . ' stock_movements=' . $movements;
    if ($movements == 0) {
        echo ' *** NO STOCK MOVEMENTS';
    }
    echo PHP_EOL;
}
