<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::where('tenant_id', Auth::user()->tenant_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $accounts = $query->with('parent')->orderBy('code')->get();

        $accountTypes = [
            'asset' => 'أصول',
            'liability' => 'خصوم',
            'equity' => 'حقوق ملكية',
            'revenue' => 'إيرادات',
            'expense' => 'مصروفات',
        ];

        return view('accounts.index', compact('accounts', 'accountTypes'));
    }

    public function create()
    {
        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $accountTypes = [
            'asset' => 'أصول',
            'liability' => 'خصوم',
            'equity' => 'حقوق ملكية',
            'revenue' => 'إيرادات',
            'expense' => 'مصروفات',
        ];

        return view('accounts.create', compact('accounts', 'accountTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:chart_of_accounts,code,NULL,id,tenant_id,' . Auth::user()->tenant_id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'sub_type' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'opening_balance' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = Auth::user()->tenant_id;
        $validated['current_balance'] = $validated['opening_balance'];
        $validated['is_active'] = $request->boolean('is_active', true);

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'تم إنشاء الحساب بنجاح');
    }

    public function show(Account $account)
    {
        $this->authorizeAccount($account);

        $allLines = JournalEntryLine::where('account_id', $account->id)
            ->with('journalEntry')
            ->orderBy('id')
            ->get();

        $isDebitNature = in_array($account->type, ['asset', 'expense']);
        $runningBalance = $account->opening_balance;

        foreach ($allLines as $line) {
            if ($isDebitNature) {
                $runningBalance += ($line->debit - $line->credit);
            } else {
                $runningBalance += ($line->credit - $line->debit);
            }
            $line->running_balance = $runningBalance;
        }

        $perPage = 25;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $journalLines = new \Illuminate\Pagination\LengthAwarePaginator(
            $allLines->slice($offset, $perPage)->values(),
            $allLines->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('accounts.show', compact('account', 'journalLines'));
    }

    public function edit(Account $account)
    {
        $this->authorizeAccount($account);

        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->where('id', '!=', $account->id)
            ->orderBy('code')
            ->get();

        $accountTypes = [
            'asset' => 'أصول',
            'liability' => 'خصوم',
            'equity' => 'حقوق ملكية',
            'revenue' => 'إيرادات',
            'expense' => 'مصروفات',
        ];

        return view('accounts.edit', compact('account', 'accounts', 'accountTypes'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:chart_of_accounts,code,' . $account->id . ',id,tenant_id,' . Auth::user()->tenant_id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'sub_type' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'opening_balance' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $validated['current_balance'] = $validated['opening_balance'];

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'تم تحديث الحساب بنجاح');
    }

    public function destroy(Account $account)
    {
        $this->authorizeAccount($account);

        $hasLines = JournalEntryLine::where('account_id', $account->id)->exists();

        if ($hasLines) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف هذا الحساب لارتباطه بقيود يومية');
        }

        $hasChildren = Account::where('parent_id', $account->id)->exists();

        if ($hasChildren) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف هذا الحساب لارتباطه بحسابات فرعية');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'تم حذف الحساب بنجاح');
    }

    public function toggleStatus(Account $account)
    {
        $this->authorizeAccount($account);

        $account->update(['is_active' => !$account->is_active]);

        $status = $account->is_active ? 'تفعيل' : 'إلغاء تفعيل';

        return redirect()->back()
            ->with('success', "تم {$status} الحساب بنجاح");
    }

    private function authorizeAccount(Account $account): void
    {
        if ($account->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'غير مصرح לך بالوصول لهذا الحساب');
        }
    }
}
