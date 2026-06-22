<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class RoleController extends TenantAwareController
{
    public function index()
    {
        $roles = $this->tenantQuery(UserRole::class)->with('permissions')->get();
        $permissions = UserPermission::where('tenant_id', $this->getTenantId())->get()->groupBy('group');
        $users = User::where('tenant_id', $this->getTenantId())->get();

        return view('settings.roles.index', compact('roles', 'permissions', 'users'));
    }

    public function create()
    {
        $permissions = UserPermission::where('tenant_id', $this->getTenantId())->get()->groupBy('group');
        return view('settings.roles.form', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:user_roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:user_permissions,id',
        ]);

        $role = UserRole::create([
            'tenant_id' => $this->getTenantId(),
            'name' => $validated['name'],
            'name_en' => $validated['name_en'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'is_active' => true,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('settings.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح');
    }

    public function edit(UserRole $role)
    {
        $this->authorizeTenant($role);
        $permissions = UserPermission::where('tenant_id', $this->getTenantId())->get()->groupBy('group');
        $role->load('permissions');

        return view('settings.roles.form', compact('role', 'permissions'));
    }

    public function update(Request $request, UserRole $role)
    {
        $this->authorizeTenant($role);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:user_permissions,id',
        ]);

        if ($role->is_system && !$request->boolean('is_active', true)) {
            return back()->with('error', 'لا يمكن تعطيل دور النظام');
        }

        $role->update([
            'name' => $validated['name'],
            'name_en' => $validated['name_en'],
            'description' => $validated['description'],
            'is_active' => $request->boolean('is_active', $role->is_active),
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('settings.roles.index')
            ->with('success', 'تم تحديث الدور بنجاح');
    }

    public function destroy(UserRole $role)
    {
        $this->authorizeTenant($role);

        if ($role->is_system) {
            return back()->with('error', 'لا يمكن حذف دور النظام');
        }

        $usersCount = User::where('tenant_id', $this->getTenantId())
            ->where('role', $role->slug)->count();

        if ($usersCount > 0) {
            return back()->with('error', 'لا يمكن حذف الدور لأنه مستخدم من قبل ' . $usersCount . ' مستخدم');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('settings.roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }

    public function assignRole(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:user_roles,slug',
        ]);

        $user = User::where('tenant_id', $this->getTenantId())
            ->findOrFail($validated['user_id']);

        if ($user->is_system) {
            return back()->with('error', 'لا يمكن تغيير دور مستخدم النظام');
        }

        $user->update(['role' => $validated['role']]);

        return redirect()->route('settings.roles.index')
            ->with('success', 'تم تعيين الدور للمستخدم بنجاح');
    }

    private function authorizeTenant(UserRole $role)
    {
        if ($role->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
