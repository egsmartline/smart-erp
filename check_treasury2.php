<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

$id = 1;
$t = DB::table('cash_treasuries')->find($id);
echo 'current_balance: ' . $t->current_balance . "\n";
echo 'opening_balance: ' . $t->opening_balance . "\n";

$transferIn = DB::table('treasury_transactions')
    ->where('treasury_id', $id)
    ->where('type', 'transfer')
    ->where('description', 'like', '%تحويل وارد%')
    ->sum('amount');
echo 'transfer_incoming: ' . $transferIn . "\n";

$receipts = DB::table('treasury_transactions')
    ->where('treasury_id', $id)
    ->whereIn('type', ['receipt', 'in', 'opening'])
    ->sum('amount');
echo 'receipts_in_opening: ' . $receipts . "\n";

$outgoing = DB::table('treasury_transactions')
    ->where('treasury_id', $id)
    ->where('type', 'out')
    ->sum('amount');
echo 'outgoing: ' . $outgoing . "\n";

$expected = $t->opening_balance + $receipts + $transferIn - $outgoing;
echo 'expected: ' . $expected . "\n";
echo 'user_wants: 10309' . "\n";
echo 'diff from expected: ' . (10309 - $expected) . "\n";

// List transfer transactions
echo "\ntransfer transactions:\n";
$txns = DB::table('treasury_transactions')
    ->where('treasury_id', $id)
    ->where('type', 'transfer')
    ->orderBy('created_at')
    ->get();
foreach ($txns as $tx) {
    echo '  amount=' . $tx->amount . ' desc=' . $tx->description . "\n";
}
