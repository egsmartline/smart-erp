<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

$id = 1;
$t = DB::table('cash_treasuries')->find($id);
echo 'الخزينة الرئيسية:' . "\n";
echo '  opening_balance: ' . $t->opening_balance . "\n";
echo '  current_balance: ' . $t->current_balance . "\n";

echo "\nحركات الخزينة:" . "\n";
$txns = DB::table('treasury_transactions')
    ->where('treasury_id', $id)
    ->orderBy('created_at')
    ->get();

$sumIn = 0;
$sumOut = 0;
foreach ($txns as $tx) {
    $isIn = in_array($tx->type, ['receipt', 'in', 'opening']);
    $isOut = $tx->type === 'out';
    $tag = $isIn ? 'داخل' : ($isOut ? 'خارج' : '?');
    echo '  ' . $tx->created_at . ' ' . $tag . ' (' . $tx->type . ') amount=' . $tx->amount . ' desc=' . $tx->description . "\n";
    if ($isIn) $sumIn += $tx->amount;
    if ($isOut) $sumOut += $tx->amount;
}

echo "\n";
echo '  مجموع الداخل: ' . $sumIn . "\n";
echo '  مجموع الخارج: ' . $sumOut . "\n";
echo '  المتوقع (opening + داخل - خارج): ' . ($t->opening_balance + $sumIn - $sumOut) . "\n";
echo '  الحالي: ' . $t->current_balance . "\n";
echo "\nما يدعمه المستخدم: 10309\n";
echo 'الفرق عن المتوقع: ' . (10309 - ($t->opening_balance + $sumIn - $sumOut)) . "\n";
