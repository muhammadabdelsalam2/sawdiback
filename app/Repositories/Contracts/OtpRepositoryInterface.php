<?php

namespace App\Repositories\Contracts;

use App\Models\Otp;

interface OtpRepositoryInterface
{
    public function create(array $data): Otp;

    public function invalidateOldOtp(string $identifier, string $type): void;

public function findValidOtp(string $identifier, string $code, ?string $type = null): ?Otp;
    public function markAsUsed(Otp $otp): void;
}