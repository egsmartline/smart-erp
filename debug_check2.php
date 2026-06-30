<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['tenants', 'companies', 'journal_entries', 'journal_entry_lines', 'chart_of_accounts', 'sales_invoices', 'purchase_invoices'];
foreach ($tables as $t) {
    $total = DB::table($t)->count();
    $ids = DB::table($t)->select('tenant_id')->distinct()->pluck('tenant_id');
    echo $t . ': ' . $total . ' rows, tenant_ids=' . json_encode($ids) . PHP_EOL;
}
