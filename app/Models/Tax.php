<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'name_en',
        'code',
        'type',
        'rate',
        'amount_type',
        'is_active',
        'is_default',
        'is_included_in_price',
        'tax_group_id',
        'account_id',
        'purchase_account_id',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_included_in_price' => 'boolean',
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

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function purchaseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'purchase_account_id');
    }

    public function taxGroup(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_group_id');
    }

    public function childTaxes(): HasMany
    {
        return $this->hasMany(Tax::class, 'tax_group_id');
    }
}
