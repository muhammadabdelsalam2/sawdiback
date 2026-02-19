<?php

namespace App\Services\Customer\HR;

use App\Models\User;

class HrContextService
{
    public function tenantIdOrFail(User $user): string
    {
        $tenantId = (string) $user->tenant_id;

        if (!$tenantId) {
            abort(403, 'No tenant linked to this user.');
        }

        return $tenantId;
    }
}
