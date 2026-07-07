<?php
require '/home/u694451735/domains/eg-smartline.com/public_html/acc/vendor/autoload.php';
$app = require '/home/u694451735/domains/eg-smartline.com/public_html/acc/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SalesInvoice;
use App\Models\Customer;

echo "=== العملاء ===\n";
foreach (Customer::all() as $c) {
    echo "#{$c->id} {$c->name}\n";
}

echo "\n=== فواتير المبيعات ===\n";
foreach (SalesInvoice::with('customer')->get() as $inv) {
    echo "#{$inv->id} {$inv->invoice_number} customer_id={$inv->customer_id} customer_name=" . ($inv->customer->name ?? 'NULL') . " total={$inv->total} status={$inv->status}\n";
}
