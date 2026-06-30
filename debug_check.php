<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenantId = session('current_tenant_id') ?? 0;
echo 'session current_tenant_id: ' . json_encode(session('current_tenant_id')) . PHP_EOL;
echo 'tenantId: ' . $tenantId . PHP_EOL;
echo PHP_EOL;

$tables = ['journal_entries', 'journal_entry_lines', 'chart_of_accounts'];
foreach ($tables as $t) {
    $count = DB::table($t)->where('tenant_id', $tenantId)->count();
    $total = DB::table($t)->count();
    echo $t . ': ' . $count . ' (tenant=' . $tenantId . '), total=' . $total . PHP_EOL;
}
echo PHP_EOL;

// Check all unique tenant_ids in journal_entries
$ids = DB::table('journal_entries')->distinct()->pluck('tenant_id');
echo 'Distinct tenant_ids in journal_entries: ' . json_encode($ids) . PHP_EOL;
