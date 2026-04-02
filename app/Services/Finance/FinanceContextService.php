<?php

namespace App\Services\Finance;

use App\Models\User;

class FinanceContextService
{
    public function tenantIdOrFail(User $user): string
    {
        $tenantId = (string) $user->tenant_id;

        if (!$tenantId) {
            abort(403, __('finance.messages.no_tenant'));
        }

        return $tenantId;
    }
}
