<?php

namespace App\Console\Commands;

use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Historically sales invoices posted their own "sale" stock movement. The
 * delivery-note flow later became the only document that moves stock, and
 * delivery notes were created for those same sales, so the goods were taken
 * out of stock twice.
 *
 * This command pairs each delivery note with the invoice it duplicates (same
 * line items and quantities, closest date) and removes the invoice's duplicate
 * stock movement, returning the quantity to the warehouse balance.
 */
class ReconcileInvoiceStockDuplicates extends Command
{
    protected $signature = 'items:reconcile-invoice-duplicates
        {--apply : Delete the duplicate movements (default is a dry run)}
        {--tenant= : Limit to a single tenant id}';

    protected $description = 'Remove sales-invoice stock movements that duplicate a delivery note for the same sale';

    private const INVOICE_TYPE = 'App\\Models\\SalesInvoice';

    /**
     * An invoice and its duplicate delivery note must be close in date;
     * anything further apart is treated as unrelated so re-runs stay safe.
     */
    private const MAX_PAIR_DAYS = 45;

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $tenantId = $this->option('tenant');

        $this->info($apply ? 'Applying fix...' : 'Dry run (use --apply to write)...');

        $invoices = $this->invoicesWithSaleMovements($tenantId);
        $notes = $this->deliveryNotes($tenantId);

        $removed = 0;
        $units = 0;

        foreach ($notes as $index => $note) {
            $invoice = $this->closestMatchingInvoice($note, $invoices);
            if ($invoice === null) {
                continue;
            }

            $invoices[$invoice['index']]['paired'] = true;
            $notes[$index]['used'] = true;

            $movements = DB::table('stock_movements')
                ->where('reference_type', self::INVOICE_TYPE)
                ->where('reference_id', $invoice['id'])
                ->where('type', 'sale')
                ->whereNull('deleted_at')
                ->get();

            foreach ($movements as $m) {
                $this->line(sprintf(
                    '  %s  ->  %s : item #%d qty %s (wh %d)',
                    $invoice['no'],
                    $note['no'],
                    $m->item_id,
                    $m->quantity,
                    $m->warehouse_id
                ));

                if ($apply) {
                    $iw = ItemWarehouse::firstOrCreate(
                        ['tenant_id' => $m->tenant_id, 'item_id' => $m->item_id, 'warehouse_id' => $m->warehouse_id],
                        ['quantity' => 0, 'reserved_quantity' => 0, 'average_cost' => 0]
                    );
                    $iw->increment('quantity', $m->quantity);

                    StockMovement::where('id', $m->id)->delete();
                }

                $removed++;
                $units += (float) $m->quantity;
            }
        }

        $this->info(($apply ? '' : '[dry-run] ') . "Done: {$removed} movement(s), {$units} unit(s) " . ($apply ? 'removed.' : 'would be removed.'));

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{index:int, id:int, no:string, tenant:int, date:string, sig:string, paired:bool}>
     */
    private function invoicesWithSaleMovements(?int $tenantId): array
    {
        $ids = DB::table('stock_movements')
            ->where('reference_type', self::INVOICE_TYPE)
            ->where('type', 'sale')
            ->whereNull('deleted_at')
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->distinct()
            ->pluck('reference_id');

        $out = [];
        foreach ($ids as $id) {
            $invoice = DB::table('sales_invoices')->where('id', $id)->first(['id', 'invoice_number', 'date', 'tenant_id']);
            if (!$invoice) {
                continue;
            }

            $lines = DB::table('sales_invoice_lines')->where('sales_invoice_id', $id)->get(['item_id', 'quantity']);
            $sig = $this->signature($lines);
            if ($sig === '') {
                continue;
            }

            $out[] = [
                'index' => count($out),
                'id' => (int) $invoice->id,
                'no' => $invoice->invoice_number,
                'tenant' => (int) $invoice->tenant_id,
                'date' => $invoice->date,
                'sig' => $sig,
                'paired' => false,
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array{index:int, id:int, no:string, tenant:int, date:string, sig:string, used:bool}>
     */
    private function deliveryNotes(?int $tenantId): array
    {
        $out = [];
        $notes = DB::table('sales_delivery_notes')
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('date')->orderBy('id')
            ->get(['id', 'delivery_number', 'date', 'tenant_id']);

        foreach ($notes as $note) {
            $lines = DB::table('sales_delivery_note_lines')->where('sales_delivery_note_id', $note->id)->get(['item_id', 'quantity']);
            $sig = $this->signature($lines);
            if ($sig === '') {
                continue;
            }

            $out[] = [
                'index' => count($out),
                'id' => (int) $note->id,
                'no' => $note->delivery_number,
                'tenant' => (int) $note->tenant_id,
                'date' => $note->date,
                'sig' => $sig,
                'used' => false,
            ];
        }

        return $out;
    }

    /**
     * @param  array{tenant:int, date:string, sig:string}  $note
     * @param  array<int, array<string, mixed>>  $invoices
     * @return array<string, mixed>|null
     */
    private function closestMatchingInvoice(array $note, array $invoices): ?array
    {
        $best = null;
        $bestDiff = null;

        foreach ($invoices as $invoice) {
            if ($invoice['paired'] || $invoice['tenant'] !== $note['tenant'] || $invoice['sig'] !== $note['sig']) {
                continue;
            }

            $diff = abs(strtotime($note['date']) - strtotime($invoice['date']));
            if ($diff > self::MAX_PAIR_DAYS * 86400) {
                continue;
            }
            if ($bestDiff === null || $diff < $bestDiff) {
                $bestDiff = $diff;
                $best = $invoice;
            }
        }

        return $best;
    }

    private function signature($lines): string
    {
        $map = [];
        foreach ($lines as $line) {
            $item = (int) $line->item_id;
            $map[$item] = ($map[$item] ?? 0) + (float) $line->quantity;
        }
        if ($map === []) {
            return '';
        }
        ksort($map);

        $parts = [];
        foreach ($map as $item => $qty) {
            $parts[] = $item . ':' . round($qty, 4);
        }

        return implode('|', $parts);
    }
}
