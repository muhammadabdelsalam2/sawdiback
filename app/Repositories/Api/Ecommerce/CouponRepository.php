<?php

namespace App\Repositories\Api\Ecommerce;

use App\Models\Coupon;
use App\Models\User;
use App\Repositories\Contracts\Api\CouponRepositoryInterface;

class CouponRepository implements CouponRepositoryInterface
{
    public function findValidCouponForUser(User $user, string $code): ?Coupon
    {
        $now = now();
        $tenantId = $user->tenant_id;

        return Coupon::query()
            ->where('code', strtoupper(trim($code)))
            ->where('is_active', true)
            ->when($tenantId, fn ($q) => $q->where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            }))
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->first();
    }
}
