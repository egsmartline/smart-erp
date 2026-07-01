<?php
require '/home/u694451735/domains/eg-smartline.com/public_html/acc/vendor/autoload.php';
$app = require_once '/home/u694451735/domains/eg-smartline.com/public_html/acc/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM employees");
echo "Employees table columns:\n";
foreach ($cols as $c) {
    echo "  {$c->Field} | {$c->Type} | Null:{$c->Null} | {$c->Key}\n";
}
