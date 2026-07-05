<?php

namespace App\Console\Commands;

use App\Models\CashTreasury;
use App\Models\BankAccount;
use App\Models\TreasuryTransaction;
use App\Models\BankTransaction;
use Illuminate\Console\Command;

class FixTransferBalances extends Command
{
    protected $signature = 'transfers:fix-balances';
    protected $description = 'Fix cash treasury and bank account balances after transfers were deleted incorrectly';

    public function handle()
    {
        $this->info('Fixing cash treasury balances...');

        foreach (CashTreasury::all() as $treasury) {
            $balance = (float) $treasury->opening_balance;

            $incoming = (float) TreasuryTransaction::where('treasury_id', $treasury->id)
                ->whereIn('type', ['receipt', 'in', 'opening'])
                ->sum('amount');

            $outgoing = (float) TreasuryTransaction::where('treasury_id', $treasury->id)
                ->where('type', 'out')
                ->sum('amount');

            $balance += $incoming - $outgoing;

            if ($treasury->current_balance != $balance) {
                $oldBalance = $treasury->current_balance;
                $treasury->update(['current_balance' => $balance]);
                $this->info("{$treasury->name}: {$oldBalance} -> {$balance}");
            } else {
                $this->info("{$treasury->name}: {$balance} (already correct)");
            }
        }

        $this->info('Fixing bank account balances...');

        foreach (BankAccount::all() as $account) {
            $balance = (float) $account->opening_balance;

            $incoming = (float) BankTransaction::where('bank_account_id', $account->id)
                ->whereIn('type', ['receipt', 'in', 'opening'])
                ->sum('amount');

            $outgoing = (float) BankTransaction::where('bank_account_id', $account->id)
                ->where('type', 'out')
                ->sum('amount');

            $balance += $incoming - $outgoing;

            if ($account->current_balance != $balance) {
                $oldBalance = $account->current_balance;
                $account->update(['current_balance' => $balance]);
                $this->info("{$account->account_name}: {$oldBalance} -> {$balance}");
            } else {
                $this->info("{$account->account_name}: {$balance} (already correct)");
            }
        }

        $this->info('Done!');
    }
}
