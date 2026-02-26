<?php

namespace App\Repositories;

use App\Models\Otp;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
class OtpRepository implements OtpRepositoryInterface
{
protected string $model = Otp::class;

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
    public function findValidOtpByIdentifierAndType(string $identifier, string $type): ?Otp
{
    return  Otp::where('identifier', $identifier)
        ->where('type', $type)
        ->whereNull('used_at')
        ->where('expires_at', '>', now())
        ->latest()
        ->first();
}

public function findValidOtp(string $identifier, string $code, ?string $type = null): ?Otp
{
    $query = Otp::where('identifier', $identifier)
                ->where('code', $code)
                ->whereNull('used_at');

    if ($type) {
        $query->where('type', $type);
    }

    $otp = $query->latest('expires_at')->first();

    return $otp;
}

    public function markAsUsed(Otp $otp): void
    {
        $otp->update(['is_used' => true , 'used_at' => now()]);
    }
}