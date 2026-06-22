<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use SoftDeletes;

    protected $table = 'user_roles';

    protected $fillable = [
        'tenant_id',
        'name',
        'name_en',
        'slug',
        'description',
        'is_system',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function permissions()
    {
        return $this->belongsToMany(UserPermission::class, 'permission_role', 'role_id', 'permission_id')
            ->withTimestamps();
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
