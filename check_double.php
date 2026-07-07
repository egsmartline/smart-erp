<?php
require '/home/u694451735/domains/eg-smartline.com/public_html/acc/vendor/autoload.php';
$app = require '/home/u694451735/domains/eg-smartline.com/public_html/acc/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Item;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use App\Models\SalesInvoice;

echo "=== Stock movements per item ===\n";
$items = Item::all();
foreach ($items as $item) {
    $iw = ItemWarehouse::where('item_id', $item->id)->first();
    $movements = StockMovement::where('item_id', $item->id)->get();
    $totalSale = StockMovement::where('item_id', $item->id)->where('type', 'sale')->sum('quantity');
    echo "{$item->name}: opening={$item->opening_stock} iw_qty={$iw->quantity} total_sale_movements={$totalSale} count=" . $movements->count() . "\n";
    foreach ($movements as $m) {
        $ref = class_basename($m->reference_type) . '#' . $m->reference_id;
        echo "  [{$m->id}] type={$m->type} qty={$m->quantity} ref={$ref}\n";
    }
}

echo "\n=== Invoices ===\n";
$invoices = SalesInvoice::all();
foreach ($invoices as $inv) {
    echo "Invoice #{$inv->id} {$inv->invoice_number} status={$inv->status}\n";
    foreach ($inv->lines as $line) {
        echo "  item={$line->item_id} qty={$line->quantity}\n";
    }
}
