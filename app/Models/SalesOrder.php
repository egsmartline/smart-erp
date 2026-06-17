<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'order_number',
        'date',
        'required_date',
        'customer_id',
        'warehouse_id',
        'user_id',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total',
        'currency_id',
        'exchange_rate',
        'payment_term_id',
        'status',
        'delivery_status',
        'invoice_status',
        'notes',
        'terms',
        'reference',
        'customer_reference',
        'cancelled_at',
        'cancelled_reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'required_date' => 'date',
            'cancelled_at' => 'date',
            'subtotal' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SalesOrderLine::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function getOrderStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'badge-gray',
            'confirmed' => 'badge-blue',
            'delivered' => 'badge-green',
            'invoiced' => 'badge-purple',
            'cancelled' => 'badge-red',
            default => 'badge-gray',
        };
    }

    public function canConfirm(): bool
    {
        return $this->status === 'draft';
    }

    public function canDeliver(): bool
    {
        return $this->status === 'confirmed';
    }

    public function canInvoice(): bool
    {
        return in_array($this->status, ['confirmed', 'delivered']);
    }
}
