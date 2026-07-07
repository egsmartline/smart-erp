<?php
$base = getenv('HOME') . '/domains/eg-smartline.com/public_html/acc';
require $base . '/vendor/autoload.php';
$app = require $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Item;
use App\Models\ItemWarehouse;

echo "=== Items opening_stock vs ItemWarehouse quantity ===\n";
$items = Item::all();
foreach ($items as $item) {
    $iw = ItemWarehouse::where('item_id', $item->id)->first();
    $iwQty = $iw ? $iw->quantity : 'NO_RECORD';
    echo "Item #{$item->id} {$item->name}: opening_stock={$item->opening_stock} iw_qty={$iwQty}\n";
}
