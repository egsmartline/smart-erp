<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'unit_id',
        'name',
        'name_ar',
        'sku',
        'barcode',
        'description',
        'cost_price',
        'purchase_currency_id',
        'selling_price',
        'sales_currency_id',
        'minimum_stock',
        'maximum_stock',
        'reorder_level',
        'tax_rate',
        'is_active',
        'has_serial_numbers',
        'has_expiry_date',
        'image',
        'opening_stock',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'maximum_stock' => 'decimal:2',
            'reorder_level' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'opening_stock' => 'decimal:2',
            'is_active' => 'boolean',
            'has_serial_numbers' => 'boolean',
            'has_expiry_date' => 'boolean',
        ];
    }

    public function scopeForTenant($query, ?int $tenantId = null)
    {
        return $query->where('tenant_id', $tenantId ?? tenant('id'));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(ItemWarehouse::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function salesInvoiceLines(): HasMany
    {
        return $this->hasMany(SalesInvoiceLine::class);
    }

    public function purchaseInvoiceLines(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceLine::class);
    }

    public function salesReturnLines(): HasMany
    {
        return $this->hasMany(SalesReturnLine::class);
    }

    public function purchaseReturnLines(): HasMany
    {
        return $this->hasMany(PurchaseReturnLine::class);
    }

    public function quotationLines(): HasMany
    {
        return $this->hasMany(QuotationLine::class);
    }

    public function purchaseOrderLines(): HasMany
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function purchaseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'purchase_currency_id');
    }

    public function salesCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'sales_currency_id');
    }
}
