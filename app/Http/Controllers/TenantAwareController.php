<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class TenantAwareController extends Controller
{
    protected function getTenantId(): int
    {
        return session('current_tenant_id') ?? auth()->user()->tenant_id;
    }

    protected function tenantQuery($model)
    {
        return $model::where('tenant_id', $this->getTenantId());
    }
}
