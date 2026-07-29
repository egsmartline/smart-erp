<?php

namespace App\Http\Controllers;

use App\Models\DiscountNote;
use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountNoteController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(DiscountNote::class)
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
            $query->where('note_number', 'like', '%' . $request->search . '%');
        }

        $notes = $query->latest()->paginate(15);
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();

        return view('discount-notes.index', compact('notes', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $invoices = $this->tenantQuery(SalesInvoice::class)
            ->where('status', 'posted')
            ->with('customer')
            ->latest()
            ->get();
        $noteNumber = $this->generateNoteNumber();

        $selectedInvoice = null;
        if ($request->filled('invoice_id')) {
            $selectedInvoice = SalesInvoice::where('id', $request->invoice_id)
                ->where('tenant_id', $this->getTenantId())
                ->first();
        }

        return view('discount-notes.create', compact('customers', 'invoices', 'noteNumber', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
        ]);

        DB::beginTransaction();

        try {
            $note = DiscountNote::create([
                'tenant_id' => $this->getTenantId(),
                'customer_id' => $validated['customer_id'],
                'original_invoice_id' => $validated['sales_invoice_id'] ?? null,
                'note_number' => $this->generateNoteNumber(),
                'date' => $validated['date'],
                'amount' => $validated['amount'],
                'reason' => $validated['reason'],
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('discount-notes.show', $note)
                ->with('success', 'تم إنشاء إشعار الخصم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الإشعار: ' . $e->getMessage());
        }
    }

    public function show(DiscountNote $discountNote)
    {
        $discountNote->load(['customer', 'salesInvoice', 'creator']);
        return view('discount-notes.show', compact('discountNote'));
    }

    public function edit(DiscountNote $discountNote)
    {
        $customers = $this->tenantQuery(Customer::class)->where('is_active', true)->get();
        $invoices = $this->tenantQuery(SalesInvoice::class)
            ->where('status', 'posted')
            ->with('customer')
            ->latest()
            ->get();

        return view('discount-notes.edit', compact('discountNote', 'customers', 'invoices'));
    }

    public function update(Request $request, DiscountNote $discountNote)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
            'sales_invoice_id' => 'nullable|exists:sales_invoices,id',
        ]);

        DB::beginTransaction();

        try {
            if ($discountNote->status === 'posted') {
                $this->reverseDiscountEffects($discountNote);
            }

            $discountNote->update([
                'customer_id' => $validated['customer_id'],
                'original_invoice_id' => $validated['sales_invoice_id'] ?? null,
                'date' => $validated['date'],
                'amount' => $validated['amount'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($discountNote->status === 'posted') {
                $this->applyDiscountEffects($discountNote);
            }

            DB::commit();

            return redirect()->route('discount-notes.show', $discountNote)
                ->with('success', 'تم تحديث إشعار الخصم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    public function destroy(DiscountNote $discountNote)
    {
        DB::beginTransaction();

        try {
            if ($discountNote->status === 'posted') {
                $this->reverseDiscountEffects($discountNote);
            }

            $discountNote->delete();
            DB::commit();

            return redirect()->route('discount-notes.index')
                ->with('success', 'تم حذف إشعار الخصم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    public function post(DiscountNote $discountNote)
    {
        if ($discountNote->status !== 'draft') {
            return back()->with('error', 'تم ترحيل هذا الإشعار بالفعل');
        }

        DB::beginTransaction();

        try {
            $discountNote->update(['status' => 'posted']);

            $this->applyDiscountEffects($discountNote);

            DB::commit();

            return back()->with('success', 'تم ترحيل إشعار الخصم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الترحيل: ' . $e->getMessage());
        }
    }

    protected function applyDiscountEffects(DiscountNote $discountNote)
    {
        $customer = $discountNote->customer;
        if ($customer) {
            $customer->decrement('balance', $discountNote->amount);
        }

        if ($discountNote->original_invoice_id) {
            $invoice = $discountNote->salesInvoice;
            if ($invoice) {
                $invoice->decrement('due_amount', $discountNote->amount);
            }
        }

        $journalService = app(JournalService::class);
        $arAccount = $journalService->getAccountByCode($this->getTenantId(), '1103');
        $revenueAccount = $journalService->getAccountByCode($this->getTenantId(), '41');

        $lines = [];

        if ($revenueAccount) {
            $lines[] = [
                'account_id' => $revenueAccount->id,
                'debit' => $discountNote->amount,
                'credit' => 0,
                'description' => 'إشعار خصم - ' . $discountNote->note_number,
            ];
        }

        if ($arAccount) {
            $lines[] = [
                'account_id' => $arAccount->id,
                'debit' => 0,
                'credit' => $discountNote->amount,
                'description' => 'إشعار خصم - ' . $discountNote->note_number,
            ];
        }

        if (!empty($lines)) {
            $journalService->createEntry([
                'tenant_id' => $this->getTenantId(),
                'date' => $discountNote->date->format('Y-m-d'),
                'description' => 'إشعار خصم للعميل ' . ($discountNote->customer->name ?? ''),
                'reference' => 'discount_note:' . $discountNote->id,
                'type' => 'discount_note',
                'lines' => $lines,
            ]);
        }
    }

    protected function reverseDiscountEffects(DiscountNote $discountNote)
    {
        $customer = $discountNote->customer;
        if ($customer) {
            $customer->increment('balance', $discountNote->amount);
        }

        if ($discountNote->original_invoice_id) {
            $invoice = $discountNote->salesInvoice;
            if ($invoice) {
                $invoice->increment('due_amount', $discountNote->amount);
            }
        }

        JournalEntry::where('tenant_id', $this->getTenantId())
            ->where('reference', 'discount_note:' . $discountNote->id)
            ->delete();
    }

    protected function generateNoteNumber(): string
    {
        $year = date('Y');
        $lastNote = $this->tenantQuery(DiscountNote::class)
            ->withTrashed()
            ->whereYear('date', $year)
            ->max('note_number');

        if ($lastNote) {
            $lastSequence = (int) substr($lastNote, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return 'CDN-' . $year . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}
