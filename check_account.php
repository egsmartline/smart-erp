<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$account = App\Models\Account::where('name', 'like', '%بنزين%')->first();
echo "Account: {$account->name} (ID: {$account->id}, Type: {$account->type}, Balance: {$account->current_balance})\n\n";

$payments = App\Models\Payment::where('account_id', $account->id)->get();
foreach ($payments as $p) {
    $treasury = $p->treasury_id ? App\Models\CashTreasury::find($p->treasury_id) : null;
    $treasuryAccId = $treasury ? $treasury->account_id : 'N/A';
    $je = App\Models\JournalEntry::where('reference', $p->payment_number)->first();
    echo "{$p->payment_number}: amount={$p->amount} treasury_id={$p->treasury_id} treasury_account_id={$treasuryAccId} has_je=" . ($je ? 'yes' : 'no') . "\n";
}

echo "\nAll accounts with payments count:\n";
$accounts = App\Models\Account::where('tenant_id', 12)->get();
foreach ($accounts as $a) {
    $cnt = App\Models\Payment::where('account_id', $a->id)->count();
    $jel = App\Models\JournalEntryLine::where('account_id', $a->id)->count();
    if ($cnt > 0 || $jel > 0) {
        echo "{$a->code} {$a->name}: payments={$cnt} je_lines={$jel} balance={$a->current_balance}\n";
    }
}
