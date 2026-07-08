<?php
$base = 'C:\\Users\\Admin\\Desktop\\SMART ERP';
$zipPath = $base . '\\deploy.zip';
if (file_exists($zipPath)) unlink($zipPath);

$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) { echo "FAIL\n"; exit; }

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$ignore = ['vendor', 'node_modules', '.git', '.env', '.env.backup', '.env.production', 'deploy.zip', 'database.sql', 'prep-deploy.bat'];

foreach ($files as $file) {
    $path = $file->getRealPath();
    $local = str_replace('\\', '/', substr($path, strlen($base . '\\')));
    $skip = false;
    foreach ($ignore as $i) {
        if (strpos($local, $i) === 0) { $skip = true; break; }
    }
    if (!$skip) $zip->addFile($path, $local);
}
$zip->close();
echo "OK\n";
