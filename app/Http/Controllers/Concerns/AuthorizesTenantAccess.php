<?php

namespace App\Http\Controllers\Concerns;

trait AuthorizesTenantAccess
{
    protected function getTenantId(): int
    {
        return session('current_tenant_id') ?? auth()->user()->tenant_id;
    }

    protected function authorizeTenant($model): void
    {
        if (!$model || $model->tenant_id !== $this->getTenantId()) {
            abort(403);
        }
    }
}
