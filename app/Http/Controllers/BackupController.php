<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupController extends TenantAwareController
{
    public function index()
    {
        $backups = $this->tenantQuery(BackupLog::class)->latest()->paginate(20);
        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        try {
            $filename = 'backup_' . $this->getTenantId() . '_' . now()->format('Y-m-d_His') . '.sqlite';
            $path = storage_path('app/backups');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $fullPath = $path . '/' . $filename;
            $dbPath = database_path('database.sqlite');

            if (!file_exists($dbPath)) {
                throw new \Exception('ملف قاعدة البيانات غير موجود');
            }

            if (!copy($dbPath, $fullPath)) {
                throw new \Exception('فشل نسخ ملف قاعدة البيانات');
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
            $dbPath = database_path('database.sqlite');

            if (!file_exists($backupLog->path)) {
                return back()->withErrors(['error' => 'ملف النسخة الاحتياطية غير موجود']);
            }

            DB::statement('PRAGMA wal_checkpoint(FULL)');
            DB::statement('PRAGMA journal_mode=OFF');

            if (!copy($backupLog->path, $dbPath)) {
                throw new \Exception('فشل استعادة ملف قاعدة البيانات');
            }

            DB::statement('PRAGMA journal_mode=WAL');

            return redirect()->route('backups.index')->with('success', 'تم استعادة النسخة الاحتياطية بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الاستعادة: ' . $e->getMessage()]);
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
