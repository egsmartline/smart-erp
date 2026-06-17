<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTerm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'name_en',
        'discount_type',
        'discount_percent',
        'days_net',
        'days_discount',
        'is_active',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'decimal:2',
            'days_net' => 'integer',
            'days_discount' => 'integer',
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
}
