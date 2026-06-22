<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends TenantAwareController
{
    public function index()
    {
        $users = User::where('tenant_id', $this->getTenantId())->get();
        $roles = $this->tenantQuery(UserRole::class)->where('is_active', true)->get();

        return view('settings.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = $this->tenantQuery(UserRole::class)->where('is_active', true)->get();
        return view('settings.users.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|exists:user_roles,slug',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        User::create([
            'tenant_id' => $this->getTenantId(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('settings.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function edit(User $user)
    {
        $this->authorizeTenant($user);
        $roles = $this->tenantQuery(UserRole::class)->where('is_active', true)->get();
        return view('settings.users.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeTenant($user);

        if ($user->is_system) {
            return back()->with('error', 'لا يمكن تعديل مستخدم النظام');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|exists:user_roles,slug',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', $user->is_active),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('settings.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        $this->authorizeTenant($user);

        if ($user->is_system) {
            return back()->with('error', 'لا يمكن حذف مستخدم النظام');
        }

        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    private function authorizeTenant(User $user)
    {
        if ($user->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
