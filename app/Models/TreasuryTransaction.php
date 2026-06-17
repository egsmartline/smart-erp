<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreasuryTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'treasury_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'transactionable_type',
        'transactionable_id',
        'reference_number',
        'description',
        'date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(CashTreasury::class, 'treasury_id');
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
