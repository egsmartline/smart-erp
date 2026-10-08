<?php

namespace App\Http\Middleware;

use App\Models\UserPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    protected static ?array $catalog = null;

    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if (!$user->tenant_id) {
            return $next($request);
        }

        $permission = $permission ?? $this->permissionFor($request);

        if ($permission === null) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            abort(403, 'ليست لديك صلاحية لتنفيذ هذا الإجراء');
        }

        return $next($request);
    }

    protected function permissionFor(Request $request): ?string
    {
        $name = $request->route()?->getName();

        if (!$name) {
            return null;
        }

        $explicit = $this->explicit($name);

        if ($explicit !== null) {
            return $this->known($explicit) ? $explicit : null;
        }

        $parts = explode('.', $name);
        $verb = array_pop($parts);
        $resource = implode('.', $parts);

        $base = config('permissions.resources.' . $resource);
        $action = config('permissions.verbs.' . $verb);

        if (!$base || !$action) {
            return null;
        }

        $slug = $action . '_' . $base;

        return $this->known($slug) ? $slug : null;
    }

    protected function explicit(string $name): ?string
    {
        $routes = config('permissions.routes', []);

        if (isset($routes[$name])) {
            return $routes[$name];
        }

        foreach ($routes as $key => $slug) {
            if (str_ends_with($key, '.') && str_starts_with($name, $key)) {
                return $slug;
            }
        }

        return null;
    }

    protected function known(string $slug): bool
    {
        if (self::$catalog === null) {
            self::$catalog = UserPermission::query()->distinct()->pluck('slug')->all();
        }

        return in_array($slug, self::$catalog, true);
    }
}
