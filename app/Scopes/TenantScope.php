<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
class TenantScope implements Scope
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function apply(Builder $builder, Model $model)
    {
        // Only filter if tenant_id column exists
        if (in_array('tenant_id', $model->getFillable()) || $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'tenant_id')) {

            $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;

            // Apply filter only if tenantId exists
            if ($tenantId) {
                $builder->where($model->getTable() . '.tenant_id', $tenantId);
            }
        }
    }
}
