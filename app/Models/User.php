<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'role',
        'phone',
        'is_active',
        'is_system',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'avatar',
        'locale',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getAccessibleTenants()
    {
        if ($this->isSuperAdmin()) {
            return Tenant::where('is_active', true)->get();
        }

        $tenants = $this->tenants()->where('is_active', true)->get();

        if ($this->tenant && $this->tenant->is_active) {
            $tenants = $tenants->push($this->tenant)->unique('id');
        }

        return $tenants;
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'created_by');
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'cashier_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'creator_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function roleModel()
    {
        return $this->belongsTo(UserRole::class, 'role', 'slug');
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->roleModel()
            ?->permissions()
            ->where('slug', $permissionSlug)
            ->exists() ?? false;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }
}
