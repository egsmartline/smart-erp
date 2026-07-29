<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAccountBalances extends Command
{
    protected $signature = 'accounts:sync-balances {--tenant= : Tenant ID to process}';
    protected $description = 'Recalculate current_balance for all accounts from journal entry lines';

    public function handle()
    {
        $this->info('Syncing account balances from journal entries...');

        $accounts = Account::query()
            ->when($this->option('tenant'), fn($q, $id) => $q->where('tenant_id', $id))
            ->get();

        $bar = $this->output->createProgressBar($accounts->count());
        $bar->start();

        foreach ($accounts as $account) {
            $balance = (float) $account->opening_balance;
            $isDebitNature = in_array($account->type, ['asset', 'assets', 'expense', 'expenses']);

            $lines = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn($q) => $q->where('is_posted', true))
                ->get(['debit', 'credit']);

            foreach ($lines as $line) {
                if ($isDebitNature) {
                    $balance += (float) $line->debit - (float) $line->credit;
                } else {
                    $balance += (float) $line->credit - (float) $line->debit;
                }
            }

            $account->update(['current_balance' => $balance]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done!');
    }
}
