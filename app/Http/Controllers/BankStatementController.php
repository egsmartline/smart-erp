<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankStatement;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankStatementController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(BankStatement::class);

        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        $bankStatements = $query->with('bankAccount')->orderByDesc('date')->paginate(20);
        $bankAccounts = $this->tenantQuery(BankAccount::class)->where('is_active', true)->orderBy('account_name')->get();

        return view('bank-statements.index', compact('bankStatements', 'bankAccounts'));
    }

    public function create()
    {
        $bankAccounts = $this->tenantQuery(BankAccount::class)->where('is_active', true)->orderBy('account_name')->get();
        $journals = $this->tenantQuery(Journal::class)->where('type', 'bank')->where('is_active', true)->orderBy('name')->get();

        return view('bank-statements.create', compact('bankAccounts', 'journals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'journal_id' => 'nullable|exists:journals,id',
            'date' => 'required|date',
            'start_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['tenant_id'] = $this->getTenantId();
        $validated['user_id'] = auth()->id();
        $validated['state'] = 'draft';
        $validated['end_balance'] = $validated['start_balance'];
        $validated['balance_difference'] = 0;

        $year = date('Y');
        $lastStatement = $this->tenantQuery(BankStatement::class)->whereYear('date', $year)->count();
        $validated['statement_number'] = 'BS-' . $year . '-' . str_pad($lastStatement + 1, 4, '0', STR_PAD_LEFT);

        BankStatement::create($validated);

        return redirect()->route('bank-statements.index')->with('success', 'تم إنشاء كشف الحساب البنكي بنجاح');
    }

    public function show(BankStatement $bankStatement)
    {
        $bankStatement->load('bankAccount', 'journal', 'lines');

        return view('bank-statements.show', compact('bankStatement'));
    }

    public function post(BankStatement $bankStatement)
    {
        if ($bankStatement->state !== 'draft') {
            return redirect()->back()->with('error', 'لا يمكن ترحيل كشف الحساب إلا إذا كان في حالة مسودة');
        }

        $bankStatement->update([
            'state' => 'posted',
            'balance_difference' => $bankStatement->end_balance - $bankStatement->start_balance,
        ]);

        return redirect()->route('bank-statements.show', $bankStatement)
            ->with('success', 'تم ترحيل كشف الحساب بنجاح');
    }

    public function destroy(BankStatement $bankStatement)
    {
        if ($bankStatement->state !== 'draft') {
            return redirect()->back()->with('error', 'لا يمكن حذف كشف الحساب إلا إذا كان في حالة مسودة');
        }

        $bankStatement->delete();

        return redirect()->route('bank-statements.index')
            ->with('success', 'تم حذف كشف الحساب بنجاح');
    }
}
