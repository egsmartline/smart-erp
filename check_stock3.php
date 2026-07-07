<?php
require '/home/u694451735/domains/eg-smartline.com/public_html/acc/vendor/autoload.php';
$app = require '/home/u694451735/domains/eg-smartline.com/public_html/acc/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Item;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;

echo "=== Current stock state ===\n";
$items = Item::all();
foreach ($items as $item) {
    $iw = ItemWarehouse::where('item_id', $item->id)->first();
    $iwQty = $iw ? $iw->quantity : 'NO_RECORD';
    
    $totalIn = (float) StockMovement::where('item_id', $item->id)
        ->whereIn('type', ['in', 'purchase', 'return_in'])->sum('quantity');
    
    $totalOut = (float) StockMovement::where('item_id', $item->id)
        ->whereIn('type', ['out', 'sale', 'return_out'])->sum('quantity');
    
    $correct = (float) $item->opening_stock + $totalIn - $totalOut;
    if ($correct < 0) $correct = 0;
    
    echo "Item #{$item->id} {$item->name}: open={$item->opening_stock} iw={$iwQty} in={$totalIn} out={$totalOut} correct={$correct}\n";
}
