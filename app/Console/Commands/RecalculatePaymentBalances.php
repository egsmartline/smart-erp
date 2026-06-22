<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\CashTreasury;
use App\Models\BankAccount;
use Illuminate\Console\Command;

class RecalculatePaymentBalances extends Command
{
    protected $signature = 'payments:recalculate-balances';
    protected $description = 'Recalculate treasury and bank account balances from all payments';

    public function handle()
    {
        $this->info('Recalculating treasury balances...');

        foreach (CashTreasury::all() as $treasury) {
            $balance = (float) $treasury->opening_balance;

            $receipts = Payment::where('treasury_id', $treasury->id)
                ->where('type', 'receipt')
                ->where('status', 'completed')
                ->sum('amount');

            $payments = Payment::where('treasury_id', $treasury->id)
                ->where('type', 'payment')
                ->where('status', 'completed')
                ->sum('amount');

            $balance += $receipts - $payments;
            $treasury->update(['current_balance' => $balance]);

            $this->info("Treasury {$treasury->name}: {$balance}");
        }

        $this->info('Recalculating bank account balances...');

        foreach (BankAccount::all() as $account) {
            $balance = (float) $account->opening_balance;

            $receipts = Payment::where('bank_account_id', $account->id)
                ->where('type', 'receipt')
                ->where('status', 'completed')
                ->sum('amount');

            $payments = Payment::where('bank_account_id', $account->id)
                ->where('type', 'payment')
                ->where('status', 'completed')
                ->sum('amount');

            $balance += $receipts - $payments;
            $account->update(['current_balance' => $balance]);

            $this->info("Bank {$account->account_name}: {$balance}");
        }

        $this->info('Done!');
    }
}
