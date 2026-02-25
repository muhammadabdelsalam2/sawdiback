<?php

namespace App\Repositories;

use App\Models\Otp;
use App\Repositories\Contracts\OtpRepositoryInterface;

class OtpRepository implements OtpRepositoryInterface
{
    public function create(array $data): Otp
    {
        return Otp::create($data);
    }

    public function invalidateOldOtp(string $identifier, string $type): void
    {
        Otp::where('identifier', $identifier)
            ->where('type', $type)
            ->where('is_used', false)
            ->update(['is_used' => true]);
    }

    public function findValidOtp(string $identifier, string $code, string $type): ?Otp
    {
        return Otp::where('identifier', $identifier)
            ->where('code', $code)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>=', now())
            ->first();
    }

    public function markAsUsed(Otp $otp): void
    {
        $otp->update(['is_used' => true]);
    }
}