<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $table = 'user_permissions';

    protected $fillable = [
        'tenant_id',
        'name',
        'name_en',
        'slug',
        'group',
    ];

    public function roles()
    {
        return $this->belongsToMany(UserRole::class, 'permission_role', 'permission_id', 'role_id')
            ->withTimestamps();
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
