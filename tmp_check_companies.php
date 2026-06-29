<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('companies')->select('id','name','tenant_id')->get();
foreach ($rows as $r) {
    echo "id={$r->id} name={$r->name} tenant={$r->tenant_id}\n";
}

$tenants = DB::table('tenants')->select('id','name')->get();
echo "---\n";
foreach ($tenants as $t) {
    echo "tenant id={$t->id} name={$t->name}\n";
}
