<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupController extends TenantAwareController
{
    /**
     * Directories (relative to the application root) that are never bundled
     * into an archive: VCS history, the backup store itself and all runtime
     * caches / logs that can be regenerated.
     */
    protected const ZIP_EXCLUDED_DIRS = [
        '.git',
        'storage/app/backups',
        'storage/framework',
        'storage/logs',
        'bootstrap/cache',
    ];

    /**
     * Tables that only hold runtime state and must never be dumped.
     */
    protected const DUMP_SKIPPED_TABLES = [
        'cache',
        'cache_locks',
        'sessions',
        'jobs',
        'failed_jobs',
    ];

    protected function getDriver(): string
    {
        return config('database.default');
    }

    protected function runSqliteBackup(string $fullPath): void
    {
        $dbPath = database_path('database.sqlite');
        if (!file_exists($dbPath)) {
            throw new \Exception('ملف قاعدة البيانات غير موجود');
        }
        if (!copy($dbPath, $fullPath)) {
            throw new \Exception('فشل نسخ ملف قاعدة البيانات');
        }
    }

    protected function runSqliteRestore(string $backupPath): void
    {
        $dbPath = database_path('database.sqlite');
        if (!file_exists($backupPath)) {
            throw new \Exception('ملف النسخة الاحتياطية غير موجود');
        }
        DB::statement('PRAGMA wal_checkpoint(FULL)');
        DB::statement('PRAGMA journal_mode=OFF');
        if (!copy($backupPath, $dbPath)) {
            throw new \Exception('فشل استعادة ملف قاعدة البيانات');
        }
        DB::statement('PRAGMA journal_mode=WAL');
    }

    public function index()
    {
        $backups = $this->tenantQuery(BackupLog::class)->latest()->paginate(20);
        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        set_time_limit(0);

        try {
            $path = storage_path('app/backups');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $tenantId = $this->getTenantId();
            $timestamp = now()->format('Y-m-d_His');

            if ($this->getDriver() === 'sqlite') {
                $filename = "backup_{$tenantId}_{$timestamp}.sqlite";
                $fullPath = $path . '/' . $filename;
                $this->runSqliteBackup($fullPath);
            } else {
                $filename = "backup_{$tenantId}_{$timestamp}.zip";
                $fullPath = $path . '/' . $filename;
                $this->buildZipArchive($tenantId, $fullPath);
            }

            $size = filesize($fullPath);

            BackupLog::create([
                'tenant_id' => $tenantId,
                'filename' => $filename,
                'path' => $fullPath,
                'status' => 'completed',
                'type' => 'full',
                'user_id' => auth()->id(),
                'size' => $size,
            ]);

            return redirect()->route('backups.index')->with('success', 'تم إنشاء النسخة الاحتياطية الكاملة بنجاح');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    /**
     * Build a self-contained archive for a single company: a tenant-scoped
     * database dump (database.sql) plus the whole application tree (code,
     * vendor, node_modules, .env, public and the public uploads).
     */
    protected function buildZipArchive(int $tenantId, string $fullPath): void
    {
        $tmpDump = tempnam(sys_get_temp_dir(), 'erpdump_');

        try {
            $this->buildTenantDatabaseDump($tenantId, $tmpDump);

            $zip = new \ZipArchive();
            if ($zip->open($fullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('تعذر إنشاء ملف الأرشيف');
            }

            $zip->addFile($tmpDump, 'database.sql');
            $this->addFolderToZip($zip, base_path(), base_path(), self::ZIP_EXCLUDED_DIRS);

            if (!$zip->close()) {
                throw new \Exception('تعذر حفظ ملف الأرشيف');
            }
        } finally {
            if (file_exists($tmpDump)) {
                @unlink($tmpDump);
            }
        }
    }

    protected function addFolderToZip(\ZipArchive $zip, string $dir, string $base, array $exclude): void
    {
        $entries = @scandir($dir);
        if ($entries === false) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $full = $dir . DIRECTORY_SEPARATOR . $entry;
            $relative = str_replace('\\', '/', substr($full, strlen($base) + 1));

            // Skip symbolic links (public/storage) to avoid loops / duplication.
            if (is_link($full)) {
                continue;
            }

            if (is_dir($full)) {
                if (in_array($relative, $exclude, true)) {
                    continue;
                }
                $this->addFolderToZip($zip, $full, $base, $exclude);
            } else {
                $zip->addFile($full, $relative);
            }
        }
    }

    /**
     * Write a SQL dump limited to one company. Rows are replaced (DELETE then
     * INSERT) instead of dropped, so the dump can be restored into a shared
     * database without touching other companies.
     */
    protected function buildTenantDatabaseDump(int $tenantId, string $target, bool $withSchema = true): void
    {
        $cfg = config('database.connections.mysql');
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $cfg['host'],
            $cfg['port'],
            $cfg['database']
        );

        $pdo = new \PDO($dsn, $cfg['username'], $cfg['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        $database = $cfg['database'];

        $tenantTables = $pdo->query(
            'SELECT table_name FROM information_schema.columns WHERE table_schema = '
            . $pdo->quote($database) . " AND column_name = 'tenant_id'"
        )->fetchAll(\PDO::FETCH_COLUMN);
        $tenantTables = array_flip($tenantTables);

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        $fh = fopen($target, 'w');
        if ($fh === false) {
            throw new \Exception('تعذر إنشاء ملف قاعدة البيانات المؤقت');
        }

        fwrite($fh, "-- Smartline ERP backup\n");
        fwrite($fh, "-- tenant_id: {$tenantId}\n");
        fwrite($fh, '-- generated_at: ' . now()->toDateTimeString() . "\n\n");
        fwrite($fh, "SET NAMES utf8mb4;\n");
        fwrite($fh, "SET SESSION sql_mode='';\n");
        fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $table) {
            if (in_array($table, self::DUMP_SKIPPED_TABLES, true)) {
                continue;
            }

            if ($withSchema) {
                $create = $pdo->query('SHOW CREATE TABLE ' . $this->quoteIdentifier($table))->fetch(\PDO::FETCH_NUM)[1];

                // Only real tables are bundled; views are derived and are
                // rebuilt from the migrations.
                if (stripos($create, 'CREATE TABLE') !== 0) {
                    continue;
                }

                $create = preg_replace('/^CREATE TABLE /i', 'CREATE TABLE IF NOT EXISTS ', $create);
                fwrite($fh, $create . ";\n\n");
            }

            [$selectWhere, $deleteWhere] = $this->tenantScope($table, isset($tenantTables[$table]), $tenantId);
            if ($selectWhere === null) {
                continue;
            }

            fwrite($fh, 'DELETE FROM ' . $this->quoteIdentifier($table)
                . ($deleteWhere ? ' WHERE ' . $deleteWhere : '') . ";\n");

            $rows = $pdo->query(
                'SELECT * FROM ' . $this->quoteIdentifier($table)
                . ($selectWhere ? ' WHERE ' . $selectWhere : '')
            );

            $columns = null;
            $buffer = [];

            while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
                if ($columns === null) {
                    $columns = array_keys($row);
                }

                $buffer[] = '(' . implode(', ', array_map(
                    fn ($value) => $value === null ? 'NULL' : $pdo->quote((string) $value),
                    array_values($row)
                )) . ')';

                if (count($buffer) >= 200) {
                    $this->writeInsert($fh, $table, $columns, $buffer);
                    $buffer = [];
                }
            }

            if ($buffer && $columns !== null) {
                $this->writeInsert($fh, $table, $columns, $buffer);
            }

            fwrite($fh, "\n");
        }

        fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fh);
    }

    protected function writeInsert($fh, string $table, array $columns, array $rows): void
    {
        fwrite(
            $fh,
            'INSERT INTO ' . $this->quoteIdentifier($table)
            . ' (`' . implode('`,`', $columns) . "`) VALUES\n"
            . implode(",\n", $rows) . ";\n"
        );
    }

    /**
     * Resolve the WHERE clauses (for SELECT and DELETE) that isolate a table's
     * rows to the requested tenant. A null SELECT means "no data for this
     * table" (e.g. the migrations log).
     *
     * @return array{0: ?string, 1: ?string}
     */
    protected function tenantScope(string $table, bool $hasTenantColumn, int $tenantId): array
    {
        if ($hasTenantColumn) {
            return ["tenant_id = {$tenantId}", "tenant_id = {$tenantId}"];
        }

        $invoiceInstallments = "purchase_invoice_id IN (SELECT id FROM purchase_invoices WHERE tenant_id = {$tenantId})";
        $salesInstallments = "sales_invoice_id IN (SELECT id FROM sales_invoices WHERE tenant_id = {$tenantId})";
        $handoverItems = "handover_id IN (SELECT id FROM document_handovers WHERE tenant_id = {$tenantId})";

        return match ($table) {
            'tenants' => ["id = {$tenantId}", "id = {$tenantId}"],
            'permission_role' => ['', ''],
            'purchase_invoice_installments' => [$invoiceInstallments, $invoiceInstallments],
            'sales_invoice_installments' => [$salesInstallments, $salesInstallments],
            'document_handover_items' => [$handoverItems, $handoverItems],
            default => [null, null],
        };
    }

    protected function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    public function restore(BackupLog $backupLog)
    {
        $this->authorizeTenant($backupLog);
        set_time_limit(0);

        try {
            if (!file_exists($backupLog->path)) {
                return back()->withErrors(['error' => 'ملف النسخة الاحتياطية غير موجود']);
            }

            $extension = strtolower(pathinfo($backupLog->path, PATHINFO_EXTENSION));

            if ($extension === 'zip') {
                $sql = $this->extractSqlFromZip($backupLog->path);
                $tmp = tempnam(sys_get_temp_dir(), 'erprestore_');
                file_put_contents($tmp, $sql);

                try {
                    $this->importSqlFile($tmp);
                } finally {
                    @unlink($tmp);
                }
            } elseif (in_array($extension, ['sqlite', 'db'], true)) {
                $this->runSqliteRestore($backupLog->path);
            } else {
                $this->importSqlFile($backupLog->path);
            }

            return redirect()->route('backups.index')->with('success', 'تم استعادة النسخة الاحتياطية بنجاح');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الاستعادة: ' . $e->getMessage()]);
        }
    }

    protected function extractSqlFromZip(string $zipPath): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \Exception('تعذر فتح ملف الأرشيف');
        }

        $sql = $zip->getFromName('database.sql');
        $zip->close();

        if ($sql === false) {
            throw new \Exception('ملف database.sql غير موجود داخل الأرشيف');
        }

        return $sql;
    }

    /**
     * Import a dump through mysqli::multi_query, which — unlike PDO::exec —
     * understands a file made of many statements. Shell access is disabled on
     * this host, so the mysql client cannot be used.
     */
    protected function importSqlFile(string $path): void
    {
        $cfg = config('database.connections.mysql');

        $mysqli = @new \mysqli($cfg['host'], $cfg['username'], $cfg['password'], $cfg['database'], (int) $cfg['port']);
        if ($mysqli->connect_errno) {
            throw new \Exception('تعذر الاتصال بقاعدة البيانات: ' . $mysqli->connect_error);
        }

        $mysqli->set_charset('utf8mb4');
        $mysqli->query("SET SESSION sql_mode=''");

        $sql = file_get_contents($path);
        if ($sql === false || trim($sql) === '') {
            $mysqli->close();
            throw new \Exception('ملف النسخة فارغ أو غير قابل للقراءة');
        }

        if (!$mysqli->multi_query($sql)) {
            $error = $mysqli->error;
            $mysqli->close();
            throw new \Exception('فشل الاستيراد: ' . $error);
        }

        while ($mysqli->more_results()) {
            $mysqli->next_result();
        }

        if ($mysqli->errno) {
            $error = $mysqli->error;
            $mysqli->close();
            throw new \Exception('فشل الاستيراد: ' . $error);
        }

        $mysqli->close();
    }

    public function download(BackupLog $backupLog)
    {
        $this->authorizeTenant($backupLog);

        if (!file_exists($backupLog->path)) {
            return back()->withErrors(['error' => 'ملف النسخة الاحتياطية غير موجود']);
        }

        return response()->download($backupLog->path, $backupLog->filename);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sqlite,db,sql,txt,zip',
        ]);

        try {
            $file = $request->file('backup_file');
            $filename = 'uploaded_' . $this->getTenantId() . '_' . now()->format('Y-m-d_His') . '.' . $file->getClientOriginalExtension();
            $path = storage_path('app/backups');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $fullPath = $path . '/' . $filename;
            $file->move($path, $filename);

            $size = filesize($fullPath);

            BackupLog::create([
                'tenant_id' => $this->getTenantId(),
                'filename' => $filename,
                'path' => $fullPath,
                'status' => 'completed',
                'user_id' => auth()->id(),
                'size' => $size,
            ]);

            return redirect()->route('backups.index')->with('success', 'تم رفع النسخة الاحتياطية بنجاح. يمكنك استخدام زر الاستعادة لتطبيقها.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الرفع: ' . $e->getMessage()]);
        }
    }

    public function destroy(BackupLog $backupLog)
    {
        $this->authorizeTenant($backupLog);

        if (file_exists($backupLog->path)) {
            unlink($backupLog->path);
        }

        $backupLog->delete();

        return redirect()->route('backups.index')->with('success', 'تم حذف النسخة الاحتياطية بنجاح');
    }

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
