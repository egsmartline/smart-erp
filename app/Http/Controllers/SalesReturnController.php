<?php

namespace App\Http\Controllers;

use App\Models\SalesReturn;
use App\Models\SalesReturnLine;
use App\Models\SalesInvoice;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\ItemWarehouse;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(SalesReturn::class)
            ->with(['customer', 'salesInvoice']);

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('return_number', 'like', '%' . $request->search . '%');
        }

        $returns = $query->latest()->paginate(15);
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();

        return view('sales-returns.index', compact('returns', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $invoices = $this->tenantQuery(SalesInvoice::class)
            ->where('status', 'posted')
            ->with('customer')
            ->latest()
            ->get();
        $returnNumber = $this->generateReturnNumber();

        $selectedInvoice = null;
        if ($request->filled('invoice_id')) {
            $selectedInvoice = SalesInvoice::where('id', $request->invoice_id)
                ->where('tenant_id', $this->getTenantId())
                ->with('lines.item')
                ->first();
        }

        return view('sales-returns.create', compact('customers', 'warehouses', 'invoices', 'returnNumber', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sales_invoice_id' => 'required|exists:sales_invoices,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.reason' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $totalTax = 0;

            $lineData = [];
            foreach ($validated['lines'] as $line) {
                $lineSubtotal = $line['quantity'] * $line['unit_price'];
                $lineTax = $lineSubtotal * (($line['tax_percent'] ?? 0) / 100);

                $subtotal += $lineSubtotal;
                $totalTax += $lineTax;

                $lineData[] = [
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'tax_percent' => $line['tax_percent'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineSubtotal + $lineTax,
                    'reason' => $line['reason'] ?? null,
                ];
            }

            $grandTotal = $subtotal + $totalTax;

            $return = SalesReturn::create([
                'tenant_id' => $this->getTenantId(),
                'customer_id' => $validated['customer_id'],
                'original_invoice_id' => $validated['sales_invoice_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'return_number' => $this->generateReturnNumber(),
                'date' => $validated['date'],
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'total' => $grandTotal,
                'reason' => $validated['reason'] ?? null,
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            foreach ($lineData as $data) {
                $data['sales_return_id'] = $return->id;
                SalesReturnLine::create($data);
            }

            DB::commit();

            return redirect()->route('sales-returns.show', $return)
                ->with('success', 'تم إنشاء مرتجع المبيعات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المرتجع: ' . $e->getMessage());
        }
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['customer', 'salesInvoice', 'warehouse', 'lines.item', 'creator']);
        return view('sales-returns.show', compact('salesReturn'));
    }

    public function post(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'draft') {
            return back()->with('error', 'تم ترحيل هذا المرتجع بالفعل');
        }

        DB::beginTransaction();

        try {
            $salesReturn->update(['status' => 'posted']);

            foreach ($salesReturn->lines as $line) {
                $itemWarehouse = ItemWarehouse::where('item_id', $line->item_id)
                    ->where('warehouse_id', $salesReturn->warehouse_id)
                    ->first();

                if ($itemWarehouse) {
                    $itemWarehouse->increment('quantity', $line->quantity);
                }

                StockMovement::create([
                    'item_id' => $line->item_id,
                    'warehouse_id' => $salesReturn->warehouse_id,
                    'stockable_type' => SalesReturn::class,
                    'stockable_id' => $salesReturn->id,
                    'type' => 'in',
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->unit_price,
                    'total_cost' => $line->total,
                    'reference_number' => $salesReturn->return_number,
                    'date' => now()->toDateString(),
                    'notes' => 'إدخال مخزون - مرتجع مبيعات',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'تم ترحيل المرتجع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الترحيل: ' . $e->getMessage());
        }
    }

    protected function generateReturnNumber(): string
    {
        $year = date('Y');
        $lastReturn = $this->tenantQuery(SalesReturn::class)
            ->withTrashed()
            ->whereYear('date', $year)
            ->max('return_number');

        if ($lastReturn) {
            $lastSequence = (int) substr($lastReturn, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return 'SR-' . $year . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}
