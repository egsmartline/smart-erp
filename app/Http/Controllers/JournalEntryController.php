<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = JournalEntry::where('tenant_id', Auth::user()->tenant_id)
            ->with(['lines.account', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('entry_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_posted', $request->status === 'posted');
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $entries = $query->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(25);

        return view('journal-entries.index', compact('entries'));
    }

    public function create()
    {
        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $entryNumber = $this->generateEntryNumber();

        return view('journal-entries.create', compact('accounts', 'entryNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:500',
            'reference' => 'nullable|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:500',
        ]);

        $totalDebit = collect($validated['lines'])->sum('debit');
        $totalCredit = collect($validated['lines'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.001) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'إجمالي المدين لا يساوي إجمالي الدائن');
        }

        $hasDebit = collect($validated['lines'])->contains('debit', '>', 0);
        $hasCredit = collect($validated['lines'])->contains('credit', '>', 0);

        if (!$hasDebit || !$hasCredit) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'يجب أن يحتوي القيد على مدين ودائن على الأقل');
        }

        DB::beginTransaction();

        try {
            $entry = JournalEntry::create([
                'entry_number' => $this->generateEntryNumber(),
                'date' => $validated['date'],
                'description' => $validated['description'],
                'reference' => $validated['reference'] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'is_posted' => false,
                'tenant_id' => Auth::user()->tenant_id,
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['lines'] as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['description'] ?? null,
                    'tenant_id' => Auth::user()->tenant_id,
                ]);
            }

            DB::commit();

            return redirect()->route('journal-entries.show', $entry)
                ->with('success', 'تم إنشاء القيد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء القيد: ' . $e->getMessage());
        }
    }

    public function show(JournalEntry $journalEntry)
    {
        $this->authorizeEntry($journalEntry);

        $journalEntry->load(['lines.account', 'creator']);

        return view('journal-entries.show', compact('journalEntry'));
    }

    public function edit(JournalEntry $journalEntry)
    {
        $this->authorizeEntry($journalEntry);

        if ($journalEntry->is_posted) {
            return redirect()->back()
                ->with('error', 'لا يمكن تعديل قيد مرحل');
        }

        $journalEntry->load('lines.account');

        $accounts = Account::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('journal-entries.edit', compact('journalEntry', 'accounts'));
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        $this->authorizeEntry($journalEntry);

        if ($journalEntry->is_posted) {
            return redirect()->back()
                ->with('error', 'لا يمكن تعديل قيد مرحل');
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:500',
            'reference' => 'nullable|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:500',
        ]);

        $totalDebit = collect($validated['lines'])->sum('debit');
        $totalCredit = collect($validated['lines'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.001) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'إجمالي المدين لا يساوي إجمالي الدائن');
        }

        DB::beginTransaction();

        try {
            $journalEntry->update([
                'date' => $validated['date'],
                'description' => $validated['description'],
                'reference' => $validated['reference'] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            $journalEntry->lines()->delete();

            foreach ($validated['lines'] as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['description'] ?? null,
                    'tenant_id' => Auth::user()->tenant_id,
                ]);
            }

            DB::commit();

            return redirect()->route('journal-entries.show', $journalEntry)
                ->with('success', 'تم تحديث القيد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث القيد: ' . $e->getMessage());
        }
    }

    public function destroy(JournalEntry $journalEntry)
    {
        $this->authorizeEntry($journalEntry);

        if ($journalEntry->is_posted) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف قيد مرحل');
        }

        DB::beginTransaction();

        try {
            $journalEntry->lines()->delete();
            $journalEntry->delete();

            DB::commit();

            return redirect()->route('journal-entries.index')
                ->with('success', 'تم حذف القيد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف القيد');
        }
    }

    public function post(JournalEntry $journalEntry)
    {
        $this->authorizeEntry($journalEntry);

        if ($journalEntry->is_posted) {
            return redirect()->back()
                ->with('error', 'هذا القيد مرحل بالفعل');
        }

        DB::beginTransaction();

        try {
            $journalEntry->update(['is_posted' => true]);

            foreach ($journalEntry->lines as $line) {
                $account = Account::find($line->account_id);

                if ($account) {
                    $newBalance = $account->current_balance + $line->debit - $line->credit;
                    $account->update(['current_balance' => $newBalance]);
                }
            }

            DB::commit();

            return redirect()->route('journal-entries.show', $journalEntry)
                ->with('success', 'تم ترحيل القيد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء ترحيل القيد');
        }
    }

    private function generateEntryNumber(): string
    {
        $year = date('Y');
        $tenantId = $this->getTenantId();

        $lastEntry = JournalEntry::where('tenant_id', $tenantId)
            ->withTrashed()
            ->whereYear('date', $year)
            ->orderByDesc('entry_number')
            ->first();

        if ($lastEntry) {
            $lastNumber = (int) substr($lastEntry->entry_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'JE-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function authorizeEntry(JournalEntry $entry): void
    {
        if ($entry->tenant_id !== $this->getTenantId()) {
            abort(403, 'غير مصرح לך بالوصول لهذا القيد');
        }
    }
}
