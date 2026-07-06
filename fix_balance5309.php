<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

DB::table('cash_treasuries')->where('id', 1)->update(['current_balance' => 5309.00]);
echo 'Reverted to 5309.00' . "\n";
$t = DB::table('cash_treasuries')->find(1);
echo 'Verified: ' . $t->current_balance . "\n";
