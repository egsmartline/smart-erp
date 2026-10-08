<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAdjustmentLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'inventory_adjustment_id',
        'item_id',
        'theoretical_qty',
        'actual_qty',
        'difference',
        'unit_cost',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'theoretical_qty' => 'decimal:2',
            'actual_qty' => 'decimal:2',
            'difference' => 'decimal:2',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(InventoryAdjustment::class, 'inventory_adjustment_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
