<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

echo "migrations that mention transfer:\n";
$migs = DB::table('migrations')->where('migration', 'like', '%transfer%')->get();
foreach ($migs as $m) {
    echo '  ' . $m->migration . ' batch=' . $m->batch . "\n";
}

echo "\nall migration files in database/migrations:\n";
$files = scandir('database/migrations');
foreach ($files as $f) {
    if ($f !== '.' && $f !== '..') {
        echo '  ' . $f . "\n";
    }
}

echo "\nTransfer model table:\n";
$ref = new ReflectionClass('App\Models\Transfer');
$props = $ref->getDefaultProperties();
echo '  table: ' . ($props['table'] ?? 'not set (default)') . "\n";
