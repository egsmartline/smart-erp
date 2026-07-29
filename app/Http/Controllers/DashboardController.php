<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        $accountsCount = Account::where('tenant_id', $tenantId)->count();

        $year = Carbon::now()->year;
        $salesByMonth = SalesInvoice::where('tenant_id', $tenantId)
            ->where('status', 'posted')
            ->whereYear('date', $year)
            ->selectRaw('MONTH(date) as month, SUM(total) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $salesChartLabels = [];
        $salesChartData = [];
        $monthNames = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        for ($m = 1; $m <= 12; $m++) {
            $salesChartLabels[] = $monthNames[$m - 1];
            $salesChartData[] = $salesByMonth[$m] ?? 0;
        }

        $receivableCustomers = Customer::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with(['salesInvoices' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'total'),
                    'payments' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'type', 'amount')])
            ->get()
            ->map(function ($c) {
                $openingBal = (float) ($c->opening_balance ?? 0);
                $balance = $c->opening_balance_type === 'credit' ? -$openingBal : $openingBal;
                foreach ($c->salesInvoices as $inv) { $balance += (float) $inv->total; }
                foreach ($c->payments as $pay) {
                    $amount = (float) $pay->amount;
                    if ($pay->type === 'receipt') $amount = -$amount;
                    $balance += $amount;
                }
                $c->real_balance = $balance;
                return $c;
            })
            ->filter(fn($c) => $c->real_balance > 0)
            ->sortByDesc('real_balance')
            ->take(10)
            ->values();

        $balanceChartLabels = $receivableCustomers->pluck('name')->toArray();
        $balanceChartData = $receivableCustomers->pluck('real_balance')->toArray();

        return view('dashboard', compact(
            'stats', 'recentSales', 'recentPurchases',
            'accountsCount', 'salesChartLabels', 'salesChartData',
            'balanceChartLabels', 'balanceChartData'
        ));
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
