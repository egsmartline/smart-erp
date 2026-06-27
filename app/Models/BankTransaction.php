<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'bank_account_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'description',
        'check_number',
        'check_date',
        'reference_number',
        'user_id',
        'target_bank_account_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'check_date' => 'date',
        ];
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetBankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'target_bank_account_id');
    }
}
