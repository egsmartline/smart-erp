<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceInstallment;
use App\Models\SalesInvoiceInstallment;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Payment;
use App\Traits\ComputesReceivableBalances;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends TenantAwareController
{
    use ComputesReceivableBalances;
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

        $currencyCodes = $this->currencyCodeMap($tenantId);
        $baseCurrencyCode = $this->baseCurrencyCode($tenantId);

        $receivableCustomers = Customer::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with(['openingBalanceCurrency',
                    'salesInvoices' => fn($q) => $q->where('status', '!=', 'voided')
                    ->select('id', 'tenant_id', 'customer_id', 'total', 'currency_id', 'status'),
                    'payments' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'type', 'amount', 'currency_id'),
                    'discountNotes' => fn($q) => $q->select('id', 'tenant_id', 'customer_id', 'amount')])
            ->get()
            ->map(function ($c) use ($currencyCodes, $baseCurrencyCode) {
                $byCurrency = $this->receivableByCurrency($c, $currencyCodes, $baseCurrencyCode);
                $c->currency_balances = $byCurrency;
                $c->real_balance = $byCurrency[$baseCurrencyCode] ?? 0.0;
                $c->positive_balance = collect($byCurrency)->filter(fn($v) => $v > 0)->sum();
                return $c;
            })
            ->filter(fn($c) => $c->positive_balance > 0.009)
            ->sortByDesc('positive_balance')
            ->values();

        $balanceChartLabels = $receivableCustomers->pluck('name')->toArray();
        $balanceChartData = $receivableCustomers->pluck('real_balance')->toArray();

        $debtors = $receivableCustomers;

        $debtorTotals = $this->totalByCurrency($debtors);
        $debtorTotal = $debtorTotals[$baseCurrencyCode] ?? 0.0;

        $monthDues = collect();

        $purchaseMonthDues = PurchaseInvoiceInstallment::query()
            ->whereColumn('amount', '>', 'paid_amount')
            ->whereYear('due_date', Carbon::now()->year)
            ->whereMonth('due_date', Carbon::now()->month)
            ->whereHas('invoice', fn($q) => $q->where('tenant_id', $tenantId)->where('status', 'posted'))
            ->with(['invoice' => fn($q) => $q->with('supplier')])
            ->orderBy('due_date')
            ->get();

        $purchaseMonthDueTotal = $purchaseMonthDues->sum(function ($inst) {
            return (float) $inst->amount - (float) $inst->paid_amount;
        });

        $customerMonthDues = SalesInvoiceInstallment::query()
            ->whereColumn('amount', '>', 'paid_amount')
            ->whereYear('due_date', Carbon::now()->year)
            ->whereMonth('due_date', Carbon::now()->month)
            ->whereHas('invoice', fn($q) => $q->where('tenant_id', $tenantId)->where('status', 'posted'))
            ->with(['invoice' => fn($q) => $q->with('customer')])
            ->orderBy('due_date')
            ->get();

        $customerMonthDueTotal = $customerMonthDues->sum(function ($inst) {
            return (float) $inst->amount - (float) $inst->paid_amount;
        });

        return view('dashboard', compact(
            'stats', 'recentSales', 'recentPurchases',
            'accountsCount', 'salesChartLabels', 'salesChartData',
            'balanceChartLabels', 'balanceChartData',
            'monthDues',
            'purchaseMonthDues', 'purchaseMonthDueTotal',
            'customerMonthDues', 'customerMonthDueTotal',
            'debtors', 'debtorTotal', 'debtorTotals', 'baseCurrencyCode', 'currencyCodes'
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
