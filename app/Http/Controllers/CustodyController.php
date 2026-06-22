<?php

namespace App\Http\Controllers;

use App\Models\Custody;
use App\Models\Employee;
use App\Models\CashTreasury;
use App\Models\Currency;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustodyController extends TenantAwareController
{
    public function index(Request $request)
    {
        $query = $this->tenantQuery(Custody::class)->with('employee', 'account');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $custodies = $query->latest()->paginate(20)->withQueryString();
        $employees = $this->tenantQuery(Employee::class)->active()->orderBy('first_name')->get();

        return view('custodies.index', compact('custodies', 'employees'));
    }

    public function create()
    {
        $employees = $this->tenantQuery(Employee::class)->active()->orderBy('first_name')->get();
        $treasuries = $this->tenantQuery(CashTreasury::class)->where('is_active', true)->orderBy('name')->get();
        $currencies = $this->tenantQuery(Currency::class)->get();
        $accounts = $this->tenantQuery(Account::class)->where('is_active', true)->orderBy('name')->get();

        return view('custodies.create', compact('employees', 'treasuries', 'currencies', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'treasury_id' => 'nullable|exists:cash_treasuries,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validated['treasury_id']) {
            $treasury = CashTreasury::findOrFail($validated['treasury_id']);
            $this->authorizeTenant($treasury);
            if ($treasury->current_balance < $validated['amount']) {
                return back()->withErrors(['amount' => 'الرصيد غير كافٍ في الخزينة'])->withInput();
            }
        }

        $validated['tenant_id'] = $this->getTenantId();
        $validated['custody_number'] = $this->generateCustodyNumber();
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'active';

        DB::transaction(function () use ($validated) {
            $custody = Custody::create($validated);

            if ($custody->treasury_id) {
                $custody->treasury()->decrement('current_balance', $custody->amount);
            }

            if ($custody->account_id) {
                $journalService = app(JournalService::class);
                $lines = $journalService->buildCustodyLines($custody->toArray());
                if (count($lines) === 2) {
                    $journalService->createEntry([
                        'tenant_id' => $custody->tenant_id,
                        'date' => $custody->date->format('Y-m-d'),
                        'description' => 'عهدة - ' . $custody->custody_number,
                        'reference' => $custody->custody_number,
                        'type' => 'custody',
                        'lines' => $lines,
                    ]);
                }
            }
        });

        return redirect()->route('custodies.index')->with('success', 'تم إنشاء العهدة بنجاح');
    }

    public function show(Custody $custody)
    {
        $this->authorizeTenant($custody);
        $custody->load(['employee', 'treasury', 'currency', 'account', 'user']);

        return view('custodies.show', compact('custody'));
    }

    public function settle(Custody $custody)
    {
        $this->authorizeTenant($custody);

        if ($custody->status === 'settled') {
            return redirect()->route('custodies.show', $custody)
                ->with('error', 'هذه العهدة مصفاة بالفعل');
        }

        $treasuries = $this->tenantQuery(CashTreasury::class)->where('is_active', true)->orderBy('name')->get();
        $currencies = $this->tenantQuery(Currency::class)->get();
        $accounts = $this->tenantQuery(Account::class)->where('is_active', true)->orderBy('name')->get();

        return view('custodies.settle', compact('custody', 'treasuries', 'currencies', 'accounts'));
    }

    public function processSettlement(Request $request, Custody $custody)
    {
        $this->authorizeTenant($custody);

        if ($custody->status === 'settled') {
            return redirect()->route('custodies.show', $custody)
                ->with('error', 'هذه العهدة مصفاة بالفعل');
        }

        $validated = $request->validate([
            'returned_amount' => 'required|numeric|min:0',
            'settlement_date' => 'required|date',
            'treasury_id' => 'nullable|exists:cash_treasuries,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'notes' => 'nullable|string',
        ]);

        if ((float) $validated['returned_amount'] > (float) $custody->amount) {
            return back()->withErrors(['returned_amount' => 'المردود لا يمكن أن يتجاوز المبلغ الأصلي'])->withInput();
        }

        if ((float) $validated['returned_amount'] >= (float) $custody->amount) {
            $validated['status'] = 'settled';
        } elseif ((float) $validated['returned_amount'] > 0) {
            $validated['status'] = 'partial';
        } else {
            $validated['status'] = 'active';
        }

        DB::transaction(function () use ($custody, $validated) {
            $oldReturned = $custody->returned_amount;
            $custody->update($validated);

            $treasuryId = $validated['treasury_id'] ?? $custody->treasury_id;
            if ($treasuryId) {
                $treasury = CashTreasury::find($treasuryId);
                if ($treasury) {
                    $this->authorizeTenant($treasury);
                    $difference = $validated['returned_amount'] - $oldReturned;
                    $treasury->increment('current_balance', $difference);
                }
            }

            $accountId = $validated['account_id'] ?? $custody->account_id;
            $diff = $validated['returned_amount'] - $oldReturned;
            if ($accountId && $diff > 0) {
                $journalService = app(JournalService::class);
                $lines = $journalService->buildCustodySettlementLines([
                    'treasury_id' => $treasuryId,
                    'account_id' => $accountId,
                ], $diff);
                if (count($lines) === 2) {
                    $journalService->createEntry([
                        'tenant_id' => $custody->tenant_id,
                        'date' => $validated['settlement_date'],
                        'description' => 'تسوية عهدة - ' . $custody->custody_number,
                        'reference' => $custody->custody_number,
                        'type' => 'custody_settlement',
                        'lines' => $lines,
                    ]);
                }
            }
        });

        return redirect()->route('custodies.show', $custody)
            ->with('success', 'تم تسوية العهدة بنجاح');
    }

    public function edit(Custody $custody)
    {
        $this->authorizeTenant($custody);
        $employees = $this->tenantQuery(Employee::class)->active()->orderBy('first_name')->get();
        $treasuries = $this->tenantQuery(CashTreasury::class)->where('is_active', true)->orderBy('name')->get();
        $currencies = $this->tenantQuery(Currency::class)->get();
        $accounts = $this->tenantQuery(Account::class)->where('is_active', true)->orderBy('name')->get();

        return view('custodies.edit', compact('custody', 'employees', 'treasuries', 'currencies', 'accounts'));
    }

    public function update(Request $request, Custody $custody)
    {
        $this->authorizeTenant($custody);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0.01',
            'returned_amount' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'treasury_id' => 'nullable|exists:cash_treasuries,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'status' => 'required|in:active,settled,partial',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['returned_amount'] = $validated['returned_amount'] ?? 0;

        if ((float) $validated['returned_amount'] > (float) $validated['amount']) {
            return back()->withErrors(['returned_amount' => 'المردود لا يمكن أن يتجاوز المبلغ'])->withInput();
        }

        if ((float) $validated['returned_amount'] >= (float) $validated['amount']) {
            $validated['status'] = 'settled';
        } elseif ((float) $validated['returned_amount'] > 0) {
            $validated['status'] = 'partial';
        }

        DB::transaction(function () use ($custody, $validated) {
            $oldAmount = $custody->amount;
            $oldReturned = $custody->returned_amount;
            $oldTreasuryId = $custody->treasury_id;
            $newTreasuryId = $validated['treasury_id'];

            $custody->update($validated);

            if ($oldTreasuryId) {
                $oldTreasury = CashTreasury::find($oldTreasuryId);
                if ($oldTreasury) {
                    $this->authorizeTenant($oldTreasury);
                    $oldTreasury->increment('current_balance', $oldAmount - $oldReturned);
                }
            }

            if ($newTreasuryId) {
                $newTreasury = CashTreasury::find($newTreasuryId);
                if ($newTreasury) {
                    $this->authorizeTenant($newTreasury);
                    $netNew = $validated['amount'] - $validated['returned_amount'];
                    $newTreasury->decrement('current_balance', $netNew);
                }
            }
        });

        return redirect()->route('custodies.index')->with('success', 'تم تحديث العهدة بنجاح');
    }

    public function destroy(Custody $custody)
    {
        $this->authorizeTenant($custody);

        DB::transaction(function () use ($custody) {
            if ($custody->treasury_id) {
                $remaining = $custody->amount - $custody->returned_amount;
                $custody->treasury()->increment('current_balance', $remaining);
            }

            if ($custody->account_id) {
                $journalEntry = JournalEntry::where('reference', $custody->custody_number)->where('type', 'custody')->first();
                if ($journalEntry) {
                    app(JournalService::class)->reverseEntry($journalEntry);
                }
            }

            $custody->delete();
        });

        return redirect()->route('custodies.index')->with('success', 'تم حذف العهدة بنجاح');
    }

    protected function generateCustodyNumber(): string
    {
        $prefix = 'CTD-' . date('Y') . '-';
        $last = $this->tenantQuery(Custody::class)
            ->where('custody_number', 'like', $prefix . '%')
            ->max('custody_number');

        if ($last) {
            $seq = (int) substr($last, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    protected function authorizeTenant($model): void
    {
        if ($model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
