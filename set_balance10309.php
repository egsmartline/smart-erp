<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

DB::table('cash_treasuries')->where('id', 1)->update(['current_balance' => 10309.00]);
echo 'OK: 10309' . "\n";
