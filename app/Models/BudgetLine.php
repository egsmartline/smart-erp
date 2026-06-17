<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'budget_id',
        'account_id',
        'analytical_account_id',
        'planned_amount',
        'actual_amount',
    ];

    protected function casts(): array
    {
        return [
            'planned_amount' => 'decimal:2',
            'actual_amount' => 'decimal:2',
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

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function analyticalAccount(): BelongsTo
    {
        return $this->belongsTo(AnalyticalAccount::class);
    }
}
