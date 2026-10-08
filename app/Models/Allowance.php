<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Allowance extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPES = [
        'housing' => 'بدل السكن',
        'transport' => 'بدل المواصلات',
        'communication' => 'بدل الاتصالات',
        'travel' => 'بدل السفر والانتداب',
    ];

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'allowance_number',
        'type',
        'amount',
        'date',
        'payment_method',
        'cash_treasury_id',
        'bank_account_id',
        'status',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(CashTreasury::class, 'cash_treasury_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
