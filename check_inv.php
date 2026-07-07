<?php
$base = 'domains/eg-smartline.com/public_html/acc';
require $base . '/vendor/autoload.php';
$app = require $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SalesInvoice;
use App\Models\StockMovement;
use App\Models\ItemWarehouse;

$invoices = SalesInvoice::where('status', 'posted')->orderBy('id')->get();
echo 'Total posted invoices: ' . count($invoices) . "\n";

foreach ($invoices as $inv) {
    $lines = $inv->lines()->get();
    $movements = StockMovement::where('reference_type', SalesInvoice::class)
        ->where('reference_id', $inv->id)->count();
    echo '#' . $inv->id . ' ' . $inv->invoice_number . ' total_lines=' . $lines->count() . ' stock_movements=' . $movements;
    if ($movements == 0) {
        echo ' *** MISSING';
    }
    echo "\n";
    foreach ($lines as $line) {
        $whId = $line->warehouse_id ?? $inv->warehouse_id;
        $iw = ItemWarehouse::where('item_id', $line->item_id)
            ->where('warehouse_id', $whId)->first();
        $available = $iw ? $iw->quantity : 'NO_RECORD';
        echo '  item=' . $line->item_id . ' wh=' . $whId . ' qty=' . $line->quantity . ' available=' . $available . "\n";
    }
}
