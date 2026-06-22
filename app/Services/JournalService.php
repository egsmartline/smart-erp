<?php

namespace App\Services;

use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public function createEntry(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            $entry = JournalEntry::create([
                'tenant_id' => $data['tenant_id'],
                'entry_number' => $this->generateEntryNumber($data['tenant_id'], $data['date']),
                'date' => $data['date'],
                'description' => $data['description'],
                'reference' => $data['reference'] ?? null,
                'fiscal_year_id' => $this->getFiscalYearId($data['tenant_id'], $data['date']),
                'type' => $data['type'] ?? 'general',
                'is_posted' => true,
                'posted_by' => Auth::id(),
                'posted_at' => now(),
                'created_by' => Auth::id(),
            ]);

            foreach ($data['lines'] as $line) {
                JournalEntryLine::create([
                    'tenant_id' => $data['tenant_id'],
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                ]);

                $account = Account::find($line['account_id']);
                if ($account) {
                    $newBalance = $account->current_balance + ($line['debit'] ?? 0) - ($line['credit'] ?? 0);
                    $account->update(['current_balance' => $newBalance]);
                }
            }

            return $entry;
        });
    }

    public function reverseEntry(JournalEntry $entry): void
    {
        DB::transaction(function () use ($entry) {
            foreach ($entry->lines as $line) {
                $account = Account::find($line->account_id);
                if ($account) {
                    $newBalance = $account->current_balance - $line->debit + $line->credit;
                    $account->update(['current_balance' => $newBalance]);
                }
            }
            $entry->lines()->delete();
            $entry->delete();
        });
    }

    public function getAccountByCode(int $tenantId, string $code): ?Account
    {
        return Account::where('tenant_id', $tenantId)->where('code', $code)->first();
    }

    public function reverseEntryByReference(string $reference, ?string $type = null): void
    {
        $query = JournalEntry::where('reference', $reference);
        if ($type) {
            $query->where('type', $type);
        }
        $entry = $query->first();
        if ($entry) {
            $this->reverseEntry($entry);
        }
    }

    public function buildSalesInvoiceLines(array $invoiceData, int $tenantId): array
    {
        $lines = [];
        $arAccount = $this->getAccountByCode($tenantId, '1103');
        $revenueAccount = $this->getAccountByCode($tenantId, '41');
        $taxAccount = $this->getAccountByCode($tenantId, '2102');

        if ($arAccount) {
            $lines[] = ['account_id' => $arAccount->id, 'debit' => $invoiceData['total'], 'credit' => 0];
        }

        if ($revenueAccount) {
            $revenueAmount = $invoiceData['subtotal'] - ($invoiceData['discount_amount'] ?? 0);
            $lines[] = ['account_id' => $revenueAccount->id, 'debit' => 0, 'credit' => $revenueAmount];
        }

        if ($taxAccount && ($invoiceData['tax_amount'] ?? 0) > 0) {
            $lines[] = ['account_id' => $taxAccount->id, 'debit' => 0, 'credit' => $invoiceData['tax_amount']];
        }

        return $lines;
    }

    public function buildPurchaseInvoiceLines(array $invoiceData, int $tenantId): array
    {
        $lines = [];
        $apAccount = $this->getAccountByCode($tenantId, '2101');
        $inventoryAccount = $this->getAccountByCode($tenantId, '1104');
        $taxAccount = $this->getAccountByCode($tenantId, '2102');

        $netAmount = $invoiceData['subtotal'] - ($invoiceData['discount_amount'] ?? 0);

        if ($inventoryAccount) {
            $lines[] = ['account_id' => $inventoryAccount->id, 'debit' => $netAmount, 'credit' => 0];
        }

        if ($taxAccount && ($invoiceData['tax_amount'] ?? 0) > 0) {
            $lines[] = ['account_id' => $taxAccount->id, 'debit' => $invoiceData['tax_amount'], 'credit' => 0];
        }

        if ($apAccount) {
            $total = $netAmount + ($invoiceData['tax_amount'] ?? 0) + ($invoiceData['shipping_cost'] ?? 0);
            $lines[] = ['account_id' => $apAccount->id, 'debit' => 0, 'credit' => $total];
        }

        return $lines;
    }

    private function generateEntryNumber(int $tenantId, string $date): string
    {
        $year = date('Y', strtotime($date));
        $lastEntry = JournalEntry::where('tenant_id', $tenantId)
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

    private function getFiscalYearId(int $tenantId, string $date): ?int
    {
        $fiscalYear = FiscalYear::where('tenant_id', $tenantId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        return $fiscalYear?->id;
    }

    public function buildPaymentLines(array $paymentData): array
    {
        $lines = [];

        if ($paymentData['type'] === 'receipt') {
            if ($paymentData['payment_method'] === 'cash' && $paymentData['treasury_id']) {
                $treasury = \App\Models\CashTreasury::find($paymentData['treasury_id']);
                $lines[] = ['account_id' => $treasury->account_id, 'debit' => $paymentData['amount'], 'credit' => 0];
            } elseif ($paymentData['payment_method'] === 'bank_transfer' && $paymentData['bank_account_id']) {
                $bank = \App\Models\BankAccount::find($paymentData['bank_account_id']);
                $lines[] = ['account_id' => $bank->account_id, 'debit' => $paymentData['amount'], 'credit' => 0];
            }
            if ($paymentData['account_id']) {
                $lines[] = ['account_id' => $paymentData['account_id'], 'debit' => 0, 'credit' => $paymentData['amount']];
            }
        } else {
            if ($paymentData['account_id']) {
                $lines[] = ['account_id' => $paymentData['account_id'], 'debit' => $paymentData['amount'], 'credit' => 0];
            }
            if ($paymentData['payment_method'] === 'cash' && $paymentData['treasury_id']) {
                $treasury = \App\Models\CashTreasury::find($paymentData['treasury_id']);
                $lines[] = ['account_id' => $treasury->account_id, 'debit' => 0, 'credit' => $paymentData['amount']];
            } elseif ($paymentData['payment_method'] === 'bank_transfer' && $paymentData['bank_account_id']) {
                $bank = \App\Models\BankAccount::find($paymentData['bank_account_id']);
                $lines[] = ['account_id' => $bank->account_id, 'debit' => 0, 'credit' => $paymentData['amount']];
            }
        }

        return $lines;
    }

    public function buildCustodyLines(array $custodyData): array
    {
        $lines = [];

        if ($custodyData['account_id']) {
            $lines[] = ['account_id' => $custodyData['account_id'], 'debit' => $custodyData['amount'], 'credit' => 0];
        }

        if ($custodyData['treasury_id']) {
            $treasury = \App\Models\CashTreasury::find($custodyData['treasury_id']);
            if ($treasury && $treasury->account_id) {
                $lines[] = ['account_id' => $treasury->account_id, 'debit' => 0, 'credit' => $custodyData['amount']];
            }
        }

        return $lines;
    }

    public function buildCustodySettlementLines(array $custodyData, float $returnedAmount): array
    {
        $lines = [];

        if ($custodyData['treasury_id']) {
            $treasury = \App\Models\CashTreasury::find($custodyData['treasury_id']);
            if ($treasury && $treasury->account_id) {
                $lines[] = ['account_id' => $treasury->account_id, 'debit' => $returnedAmount, 'credit' => 0];
            }
        }

        if ($custodyData['account_id']) {
            $lines[] = ['account_id' => $custodyData['account_id'], 'debit' => 0, 'credit' => $returnedAmount];
        }

        return $lines;
    }
}