<?php

namespace App\Services\API\Auth;

use App\DTOs\Auth\SendOtpDTO;
use App\DTOs\Auth\VerifyOtpDTO;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Illuminate\Validation\ValidationException;

use App\Services\API\Auth\Factories\OtpSenderFactory;
use App\Models\Otp;
use App\Repositories\OtpRepository;

class OtpService
{
    public function __construct(private OtpRepository $otpRepository)
    {
    }

    /**
     * Send a new OTP
     */
    public function send(SendOtpDTO $dto): void
    {
        // 1️⃣ Check if there's a valid OTP already
        $existingOtp = $this->otpRepository->findValidOtpByIdentifierAndType(
            $dto->identifier,
            $dto->type
        );

        if ($existingOtp) {
            $code = $existingOtp->code; // reuse the existing code
        } else {
            // 2️⃣ Generate new 6-digit OTP
            $code = random_int(100000, 999999);

            // 3️⃣ Store new OTP in database
            $this->otpRepository->create([
                'identifier' => $dto->identifier,
                'code' => $code,
                'type' => $dto->type,
                'expires_at' => now()->addMinutes(5),
                'used' => false,
            ]);
        }

        // 4️⃣ Send OTP via appropriate channel
        $sender = OtpSenderFactory::make($dto->identifier);
        $sender->send($dto->identifier, $code, $dto->type);
    }

    /**
     * Resend OTP (same logic as send)
     */
    public function resend(SendOtpDTO $dto): void
    {
        // Just call send() - it will reuse unexpired OTP if available
        $this->send($dto);
    }

    /**
     * Verify OTP
     *
     * @throws ValidationException
     */


public function verify(string $identifier, string $code): Otp
{
    $otp = $this->otpRepository->findValidOtp($identifier, $code);
    if (!$otp) {
        throw ValidationException::withMessages([
            'code' => __('auth.invalid_otp')
        ]);
    }

    if ($otp->expires_at->isPast()) {
        throw ValidationException::withMessages([
            'code' => __('auth.otp_expired')
        ]);
    }
    if (!is_null($otp->used_at)) {
        throw ValidationException::withMessages([
            'code' => __('auth.otp_already_used')
        ]);
    }
    if ($otp->is_used === 1) {
        throw ValidationException::withMessages([
            'code' => __('auth.otp_already_used')
        ]);
    }
    $this->otpRepository->markAsUsed($otp);

    return $otp;
}
}
