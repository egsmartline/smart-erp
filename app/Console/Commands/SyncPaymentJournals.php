<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Payment;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPaymentJournals extends Command
{
    protected $signature = 'payments:sync-journals';
    protected $description = 'Create missing journal entries and update account balances from payments';

    public function handle()
    {
        $this->info('Creating missing journal entries for payments...');

        $payments = Payment::whereNotNull('account_id')
            ->where('account_id', '!=', '')
            ->get();

        $bar = $this->output->createProgressBar($payments->count());
        $bar->start();

        $created = 0;
        $skipped = 0;

        foreach ($payments as $payment) {
            $exists = JournalEntry::where('reference', $payment->payment_number)->exists();
            if ($exists) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                DB::beginTransaction();

                $journalService = app(JournalService::class);
                $validated = $payment->toArray();
                $validated['tenant_id'] = $payment->tenant_id;
                $validated['account_id'] = $payment->account_id;
                $validated['type'] = $payment->type;
                $validated['payment_method'] = $payment->payment_method;
                $validated['amount'] = $payment->amount;
                $validated['treasury_id'] = $payment->treasury_id;
                $validated['bank_account_id'] = $payment->bank_account_id;

                $lines = $journalService->buildPaymentLines($validated);
                if (count($lines) >= 2) {
                    $journalService->createEntry([
                        'tenant_id' => $payment->tenant_id,
                        'date' => $payment->date->format('Y-m-d'),
                        'description' => $payment->notes ?? (($payment->type === 'receipt' ? 'قبض' : 'صرف') . ' - ' . $payment->payment_number),
                        'reference' => $payment->payment_number,
                        'type' => $payment->type === 'receipt' ? 'receipt' : 'payment',
                        'lines' => $lines,
                    ]);
                    $created++;
                } else {
                    // Directly update account balance if journal entry not possible
                    $account = Account::find($payment->account_id);
                    if ($account) {
                        $isDebitNature = in_array($account->type, ['asset', 'assets', 'expense', 'expenses']);
                        $amount = (float) $payment->amount;
                        if ($payment->type === 'payment') {
                            $account->increment('current_balance', $isDebitNature ? $amount : -$amount);
                        } else {
                            $account->increment('current_balance', $isDebitNature ? -$amount : $amount);
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed for {$payment->payment_number}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Created: {$created}, Skipped (already exist): {$skipped}");
    }
}
