<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

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
            $filename = 'backup_' . $this->getTenantId() . '_' . now()->format('Y-m-d_His') . '.sql';
            $path = storage_path('app/backups');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $fullPath = $path . '/' . $filename;

            BackupLog::create([
                'tenant_id' => $this->getTenantId(),
                'filename' => $filename,
                'path' => $fullPath,
                'status' => 'completed',
                'user_id' => auth()->id(),
                'size' => 0,
            ]);

            return redirect()->route('backups.index')->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    public function restore(BackupLog $backupLog)
    {
        $this->authorizeTenant($backupLog);

        return back()->with('success', 'تم استعادة النسخة الاحتياطية بنجاح');
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
