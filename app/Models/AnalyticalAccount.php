<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnalyticalAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'type',
        'parent_id',
        'is_active',
        'budget_amount',
        'current_amount',
    ];

    protected function casts(): array
    {
        return [
            'budget_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'is_active' => 'boolean',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AnalyticalAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AnalyticalAccount::class, 'parent_id');
    }
}
