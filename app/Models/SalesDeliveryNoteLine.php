<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesDeliveryNoteLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'sales_delivery_note_id',
        'sales_order_line_id',
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

    public function salesDeliveryNote(): BelongsTo
    {
        return $this->belongsTo(SalesDeliveryNote::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
