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
    protected $description = 'Recalculate item stock from stock_movements and opening_stock';

    public function handle()
    {
        $this->info('Recalculating item stock...');

        Item::chunk(100, function ($items) {
            foreach ($items as $item) {
                $warehouses = ItemWarehouse::where('item_id', $item->id)->get();

                foreach ($warehouses as $iw) {
                    if ($iw->created_at->eq($iw->updated_at)) {
                        continue;
                    }

                    $totalIn = (float) StockMovement::where('item_id', $item->id)
                        ->where('warehouse_id', $iw->warehouse_id)
                        ->where('type', 'in')
                        ->sum('quantity');

                    $totalOut = (float) StockMovement::where('item_id', $item->id)
                        ->where('warehouse_id', $iw->warehouse_id)
                        ->where('type', 'out')
                        ->sum('quantity');

                    $correctQty = (float) $item->opening_stock + $totalIn - $totalOut;

                    if ($correctQty < 0) {
                        $correctQty = 0;
                    }

                    DB::table('item_warehouse')
                        ->where('id', $iw->id)
                        ->update(['quantity' => $correctQty]);
                }
            }
        });

        $this->info('Done! Stock quantities have been recalculated.');
    }
}
