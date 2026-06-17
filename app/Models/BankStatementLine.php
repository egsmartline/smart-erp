<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankStatementLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'bank_statement_id',
        'date',
        'description',
        'payment_type',
        'amount',
        'balance',
        'partner_type',
        'partner_id',
        'currency_id',
        'exchange_rate',
        'is_reconciled',
        'reconciled_date',
        'journal_entry_line_id',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
            'is_reconciled' => 'boolean',
            'reconciled_date' => 'date',
        ];
    }

    public function scopeForTenant($query, ?int $tenantId = null)
    {
        return $query->where('tenant_id', $tenantId ?? tenant('id'));
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bankStatement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class);
    }

    public function journalEntryLine(): BelongsTo
    {
        return $this->belongsTo(JournalEntryLine::class);
    }
}
