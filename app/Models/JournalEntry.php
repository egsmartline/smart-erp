<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entry_number',
        'date',
        'description',
        'reference',
        'journal_id',
        'total_debit',
        'total_credit',
        'is_posted',
        'tenant_id',
        'fiscal_year_id',
        'type',
        'posted_by',
        'posted_at',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'is_posted' => 'boolean',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
