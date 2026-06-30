<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['tenants', 'companies', 'users', 'tenant_user'];
foreach ($tables as $t) {
    $total = DB::table($t)->count();
    echo $t . ': ' . $total . ' rows' . PHP_EOL;
    if ($t == 'users') {
        $rows = DB::table($t)->select(['id', 'email', 'tenant_id', 'role'])->get();
        foreach ($rows as $r) {
            echo '  User ' . $r->id . ': ' . $r->email . ', tenant_id=' . $r->tenant_id . ', role=' . $r->role . PHP_EOL;
        }
    }
    if ($t == 'tenant_user') {
        $rows = DB::table($t)->select(['user_id', 'tenant_id', 'role'])->get();
        foreach ($rows as $r) {
            echo '  user_id=' . $r->user_id . ', tenant_id=' . $r->tenant_id . ', role=' . $r->role . PHP_EOL;
        }
    }
    if ($t == 'companies') {
        $rows = DB::table($t)->select(['id', 'name', 'tenant_id'])->get();
        foreach ($rows as $r) {
            echo '  Company ' . $r->id . ': ' . $r->name . ', tenant_id=' . $r->tenant_id . PHP_EOL;
        }
    }
}
