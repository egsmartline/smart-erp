<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TreasuryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'treasury_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'description',
        'reference_number',
        'user_id',
        'target_treasury_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(CashTreasury::class, 'treasury_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetTreasury(): BelongsTo
    {
        return $this->belongsTo(CashTreasury::class, 'target_treasury_id');
    }
}
