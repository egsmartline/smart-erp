<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class FixItemStock extends Command
{
    protected $signature = 'items:fix-stock';
    protected $description = 'Reset item stock to correct values';

    public function handle()
    {
        $this->info('Fix item stock...');

        $fixed = 0;
        Item::chunk(100, function ($items) use (&$fixed) {
            foreach ($items as $item) {
                $iw = ItemWarehouse::where('item_id', $item->id)->first();
                if (!$iw) continue;

                $totalIn = (float) StockMovement::where('item_id', $item->id)
                    ->where('warehouse_id', $iw->warehouse_id)
                    ->where('type', 'in')->sum('quantity');

                $totalOut = (float) StockMovement::where('item_id', $item->id)
                    ->where('warehouse_id', $iw->warehouse_id)
                    ->where('type', 'out')->sum('quantity');

                $correct = (float) $item->opening_stock + $totalIn - $totalOut;
                if ($correct < 0) $correct = 0;

                if ((float) $iw->quantity !== $correct) {
                    DB::table('item_warehouse')->where('id', $iw->id)->update(['quantity' => $correct]);
                    $this->line("  {$item->name}: {$iw->quantity} -> {$correct}");
                    $fixed++;
                }
            }
        });

        $this->info("Done! {$fixed} items fixed.");
    }
}
