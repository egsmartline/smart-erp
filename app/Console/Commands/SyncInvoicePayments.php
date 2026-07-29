<?php

namespace App\Console\Commands;

use App\Models\SalesInvoice;
use App\Models\Payment;
use Illuminate\Console\Command;

class SyncInvoicePayments extends Command
{
    protected $signature = 'invoices:sync-payments';
    protected $description = 'Sync invoice paid_amount and payment_status from payments table';

    public function handle()
    {
        $this->info('Syncing invoice payments...');

        $invoices = SalesInvoice::whereNull('deleted_at')
            ->where('status', '!=', 'voided')
            ->orderBy('customer_id')
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $grouped = $invoices->groupBy('customer_id');

        foreach ($grouped as $customerId => $customerInvoices) {
            $receipts = Payment::whereNull('deleted_at')
                ->where('type', 'receipt')
                ->where('customer_id', $customerId)
                ->orderBy('date')
                ->orderBy('id')
                ->get();

            $totalReceipts = $receipts->sum('amount');
            $totalInvoiced = $customerInvoices->sum('total');

            if ($totalReceipts <= 0) {
                foreach ($customerInvoices as $inv) {
                    $inv->update(['paid_amount' => 0, 'due_amount' => $inv->total, 'payment_status' => 'unpaid']);
                }
                continue;
            }

            // Reset all invoices to unpaid
            foreach ($customerInvoices as $inv) {
                $inv->update(['paid_amount' => 0, 'due_amount' => $inv->total, 'payment_status' => 'unpaid']);
            }

            // Distribute payments FIFO across invoices
            $remaining = $totalReceipts;
            foreach ($customerInvoices as $inv) {
                if ($remaining <= 0) break;

                $allocated = min($remaining, $inv->total);
                $dueAmount = $inv->total - $allocated;
                $paymentStatus = $dueAmount <= 0 ? 'paid' : 'partial';

                $inv->update([
                    'paid_amount' => $allocated,
                    'due_amount' => $dueAmount,
                    'payment_status' => $paymentStatus,
                ]);

                $remaining -= $allocated;
            }
        }

        $this->info('Done!');
    }
}
