<?php
require '/home/u694451735/domains/eg-smartline.com/public_html/acc/vendor/autoload.php';
$app = require '/home/u694451735/domains/eg-smartline.com/public_html/acc/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use App\Models\BankTransaction;
use App\Models\BankAccount;
use App\Models\TreasuryTransaction;

$p = Payment::find(94);
echo "Payment #{$p->id}: {$p->payment_number}\n";
echo "  type={$p->type} method={$p->payment_method} amount={$p->amount}\n";
echo "  treasury_id={$p->treasury_id} bank_account_id={$p->bank_account_id}\n";
echo "  notes={$p->notes}\n";

$bankTxns = BankTransaction::where('reference_type', 'payment')->where('reference_id', 94)->get();
echo "Bank transactions:\n";
foreach ($bankTxns as $t) {
    echo "  [{$t->id}] type={$t->type} amount={$t->amount}\n";
}
