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
            ->when($request->model, fn($q, $m) => $q->where('model', $m))
            ->when($request->action, fn($q, $a) => $q->where('action', $a))
            ->latest()
            ->paginate(50);

        $models = collect([
            'App\Models\SalesInvoice' => 'فواتير المبيعات',
            'App\Models\PurchaseInvoice' => 'فواتير المشتريات',
            'App\Models\SalesInvoiceLine' => 'بنود فواتير المبيعات',
            'App\Models\PurchaseInvoiceLine' => 'بنود فواتير المشتريات',
            'App\Models\Customer' => 'العملاء',
            'App\Models\Supplier' => 'الموردين',
            'App\Models\Item' => 'الأصناف',
            'App\Models\Payment' => 'المدفوعات',
            'App\Models\Expense' => 'المصروفات',
            'App\Models\Quotation' => 'عروض الأسعار',
            'App\Models\SalesReturn' => 'مرتجعات المبيعات',
            'App\Models\PurchaseReturn' => 'مرتجعات المشتريات',
        ]);

        return view('audit-log.index', compact('logs', 'models'));
    }
}
