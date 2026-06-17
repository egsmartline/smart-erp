<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(Quotation::class)
            ->with('customer');

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
            $query->where('quotation_number', 'like', '%' . $request->search . '%');
        }

        $quotations = $query->latest()->paginate(15);
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();

        return view('quotations.index', compact('quotations', 'customers'));
    }

    public function create()
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();
        $quotationNumber = $this->generateQuotationNumber();

        return view('quotations.create', compact('customers', 'warehouses', 'quotationNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            $lineData = [];
            foreach ($validated['lines'] as $line) {
                $lineSubtotal = $line['quantity'] * $line['unit_price'];
                $lineDiscount = $lineSubtotal * (($line['discount_percent'] ?? 0) / 100);
                $lineAfterDiscount = $lineSubtotal - $lineDiscount;
                $lineTax = $lineAfterDiscount * (($line['tax_rate'] ?? 0) / 100);
                $lineTotal = $lineAfterDiscount + $lineTax;

                $subtotal += $lineSubtotal;
                $totalDiscount += $lineDiscount;
                $totalTax += $lineTax;

                $lineData[] = [
                    'item_id' => $line['item_id'],
                    'description' => $line['description'] ?? null,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_rate' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                ];
            }

            $overallDiscount = $validated['discount_amount'] ?? 0;
            $grandTotal = $subtotal - $totalDiscount - $overallDiscount + $totalTax;

            $quotation = Quotation::create([
                'tenant_id' => $this->getTenantId(),
                'customer_id' => $validated['customer_id'],
                'quotation_number' => $this->generateQuotationNumber(),
                'date' => $validated['date'],
                'valid_until' => $validated['valid_until'],
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount + $overallDiscount,
                'discount_percent' => 0,
                'tax_amount' => $totalTax,
                'total' => $grandTotal,
                'currency_code' => 'SAR',
                'exchange_rate' => 1,
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            foreach ($lineData as $data) {
                $data['quotation_id'] = $quotation->id;
                QuotationLine::create($data);
            }

            DB::commit();

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'تم إنشاء عرض الأسعار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء العرض: ' . $e->getMessage());
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'lines.item']);
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return back()->with('error', 'لا يمكن تعديل عرض أسعار في هذه الحالة');
        }

        $quotation->load('lines.item');
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $warehouses = $this->tenantQuery(Warehouse::class)->where('is_active', true)->get();

        return view('quotations.edit', compact('quotation', 'customers', 'warehouses'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return back()->with('error', 'لا يمكن تعديل عرض أسعار في هذه الحالة');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.description' => 'nullable|string',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            $lineData = [];
            foreach ($validated['lines'] as $line) {
                $lineSubtotal = $line['quantity'] * $line['unit_price'];
                $lineDiscount = $lineSubtotal * (($line['discount_percent'] ?? 0) / 100);
                $lineAfterDiscount = $lineSubtotal - $lineDiscount;
                $lineTax = $lineAfterDiscount * (($line['tax_rate'] ?? 0) / 100);
                $lineTotal = $lineAfterDiscount + $lineTax;

                $subtotal += $lineSubtotal;
                $totalDiscount += $lineDiscount;
                $totalTax += $lineTax;

                $lineData[] = [
                    'item_id' => $line['item_id'],
                    'description' => $line['description'] ?? null,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount_percent' => $line['discount_percent'] ?? 0,
                    'discount_amount' => $lineDiscount,
                    'tax_rate' => $line['tax_rate'] ?? 0,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                ];
            }

            $overallDiscount = $validated['discount_amount'] ?? 0;
            $grandTotal = $subtotal - $totalDiscount - $overallDiscount + $totalTax;

            $quotation->update([
                'customer_id' => $validated['customer_id'],
                'date' => $validated['date'],
                'valid_until' => $validated['valid_until'],
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount + $overallDiscount,
                'tax_amount' => $totalTax,
                'total' => $grandTotal,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $quotation->lines()->delete();

            foreach ($lineData as $data) {
                $data['quotation_id'] = $quotation->id;
                QuotationLine::create($data);
            }

            DB::commit();

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'تم تحديث عرض الأسعار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث العرض: ' . $e->getMessage());
        }
    }

    public function destroy(Quotation $quotation)
    {
        if (in_array($quotation->status, ['accepted', 'converted'])) {
            return back()->with('error', 'لا يمكن حذف عرض أسعار تم قبوله أو تحويله');
        }

        DB::beginTransaction();

        try {
            $quotation->lines()->delete();
            $quotation->delete();

            DB::commit();

            return redirect()->route('quotations.index')
                ->with('success', 'تم حذف عرض الأسعار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    public function convert(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'تم تحويل هذا العرض بالفعل');
        }

        if (!in_array($quotation->status, ['draft', 'sent', 'accepted'])) {
            return back()->with('error', 'لا يمكن تحويل عرض أسعار في هذه الحالة');
        }

        DB::beginTransaction();

        try {
            $warehouse = $this->tenantQuery(Warehouse::class)->where('is_default', true)->first();

            $invoice = SalesInvoice::create([
                'tenant_id' => $this->getTenantId(),
                'customer_id' => $quotation->customer_id,
                'warehouse_id' => $warehouse->id ?? 1,
                'cashier_id' => auth()->id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => $quotation->subtotal,
                'discount_amount' => $quotation->discount_amount,
                'tax_amount' => $quotation->tax_amount,
                'total' => $quotation->total,
                'paid_amount' => 0,
                'due_amount' => $quotation->total,
                'currency_code' => 'SAR',
                'exchange_rate' => 1,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'notes' => 'تحويل من عرض أسعار رقم: ' . $quotation->quotation_number,
                'reference_type' => Quotation::class,
                'reference_id' => $quotation->id,
            ]);

            foreach ($quotation->lines as $qLine) {
                SalesInvoiceLine::create([
                    'sales_invoice_id' => $invoice->id,
                    'item_id' => $qLine->item_id,
                    'description' => $qLine->description,
                    'quantity' => $qLine->quantity,
                    'unit_price' => $qLine->unit_price,
                    'discount_percent' => $qLine->discount_percent,
                    'discount_amount' => $qLine->discount_amount,
                    'tax_rate' => $qLine->tax_rate,
                    'tax_amount' => $qLine->tax_amount,
                    'subtotal' => $qLine->subtotal,
                    'total' => $qLine->total,
                    'warehouse_id' => $warehouse->id ?? 1,
                ]);
            }

            $quotation->update([
                'status' => 'converted',
                'converted_to_invoice' => true,
                'converted_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('sales-invoices.show', $invoice)
                ->with('success', 'تم تحويل عرض الأسعار إلى فاتورة مبيعات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء التحويل: ' . $e->getMessage());
        }
    }

    public function send(Quotation $quotation)
    {
        if ($quotation->status !== 'draft') {
            return back()->with('error', 'لا يمكن إرسال عرض أسعار غير مسودة');
        }

        $quotation->update(['status' => 'sent']);

        return back()->with('success', 'تم إرسال عرض الأسعار بنجاح');
    }

    public function accept(Quotation $quotation)
    {
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return back()->with('error', 'لا يمكن قبول عرض أسعار في هذه الحالة');
        }

        $quotation->update(['status' => 'accepted']);

        return back()->with('success', 'تم قبول عرض الأسعار بنجاح');
    }

    public function reject(Quotation $quotation)
    {
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return back()->with('error', 'لا يمكن رفض عرض أسعار في هذه الحالة');
        }

        $quotation->update(['status' => 'rejected']);

        return back()->with('success', 'تم رفض عرض الأسعار');
    }

    protected function generateQuotationNumber(): string
    {
        $year = date('Y');
        $lastQuotation = $this->tenantQuery(Quotation::class)
            ->whereYear('date', $year)
            ->max('quotation_number');

        if ($lastQuotation) {
            $lastSequence = (int) substr($lastQuotation, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return 'QT-' . $year . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    protected function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = $this->tenantQuery(SalesInvoice::class)
            ->whereYear('date', $year)
            ->max('invoice_number');

        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return 'INV-S-' . $year . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}
