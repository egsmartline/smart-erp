<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

echo 'DB database: ' . DB::connection()->getDatabaseName() . "\n";

// Check all tables
$tables = DB::select('SHOW TABLES');
echo "\nTables:\n";
foreach ($tables as $t) {
    $vals = array_values((array)$t);
    echo '  ' . $vals[0] . "\n";
}

// Check transfers table
$hasTransfers = Schema::hasTable('transfers');
echo "\ntransfers table: " . ($hasTransfers ? 'YES' : 'NO') . "\n";

// Check treasury_transactions transfer records  
$txnCount = DB::table('treasury_transactions')->where('type', 'transfer')->count();
echo 'treasury_transactions (type=transfer): ' . $txnCount . "\n";
$txnSum = DB::table('treasury_transactions')->where('type', 'transfer')->sum('amount');
echo 'treasury_transactions (type=transfer) sum: ' . $txnSum . "\n";
