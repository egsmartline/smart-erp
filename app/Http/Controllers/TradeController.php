<?php

namespace App\Http\Controllers;

use App\Models\TradeOperation;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Currency;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    private function getTenantId()
    {
        return session('current_tenant_id') ?? auth()->user()->tenant_id;
    }

    public function index(Request $request)
    {
        $tenantId = $this->getTenantId();
        $query = TradeOperation::forTenant();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('operation_number', 'like', "%{$s}%")
                  ->orWhere('party_name', 'like', "%{$s}%")
                  ->orWhere('container_number', 'like', "%{$s}%")
                  ->orWhere('vessel_name', 'like', "%{$s}%")
                  ->orWhere('bill_of_lading_number', 'like', "%{$s}%")
                  ->orWhere('lc_number', 'like', "%{$s}%");
            });
        }

        $operations = $query->orderBy('created_at', 'desc')->paginate(20);
        $importCount = TradeOperation::forTenant()->importOperation()->count();
        $exportCount = TradeOperation::forTenant()->exportOperation()->count();
        $activeCount = TradeOperation::forTenant()->whereNotIn('status', ['completed', 'cancelled'])->count();

        return view('trade.index', compact('operations', 'importCount', 'exportCount', 'activeCount'));
    }

    public function create(Request $request)
    {
        $tenantId = $this->getTenantId();
        $type = $request->type ?? 'import';
        $customers = Customer::where('tenant_id', $tenantId)->get();
        $suppliers = Supplier::where('tenant_id', $tenantId)->get();
        $currencies = Currency::where('tenant_id', $tenantId)->get();
        $operationNumber = TradeOperation::generateNumber($type);

        return view('trade.create', compact('type', 'customers', 'suppliers', 'currencies', 'operationNumber'));
    }

    public function store(Request $request)
    {
        $tenantId = $this->getTenantId();

        $data = $request->validate([
            'type' => 'required|in:import,export',
            'date' => 'required|date',
            'party_id' => 'nullable|integer',
            'party_type' => 'nullable|string',
            'party_name' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'port_of_loading' => 'nullable|string|max:255',
            'port_of_discharge' => 'nullable|string|max:255',
            'incoterm' => 'nullable|string|max:50',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'total_value' => 'nullable|numeric|min:0',
            'shipping_method' => 'nullable|string|max:255',
            'container_number' => 'nullable|string|max:255',
            'vessel_name' => 'nullable|string|max:255',
            'bill_of_lading_number' => 'nullable|string|max:255',
            'etd_date' => 'nullable|date',
            'eta_date' => 'nullable|date',
            'lc_number' => 'nullable|string|max:255',
            'lc_issuing_bank' => 'nullable|string|max:255',
            'lc_beneficiary_bank' => 'nullable|string|max:255',
            'lc_type' => 'nullable|in:sight,deferred,standby',
            'lc_amount' => 'nullable|numeric|min:0',
            'lc_issue_date' => 'nullable|date',
            'lc_expiry_date' => 'nullable|date',
            'customs_value' => 'nullable|numeric|min:0',
            'customs_duty_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'insurance_cost' => 'nullable|numeric|min:0',
            'inspection_cost' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['tenant_id'] = $tenantId;
        $data['operation_number'] = TradeOperation::generateNumber($data['type']);
        $data['created_by'] = auth()->id();

        if ($data['party_id'] && $data['party_type'] === 'customer') {
            $customer = Customer::find($data['party_id']);
            $data['party_name'] = $customer?->name;
        } elseif ($data['party_id'] && $data['party_type'] === 'supplier') {
            $supplier = Supplier::find($data['party_id']);
            $data['party_name'] = $supplier?->name;
        }

        TradeOperation::create($data);

        return redirect()->route('trade.index')->with('success', 'تم إنشاء العملية بنجاح');
    }

    public function show(TradeOperation $tradeOperation)
    {
        if ($tradeOperation->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
        return view('trade.show', compact('tradeOperation'));
    }

    public function edit(TradeOperation $tradeOperation)
    {
        if ($tradeOperation->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
        $tenantId = $this->getTenantId();
        $customers = Customer::where('tenant_id', $tenantId)->get();
        $suppliers = Supplier::where('tenant_id', $tenantId)->get();
        $currencies = Currency::where('tenant_id', $tenantId)->get();
        return view('trade.edit', compact('tradeOperation', 'customers', 'suppliers', 'currencies'));
    }

    public function update(Request $request, TradeOperation $tradeOperation)
    {
        if ($tradeOperation->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $data = $request->validate([
            'date' => 'required|date',
            'party_id' => 'nullable|integer',
            'party_type' => 'nullable|string',
            'party_name' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'port_of_loading' => 'nullable|string|max:255',
            'port_of_discharge' => 'nullable|string|max:255',
            'incoterm' => 'nullable|string|max:50',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'total_value' => 'nullable|numeric|min:0',
            'shipping_method' => 'nullable|string|max:255',
            'container_number' => 'nullable|string|max:255',
            'vessel_name' => 'nullable|string|max:255',
            'bill_of_lading_number' => 'nullable|string|max:255',
            'etd_date' => 'nullable|date',
            'eta_date' => 'nullable|date',
            'lc_number' => 'nullable|string|max:255',
            'lc_issuing_bank' => 'nullable|string|max:255',
            'lc_beneficiary_bank' => 'nullable|string|max:255',
            'lc_type' => 'nullable|in:sight,deferred,standby',
            'lc_amount' => 'nullable|numeric|min:0',
            'lc_issue_date' => 'nullable|date',
            'lc_expiry_date' => 'nullable|date',
            'customs_value' => 'nullable|numeric|min:0',
            'customs_duty_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'insurance_cost' => 'nullable|numeric|min:0',
            'inspection_cost' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($data['party_id'] && $data['party_type'] === 'customer') {
            $customer = Customer::find($data['party_id']);
            $data['party_name'] = $customer?->name;
        } elseif ($data['party_id'] && $data['party_type'] === 'supplier') {
            $supplier = Supplier::find($data['party_id']);
            $data['party_name'] = $supplier?->name;
        }

        $tradeOperation->update($data);

        return redirect()->route('trade.index')->with('success', 'تم تحديث العملية بنجاح');
    }

    public function destroy(TradeOperation $tradeOperation)
    {
        if ($tradeOperation->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
        $tradeOperation->delete();
        return redirect()->route('trade.index')->with('success', 'تم حذف العملية بنجاح');
    }

    public function updateStatus(Request $request, TradeOperation $tradeOperation)
    {
        if ($tradeOperation->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
        $request->validate(['status' => 'required|in:draft,confirmed,shipped,cleared,completed,cancelled']);
        $tradeOperation->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'تم تحديث الحالة بنجاح');
    }

    public function print(TradeOperation $tradeOperation)
    {
        if ($tradeOperation->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
        return view('trade.print', compact('tradeOperation'));
    }
}