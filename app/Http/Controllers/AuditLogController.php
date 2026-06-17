<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends TenantAwareController
{
    public function index(Request $request)
    {
        $logs = $this->tenantQuery(AuditLog::class)
            ->with('user')
            ->when($request->table_name, fn($q, $t) => $q->where('table_name', $t))
            ->when($request->action, fn($q, $a) => $q->where('action', $a))
            ->latest()
            ->paginate(50);

        return view('audit-log.index', compact('logs'));
    }
}
