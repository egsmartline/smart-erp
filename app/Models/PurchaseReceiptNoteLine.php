<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReceiptNoteLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'purchase_receipt_note_id',
        'purchase_order_line_id',
        'item_id',
        'quantity',
        'unit_price',
        'total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function purchaseReceiptNote(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceiptNote::class);
    }

    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderLine::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
