<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Http\Request;

class DashboardController extends TenantAwareController
{
    public function index()
    {
        $tenantId = $this->getTenantId();

        $stats = [
            'total_sales' => SalesInvoice::where('tenant_id', $tenantId)->where('status', 'posted')->sum('total'),
            'total_purchases' => PurchaseInvoice::where('tenant_id', $tenantId)->where('status', 'posted')->sum('total'),
            'customers_count' => Customer::where('tenant_id', $tenantId)->count(),
            'suppliers_count' => Supplier::where('tenant_id', $tenantId)->count(),
            'items_count' => Item::where('tenant_id', $tenantId)->count(),
            'pending_invoices' => SalesInvoice::where('tenant_id', $tenantId)->where('status', 'draft')->count(),
            'pending_purchases' => PurchaseInvoice::where('tenant_id', $tenantId)->where('status', 'draft')->count(),
        ];

        $recentSales = SalesInvoice::where('tenant_id', $tenantId)
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        $recentPurchases = PurchaseInvoice::where('tenant_id', $tenantId)
            ->with('supplier')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentSales', 'recentPurchases'));
    }

    public function switchTenant(Request $request, $tenantId)
    {
        $user = auth()->user();
        $tenant = \App\Models\Tenant::findOrFail($tenantId);
        $accessible = $user->getAccessibleTenants()->pluck('id')->toArray();
        if (!in_array($tenant->id, $accessible)) {
            abort(403);
        }
        session(['current_tenant_id' => $tenant->id]);
        return redirect()->route('dashboard')->with('success', 'تم التبديل إلى ' . ($tenant->name ?? $tenant->name_en));
    }
}
