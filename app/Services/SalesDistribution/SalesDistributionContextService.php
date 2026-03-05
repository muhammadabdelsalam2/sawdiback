<?php

namespace App\Services\SalesDistribution;

use App\Models\User;

class SalesDistributionContextService
{
    public function tenantIdOrFail(User $user): string
    {
        $tenantId = (string) $user->tenant_id;

        if (!$tenantId) {
            abort(403, __('sales_dist.messages.no_tenant'));
        }

        return $tenantId;
    }
}
