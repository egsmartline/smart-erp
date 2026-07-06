<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

echo "=== treasury_transactions with amount=5000 ===\n";
$tts = DB::table('treasury_transactions')
    ->where('amount', 5000)
    ->orderBy('created_at')
    ->get();
foreach ($tts as $t) {
    echo '  id=' . $t->id . ' treasury_id=' . $t->treasury_id . ' type=' . $t->type . ' amount=' . $t->amount . ' desc=' . $t->description . ' created_at=' . $t->created_at . "\n";
}

echo "\n=== bank_transactions with amount=5000 (transfer type) ===\n";
$bts = DB::table('bank_transactions')
    ->where('amount', 5000)
    ->where('type', 'transfer')
    ->orderBy('created_at')
    ->get();
foreach ($bts as $b) {
    echo '  id=' . $b->id . ' bank_id=' . $b->bank_account_id . ' type=' . $b->type . ' amount=' . $b->amount . ' desc=' . $b->description . ' created_at=' . $b->created_at . "\n";
}

echo "\n=== All bank_transactions (transfer type) ===\n";
$all = DB::table('bank_transactions')
    ->where('type', 'transfer')
    ->orderBy('created_at')
    ->get();
foreach ($all as $b) {
    echo '  id=' . $b->id . ' bank_id=' . $b->bank_account_id . ' type=' . $b->type . ' amount=' . $b->amount . ' desc=' . $b->description . ' created_at=' . $b->created_at . "\n";
}

echo "\n=== Check: what 5000 treasury txns correspond to ===\n";
// Each treasury transfer should have a corresponding bank/account transaction
// Let's check if both 5000 transfers have a counterpart
$transferIds1 = [];
foreach ($tts as $t) {
    $transferIds1[] = $t->id;
}
print_r($transferIds1);
