<?php
require '/home/u694451735/domains/eg-smartline.com/public_html/acc/vendor/autoload.php';
$app = require '/home/u694451735/domains/eg-smartline.com/public_html/acc/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;

// Check customer #5 (البراق للكرتون المضلع)
$c = Customer::with(['salesInvoices', 'payments'])->find(5);
echo "Customer: {$c->name}\n";
echo "Opening balance: {$c->opening_balance} type={$c->opening_balance_type}\n";
echo "Sales invoices count: " . $c->salesInvoices->count() . "\n";
foreach ($c->salesInvoices as $inv) {
    echo "  INV #{$inv->id} {$inv->invoice_number} date={$inv->date} total={$inv->total}\n";
}
echo "Payments count: " . $c->payments->count() . "\n";
foreach ($c->payments as $pay) {
    echo "  PAY #{$pay->id} {$pay->payment_number} date={$pay->date} amount={$pay->amount} type={$pay->type} method={$pay->payment_method}\n";
}
