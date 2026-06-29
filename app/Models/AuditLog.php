<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'action',
        'model',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): ?string
    {
        return match ($this->model) {
            SalesInvoice::class => route('sales-invoices.show', $this->model_id, false),
            PurchaseInvoice::class => route('purchase-invoices.show', $this->model_id, false),
            Customer::class => route('customers.show', $this->model_id, false),
            Supplier::class => route('suppliers.show', $this->model_id, false),
            Item::class => route('items.show', $this->model_id, false),
            Payment::class => route('payments.show', $this->model_id, false),
            Expense::class => route('expenses.show', $this->model_id, false),
            Quotation::class => route('quotations.show', $this->model_id, false),
            SalesReturn::class => route('sales-returns.show', $this->model_id, false),
            PurchaseReturn::class => route('purchase-returns.show', $this->model_id, false),
            default => null,
        };
    }
}
