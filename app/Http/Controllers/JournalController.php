<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = Journal::where('tenant_id', Auth::user()->tenant_id)->with('defaultAccount', 'currency');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $journals = $query->orderBy('code')->paginate(20)->withQueryString();

        return view('journals.index', compact('journals'));
    }

    public function create()
    {
        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_header', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $currencies = Currency::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('journals.create', compact('accounts', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:journals,code,NULL,id,tenant_id,' . Auth::user()->tenant_id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:sale,purchase,cash,bank,general',
            'default_account_id' => 'nullable|exists:accounts,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = Auth::user()->tenant_id;
        $validated['is_active'] = $request->boolean('is_active', true);

        Journal::create($validated);

        return redirect()->route('journals.index')
            ->with('success', 'تم إنشاء الدفتر بنجاح');
    }

    public function show(Journal $journal)
    {
        $journal->load('defaultAccount', 'currency');

        return view('journals.show', compact('journal'));
    }

    public function edit(Journal $journal)
    {
        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_header', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $currencies = Currency::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('journals.edit', compact('journal', 'accounts', 'currencies'));
    }

    public function update(Request $request, Journal $journal)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:journals,code,' . $journal->id . ',id,tenant_id,' . Auth::user()->tenant_id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:sale,purchase,cash,bank,general',
            'default_account_id' => 'nullable|exists:accounts,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $journal->update($validated);

        return redirect()->route('journals.index')
            ->with('success', 'تم تحديث الدفتر بنجاح');
    }

    public function destroy(Journal $journal)
    {
        $journal->delete();

        return redirect()->route('journals.index')
            ->with('success', 'تم حذف الدفتر بنجاح');
    }
}
