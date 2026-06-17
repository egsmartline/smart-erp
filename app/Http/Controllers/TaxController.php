<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $query = Tax::where('tenant_id', Auth::user()->tenant_id);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $taxes = $query->with('account', 'purchaseAccount', 'taxGroup')->orderBy('code')->paginate(20);

        return view('taxes.index', compact('taxes'));
    }

    public function create()
    {
        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_header', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $taxGroups = Tax::where('tenant_id', Auth::user()->tenant_id)
            ->where('type', 'group')
            ->orderBy('name')
            ->get();

        return view('taxes.create', compact('accounts', 'taxGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:taxes,code,NULL,id,tenant_id,' . Auth::user()->tenant_id,
            'type' => 'required|in:fixed,percentage,group',
            'amount_type' => 'required|in:fixed,percent,group,division',
            'rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_included_in_price' => 'boolean',
            'tax_group_id' => 'nullable|exists:taxes,id',
            'account_id' => 'nullable|exists:accounts,id',
            'purchase_account_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['tenant_id'] = Auth::user()->tenant_id;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['is_included_in_price'] = $request->boolean('is_included_in_price', false);

        Tax::create($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'تم إنشاء الضريبة بنجاح');
    }

    public function show(Tax $tax)
    {
        $tax->load('account', 'purchaseAccount', 'taxGroup', 'childTaxes');

        return view('taxes.show', compact('tax'));
    }

    public function edit(Tax $tax)
    {
        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_header', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $taxGroups = Tax::where('tenant_id', Auth::user()->tenant_id)
            ->where('type', 'group')
            ->where('id', '!=', $tax->id)
            ->orderBy('name')
            ->get();

        return view('taxes.edit', compact('tax', 'accounts', 'taxGroups'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:taxes,code,' . $tax->id . ',id,tenant_id,' . Auth::user()->tenant_id,
            'type' => 'required|in:fixed,percentage,group',
            'amount_type' => 'required|in:fixed,percent,group,division',
            'rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_included_in_price' => 'boolean',
            'tax_group_id' => 'nullable|exists:taxes,id',
            'account_id' => 'nullable|exists:accounts,id',
            'purchase_account_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['is_included_in_price'] = $request->boolean('is_included_in_price', false);

        $tax->update($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'تم تحديث الضريبة بنجاح');
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->route('taxes.index')
            ->with('success', 'تم حذف الضريبة بنجاح');
    }
}
