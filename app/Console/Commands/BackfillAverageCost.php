<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillAverageCost extends Command
{
    protected $signature = 'inventory:backfill-average-cost
        {--dry-run : Report the changes without writing them}';

    protected $description = 'Backfill item_warehouses.average_cost from posted purchase invoices (weighted average), falling back to items.cost_price';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $rows = DB::table('item_warehouses as iw')
            ->join('items as i', 'i.id', '=', 'iw.item_id')
            ->where('iw.average_cost', 0)
            ->where('iw.quantity', '!=', 0)
            ->orderBy('iw.id')
            ->get([
                'iw.id as iw_id', 'iw.item_id', 'iw.warehouse_id', 'iw.tenant_id', 'iw.quantity',
                'i.name as item_name', 'i.sku', 'i.cost_price',
            ]);

        if ($rows->isEmpty()) {
            $this->info('Nothing to backfill.');
            return self::SUCCESS;
        }

        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $value = $this->weightedCost((int) $row->tenant_id, (int) $row->item_id, (int) $row->warehouse_id);
            $source = 'posted purchases';

            if ($value === null && (float) $row->cost_price > 0) {
                $value = (float) $row->cost_price;
                $source = 'items.cost_price';
            }

            if ($value === null) {
                $this->line(sprintf('  SKIP iw=%-4d item=%-4d sku=%-10s qty=%-8s  no usable cost source',
                    $row->iw_id, $row->item_id, $row->sku, $row->quantity));
                $skipped++;
                continue;
            }

            $value = round($value, 2);

            $this->line(sprintf('  %s iw=%-4d item=%-4d sku=%-10s qty=%-8s  0.00 -> %12s  (%s)',
                $dryRun ? 'DRY' : 'SET',
                $row->iw_id, $row->item_id, $row->sku, $row->quantity,
                number_format($value, 2), $source));

            if (!$dryRun) {
                DB::table('item_warehouses')
                    ->where('id', $row->iw_id)
                    ->where('average_cost', 0)
                    ->update(['average_cost' => $value, 'updated_at' => now()]);
            }

            $updated++;
        }

        $this->info(sprintf('%s %d row(s), %d skipped.',
            $dryRun ? 'Would update' : 'Updated', $updated, $skipped));

        return self::SUCCESS;
    }

    /**
     * Weighted average cost for one item/warehouse, from posted purchase
     * invoice lines only. Invoice-level discount and tax are handled by
     * allocating each invoice's net total (total - tax_amount) across its
     * lines in proportion to their line value, so the capitalized cost is
     * the amount actually payable rather than the pre-discount line sum.
     */
    private function weightedCost(int $tenantId, int $itemId, int $warehouseId): ?float
    {
        $lines = DB::table('purchase_invoice_lines as pil')
            ->join('purchase_invoices as pi', 'pi.id', '=', 'pil.purchase_invoice_id')
            ->where('pil.tenant_id', $tenantId)
            ->where('pil.item_id', $itemId)
            ->where('pil.warehouse_id', $warehouseId)
            ->where('pi.tenant_id', $tenantId)
            ->where('pi.status', 'posted')
            ->whereNull('pi.deleted_at')
            ->where('pil.quantity', '>', 0)
            ->get([
                'pi.id as invoice_id', 'pi.subtotal', 'pi.total as invoice_total', 'pi.tax_amount',
                'pil.quantity', 'pil.total as line_total',
            ]);

        if ($lines->isEmpty()) {
            return null;
        }

        $grouped = [];
        foreach ($lines as $line) {
            $grouped[$line->invoice_id][] = $line;
        }

        $totalQty = 0.0;
        $totalValue = 0.0;

        foreach ($grouped as $invoiceLines) {
            $net = (float) $invoiceLines[0]->invoice_total - (float) $invoiceLines[0]->tax_amount;

            $lineSum = 0.0;
            foreach ($invoiceLines as $line) {
                $lineSum += (float) $line->line_total;
            }

            foreach ($invoiceLines as $line) {
                $qty = (float) $line->quantity;
                if ($qty <= 0) {
                    continue;
                }

                $share = $lineSum > 0
                    ? (float) $line->line_total / $lineSum
                    : 1 / count($invoiceLines);

                $totalQty += $qty;
                $totalValue += $net * $share;
            }
        }

        if ($totalQty <= 0 || $totalValue <= 0) {
            return null;
        }

        return $totalValue / $totalQty;
    }
}
