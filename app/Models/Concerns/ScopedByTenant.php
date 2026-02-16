<?php

namespace App\Models\Concerns;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

trait ScopedByTenant
{
    protected static function bootScopedByTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function (Model $model): void {
            if (!empty($model->tenant_id)) {
                return;
            }

            $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
            if ($tenantId) {
                $model->tenant_id = $tenantId;
            }
        });
    }
}
