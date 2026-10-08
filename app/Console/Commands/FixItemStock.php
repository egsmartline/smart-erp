<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;

class FixItemStock extends Command
{
    protected $signature = 'items:fix-stock {--dry-run : Show what would change without writing}';
    protected $description = 'Reset item stock to values derived from the stock movement ledger';

    private const IN_TYPES = ['purchase', 'return_in', 'transfer_in', 'adjustment_in', 'opening'];
    private const OUT_TYPES = ['sale', 'return_out', 'transfer_out', 'adjustment_out'];

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $this->info($dryRun ? 'Fix item stock (dry run)...' : 'Fix item stock...');

        $changed = 0;
        Item::chunk(100, function ($items) use (&$changed, $dryRun) {
            foreach ($items as $item) {
                $warehouses = ItemWarehouse::where('item_id', $item->id)
                    ->where('tenant_id', $item->tenant_id)
                    ->orderBy('id')
                    ->get();

                if ($warehouses->isEmpty()) {
                    continue;
                }

                $firstId = $warehouses->first()->id;
                $opening = (float) $item->opening_stock;

                foreach ($warehouses as $iw) {
                    $totalIn = (float) StockMovement::where('tenant_id', $iw->tenant_id)
                        ->where('item_id', $item->id)
                        ->where('warehouse_id', $iw->warehouse_id)
                        ->whereNull('deleted_at')
                        ->whereIn('type', self::IN_TYPES)
                        ->sum('quantity');

                    $totalOut = (float) StockMovement::where('tenant_id', $iw->tenant_id)
                        ->where('item_id', $item->id)
                        ->where('warehouse_id', $iw->warehouse_id)
                        ->whereNull('deleted_at')
                        ->whereIn('type', self::OUT_TYPES)
                        ->sum('quantity');

                    $correct = $totalIn - $totalOut;
                    if ($iw->id === $firstId) {
                        $correct += $opening;
                    }
                    if ($correct < 0) {
                        $correct = 0;
                    }

                    if ((float) $iw->quantity !== $correct) {
                        $this->line("  {$item->name} (wh {$iw->warehouse_id}): {$iw->quantity} -> {$correct}");
                        if (!$dryRun) {
                            $iw->update(['quantity' => $correct]);
                        }
                        $changed++;
                    }
                }
            }
        });

        $this->info(($dryRun ? '[dry-run] ' : '') . "Done! {$changed} warehouse row(s) " . ($dryRun ? 'would change.' : 'changed.'));
    }
}
