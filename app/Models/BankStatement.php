<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankStatement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'bank_account_id',
        'journal_id',
        'statement_number',
        'date',
        'start_balance',
        'end_balance',
        'balance_difference',
        'state',
        'user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_balance' => 'decimal:2',
            'end_balance' => 'decimal:2',
            'balance_difference' => 'decimal:2',
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

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BankStatementLine::class);
    }
}
