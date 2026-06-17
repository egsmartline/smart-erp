<?php

namespace App\Http\Controllers;

use App\Models\AnalyticalAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticalAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = AnalyticalAccount::where('tenant_id', Auth::user()->tenant_id);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $analyticalAccounts = $query->with('parent')->orderBy('code')->paginate(20);

        $types = [
            'cost_center' => 'مركز تكلفة',
            'profit_center' => 'مركز ربح',
            'project' => 'مشروع',
            'department' => 'قسم',
        ];

        return view('analytical-accounts.index', compact('analyticalAccounts', 'types'));
    }

    public function create()
    {
        $parentAccounts = AnalyticalAccount::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $types = [
            'cost_center' => 'مركز تكلفة',
            'profit_center' => 'مركز ربح',
            'project' => 'مشروع',
            'department' => 'قسم',
        ];

        return view('analytical-accounts.create', compact('parentAccounts', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:analytical_accounts,code,NULL,id,tenant_id,' . Auth::user()->tenant_id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:cost_center,profit_center,project,department',
            'parent_id' => 'nullable|exists:analytical_accounts,id',
            'budget_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = Auth::user()->tenant_id;
        $validated['current_amount'] = 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        AnalyticalAccount::create($validated);

        return redirect()->route('analytical-accounts.index')
            ->with('success', 'تم إنشاء الحساب التحليلي بنجاح');
    }

    public function show(AnalyticalAccount $analyticalAccount)
    {
        $this->authorizeAccount($analyticalAccount);

        $utilization = $analyticalAccount->budget_amount > 0
            ? ($analyticalAccount->current_amount / $analyticalAccount->budget_amount) * 100
            : 0;

        return view('analytical-accounts.show', compact('analyticalAccount', 'utilization'));
    }

    public function edit(AnalyticalAccount $analyticalAccount)
    {
        $this->authorizeAccount($analyticalAccount);

        $parentAccounts = AnalyticalAccount::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->where('id', '!=', $analyticalAccount->id)
            ->orderBy('code')
            ->get();

        $types = [
            'cost_center' => 'مركز تكلفة',
            'profit_center' => 'مركز ربح',
            'project' => 'مشروع',
            'department' => 'قسم',
        ];

        return view('analytical-accounts.edit', compact('analyticalAccount', 'parentAccounts', 'types'));
    }

    public function update(Request $request, AnalyticalAccount $analyticalAccount)
    {
        $this->authorizeAccount($analyticalAccount);

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:analytical_accounts,code,' . $analyticalAccount->id . ',id,tenant_id,' . Auth::user()->tenant_id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:cost_center,profit_center,project,department',
            'parent_id' => 'nullable|exists:analytical_accounts,id',
            'budget_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $analyticalAccount->update($validated);

        return redirect()->route('analytical-accounts.index')
            ->with('success', 'تم تحديث الحساب التحليلي بنجاح');
    }

    public function destroy(AnalyticalAccount $analyticalAccount)
    {
        $this->authorizeAccount($analyticalAccount);

        $hasChildren = AnalyticalAccount::where('parent_id', $analyticalAccount->id)->exists();

        if ($hasChildren) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف هذا الحساب لارتباطه بحسابات فرعية');
        }

        $analyticalAccount->delete();

        return redirect()->route('analytical-accounts.index')
            ->with('success', 'تم حذف الحساب التحليلي بنجاح');
    }

    private function authorizeAccount(AnalyticalAccount $analyticalAccount): void
    {
        if ($analyticalAccount->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'غير مصرح לך بالوصول لهذا الحساب');
        }
    }
}
