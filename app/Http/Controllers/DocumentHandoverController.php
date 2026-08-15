<?php

namespace App\Http\Controllers;

use App\Models\DocumentHandover;
use App\Models\DocumentHandoverItem;
use App\Models\Company;
use Illuminate\Http\Request;

class DocumentHandoverController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(DocumentHandover::class)->with('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('handover_number', 'like', "%{$search}%")
                  ->orWhere('receiver_name', 'like', "%{$search}%")
                  ->orWhere('receiver_entity', 'like', "%{$search}%")
                  ->orWhere('shipment_ref', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $handovers = $query->latest()->paginate(15);

        return view('document-handovers.index', compact('handovers'));
    }

    protected const DEFAULT_ITEMS = [
        'أصل شهادة السجل التجاري',
        'أصل البطاقة الضريبية',
        'أصل بطاقة القيمة المضافة',
        'أصل البطاقة الاستيرادية',
        'أصل بوليصة الشحن (Bill of Lading)',
        'أصل الفاتورة التجارية (Commercial Invoice)',
        'أصل قائمة التعبئة (Packing List)',
        'أصل شهادة المنشأ (Certificate of Origin)',
        'أصل شهادة اليورو',
    ];

    public function create()
    {
        $handoverNumber = $this->generateHandoverNumber();
        $company = $this->tenantQuery(Company::class)->first();
        $defaultItems = self::DEFAULT_ITEMS;

        return view('document-handovers.create', compact('handoverNumber', 'company', 'defaultItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'receiver_name' => 'nullable|string|max:255',
            'receiver_role' => 'nullable|string|max:255',
            'receiver_entity' => 'nullable|string|max:255',
            'receiver_id_card' => 'nullable|string|max:100',
            'receiver_id_issuer' => 'nullable|string|max:255',
            'receiver_address' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'shipment_ref' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.document_name' => 'required|string|max:255',
            'items.*.selected' => 'nullable|boolean',
            'items.*.copies' => 'nullable|integer|min:0',
            'items.*.notes' => 'nullable|string|max:255',
            'items.*.sort_order' => 'nullable|integer',
        ]);

        $handover = DocumentHandover::create([
            'tenant_id' => $this->getTenantId(),
            'handover_number' => $this->generateHandoverNumber(),
            'date' => $validated['date'],
            'receiver_name' => $validated['receiver_name'] ?? null,
            'receiver_role' => $validated['receiver_role'] ?? null,
            'receiver_entity' => $validated['receiver_entity'] ?? null,
            'receiver_id_card' => $validated['receiver_id_card'] ?? null,
            'receiver_id_issuer' => $validated['receiver_id_issuer'] ?? null,
            'receiver_address' => $validated['receiver_address'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'shipment_ref' => $validated['shipment_ref'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        $this->syncItems($handover, $validated['items']);

        return redirect()->route('document-handovers.show', $handover)
            ->with('success', 'تم إنشاء حافظة المستندات بنجاح');
    }

    public function show(DocumentHandover $documentHandover)
    {
        if ($documentHandover->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $documentHandover->load(['items', 'creator']);

        return view('document-handovers.show', compact('documentHandover'));
    }

    public function edit(DocumentHandover $documentHandover)
    {
        if ($documentHandover->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $documentHandover->load('items');

        return view('document-handovers.edit', compact('documentHandover'));
    }

    public function update(Request $request, DocumentHandover $documentHandover)
    {
        if ($documentHandover->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'receiver_name' => 'nullable|string|max:255',
            'receiver_role' => 'nullable|string|max:255',
            'receiver_entity' => 'nullable|string|max:255',
            'receiver_id_card' => 'nullable|string|max:100',
            'receiver_id_issuer' => 'nullable|string|max:255',
            'receiver_address' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'shipment_ref' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.document_name' => 'required|string|max:255',
            'items.*.selected' => 'nullable|boolean',
            'items.*.copies' => 'nullable|integer|min:0',
            'items.*.notes' => 'nullable|string|max:255',
            'items.*.sort_order' => 'nullable|integer',
        ]);

        $documentHandover->update([
            'date' => $validated['date'],
            'receiver_name' => $validated['receiver_name'] ?? null,
            'receiver_role' => $validated['receiver_role'] ?? null,
            'receiver_entity' => $validated['receiver_entity'] ?? null,
            'receiver_id_card' => $validated['receiver_id_card'] ?? null,
            'receiver_id_issuer' => $validated['receiver_id_issuer'] ?? null,
            'receiver_address' => $validated['receiver_address'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'shipment_ref' => $validated['shipment_ref'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->syncItems($documentHandover, $validated['items']);

        return redirect()->route('document-handovers.show', $documentHandover)
            ->with('success', 'تم تحديث حافظة المستندات بنجاح');
    }

    public function destroy(DocumentHandover $documentHandover)
    {
        if ($documentHandover->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $documentHandover->delete();

        return redirect()->route('document-handovers.index')
            ->with('success', 'تم حذف حافظة المستندات بنجاح');
    }

    public function print(DocumentHandover $documentHandover)
    {
        if ($documentHandover->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $documentHandover->load('items');
        $company = $this->tenantQuery(Company::class)->first();
        $items = $documentHandover->items->where('selected', true)->values();

        return response()
            ->view('document-handovers.print', compact('documentHandover', 'company', 'items'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    protected function syncItems(DocumentHandover $handover, array $items): void
    {
        $clean = array_values(array_filter($items, fn($item) => !empty($item['document_name'])));

        $handover->items()->delete();

        foreach ($clean as $index => $item) {
            DocumentHandoverItem::create([
                'handover_id' => $handover->id,
                'document_name' => $item['document_name'],
                'copies' => $item['copies'] ?? 0,
                'notes' => $item['notes'] ?? null,
                'selected' => !empty($item['selected']),
                'sort_order' => $item['sort_order'] ?? $index,
            ]);
        }
    }

    protected function generateHandoverNumber(): string
    {
        $year = date('Y');
        $prefix = 'HVN-' . $year . '-';
        $last = $this->tenantQuery(DocumentHandover::class)
            ->withTrashed()
            ->where('handover_number', 'like', $prefix . '%')
            ->max('handover_number');

        if ($last) {
            $lastSequence = (int) substr($last, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}