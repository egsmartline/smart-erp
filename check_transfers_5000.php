<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

$trs = DB::table('transfers')
    ->where('amount', 5000)
    ->orderBy('created_at')
    ->get();
echo 'transfers with amount=5000: ' . $trs->count() . "\n";
foreach ($trs as $tr) {
    echo '  id=' . $tr->id . ' from_type=' . $tr->from_type . ' from_id=' . $tr->from_id . ' to_type=' . $tr->to_type . ' to_id=' . $tr->to_id . ' desc=' . $tr->description . ' created_at=' . $tr->created_at . "\n";
}

echo "\nAll transfers:\n";
$all = DB::table('transfers')->orderBy('created_at')->get();
foreach ($all as $tr) {
    echo '  id=' . $tr->id . ' amount=' . $tr->amount . ' from_type=' . $tr->from_type . ' from_id=' . $tr->from_id . ' to_type=' . $tr->to_type . ' to_id=' . $tr->to_id . "\n";
}
