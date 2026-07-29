<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Test search directly
$count = \App\Models\Payment::whereHas('customer', function($q) { $q->where('name', 'like', '%أ%'); })->count();
echo "Payments with customer name containing أ: " . $count . "\n";

$count2 = \App\Models\Payment::where('payment_number', 'like', '%1%')->count();
echo "Payments with number containing 1: " . $count2 . "\n";
