<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

echo 'TT count: ' . DB::table('treasury_transactions')->count() . "\n";
echo 'BT count: ' . DB::table('bank_transactions')->count() . "\n";
echo 'TT transfer: ' . DB::table('treasury_transactions')->where('type', 'transfer')->count() . "\n";
echo 'BT transfer: ' . DB::table('bank_transactions')->where('type', 'transfer')->count() . "\n";

$t = DB::table('treasury_transactions')->latest()->first();
if ($t) {
    echo 'Latest TT: ' . json_encode($t, JSON_UNESCAPED_UNICODE) . "\n";
}
$t = DB::table('bank_transactions')->latest()->first();
if ($t) {
    echo 'Latest BT: ' . json_encode($t, JSON_UNESCAPED_UNICODE) . "\n";
}

// Check a specific transfer
$tt = DB::table('treasury_transactions')->where('type', 'transfer')->latest()->first();
if ($tt) {
    echo "\nFound TT transfer:\n";
    echo json_encode($tt, JSON_UNESCAPED_UNICODE) . "\n";
    echo 'starts with تحويل صادر: ' . (str_starts_with($tt->description ?? '', 'تحويل صادر') ? 'yes' : 'no') . "\n";
    
    $related = DB::table('treasury_transactions')
        ->where('reference_number', $tt->reference_number)
        ->where('id', '!=', $tt->id)
        ->get();
    echo 'Related TT count: ' . count($related) . "\n";
    
    $relatedBT = DB::table('bank_transactions')
        ->where('reference_number', $tt->reference_number)
        ->get();
    echo 'Related BT count: ' . count($relatedBT) . "\n";
}
