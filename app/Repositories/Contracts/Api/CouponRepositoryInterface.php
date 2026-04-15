<?php

namespace App\Repositories\Contracts\Api;

use App\Models\Coupon;
use App\Models\User;

interface CouponRepositoryInterface
{
    public function findValidCouponForUser(User $user, string $code): ?Coupon;
}
