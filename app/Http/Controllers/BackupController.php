<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;

class BackupController extends TenantAwareController
{
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

    protected function runMysqlBackup(string $fullPath): void
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($fullPath)
        );

        $result = Process::run($command);

        if (!$result->successful()) {
            throw new \Exception('فشل تصدير قاعدة البيانات: ' . $result->errorOutput());
        }
    }

    protected function runMysqlRestore(string $backupPath): void
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );

        $result = Process::run($command);

        if (!$result->successful()) {
            throw new \Exception('فشل استيراد قاعدة البيانات: ' . $result->errorOutput());
        }
    }

    public function index()
    {
        $backups = $this->tenantQuery(BackupLog::class)->latest()->paginate(20);
        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        try {
            $driver = $this->getDriver();
            $ext = $driver === 'sqlite' ? 'sqlite' : 'sql';
            $filename = 'backup_' . $this->getTenantId() . '_' . now()->format('Y-m-d_His') . '.' . $ext;
            $path = storage_path('app/backups');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $fullPath = $path . '/' . $filename;

            if ($driver === 'sqlite') {
                $this->runSqliteBackup($fullPath);
            } else {
                $this->runMysqlBackup($fullPath);
            }

            $size = filesize($fullPath);

            BackupLog::create([
                'tenant_id' => $this->getTenantId(),
                'filename' => $filename,
                'path' => $fullPath,
                'status' => 'completed',
                'user_id' => auth()->id(),
                'size' => $size,
            ]);

            return redirect()->route('backups.index')->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    public function restore(BackupLog $backupLog)
    {
        $this->authorizeTenant($backupLog);

        try {
            if (!file_exists($backupLog->path)) {
                return back()->withErrors(['error' => 'ملف النسخة الاحتياطية غير موجود']);
            }

            $driver = $this->getDriver();

            if ($driver === 'sqlite') {
                $this->runSqliteRestore($backupLog->path);
            } else {
                $this->runMysqlRestore($backupLog->path);
            }

            return redirect()->route('backups.index')->with('success', 'تم استعادة النسخة الاحتياطية بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الاستعادة: ' . $e->getMessage()]);
        }
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
            'backup_file' => 'required|file|mimes:sqlite,db,sql,txt',
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

            $backupLog = BackupLog::create([
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
