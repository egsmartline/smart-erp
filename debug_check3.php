<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = [
    ['name' => 'tenants', 'id_column' => 'id'],
    ['name' => 'companies', 'id_column' => 'tenant_id'],
    ['name' => 'journal_entries', 'id_column' => 'tenant_id'],
    ['name' => 'journal_entry_lines', 'id_column' => 'tenant_id'],
    ['name' => 'chart_of_accounts', 'id_column' => 'tenant_id'],
    ['name' => 'sales_invoices', 'id_column' => 'tenant_id'],
    ['name' => 'purchase_invoices', 'id_column' => 'tenant_id'],
];
foreach ($tables as $t) {
    $total = DB::table($t['name'])->count();
    $ids = DB::table($t['name'])->select($t['id_column'])->distinct()->pluck($t['id_column']);
    echo $t['name'] . ': ' . $total . ' rows, ' . $t['id_column'] . 's=' . json_encode($ids) . PHP_EOL;
}
