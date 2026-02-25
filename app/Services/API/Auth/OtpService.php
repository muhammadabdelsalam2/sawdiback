<?php

namespace App\Services\API\Auth;

use App\DTOs\Auth\SendOtpDTO;
use App\DTOs\Auth\VerifyOtpDTO;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Illuminate\Validation\ValidationException;

use App\Services\API\Auth\Factories\OtpSenderFactory;
class OtpService
{
    public function __construct(private OtpRepositoryInterface $otpRepository) {}

    /**
     * Send a new OTP
     */
    public function send(SendOtpDTO $dto): void
    {
        // Generate 6-digit OTP code
        $code = random_int(100000, 999999);

        // Invalidate any previous OTPs for this identifier & type
        $this->otpRepository->invalidateOldOtp($dto->identifier, $dto->type);

        // Store OTP in database
        $this->otpRepository->create([
            'identifier' => $dto->identifier,
            'code' => $code,
            'type' => $dto->type,
            'expires_at' => now()->addMinutes(5),
            'used' => false,
        ]);

        // Send OTP via appropriate sender (email or phone)
        $sender = OtpSenderFactory::make($dto->identifier);
        $sender->send($dto->identifier, $code, $dto->type);
    }

    /**
     * Resend OTP
     */
    public function resend(SendOtpDTO $dto): void
    {
        $this->send($dto);
    }

    /**
     * Verify OTP
     *
     * @throws ValidationException
     */
    public function verify(VerifyOtpDTO $dto): bool
    {
        $otp = $this->otpRepository->findValidOtp(
            $dto->identifier,
            $dto->code,
            $dto->type
        );

        if (!$otp) {
            throw ValidationException::withMessages([
                'code' => __('auth.invalid_otp')
            ]);
        }

        // Mark OTP as used
        $this->otpRepository->markAsUsed($otp);

        return true;
    }
}