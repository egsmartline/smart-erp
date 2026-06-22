<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'first_name',
        'last_name',
        'name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'national_id',
        'job_position_id',
        'hire_date',
        'contract_end_date',
        'employment_status',
        'gross_salary',
        'bank_name',
        'bank_account',
        'address',
        'city',
        'photo',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'contract_end_date' => 'date',
        'gross_salary' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
