<?php

namespace App\Services\API\Auth;

use App\DTOs\Auth\SendOtpDTO;
use App\DTOs\Auth\VerifyOtpDTO;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Illuminate\Validation\ValidationException;

use App\Services\API\Auth\Factories\OtpSenderFactory;
use App\Models\Otp;
use App\Models\User;
use App\Repositories\OtpRepository;
use Illuminate\Support\Facades\DB;
use App\Enums\OtpType;
class OtpService
{
    public function __construct(private OtpRepository $otpRepository)
    {
    }

    /**
     * Send a new OTP
     */
    public function send(SendOtpDTO $dto, User $user): Otp
    {
        //  Check if there's a valid OTP already
        $existingOtp = $this->otpRepository->findValidOtpByIdentifierAndType(
            $dto->identifier,
            $dto->type
        );
        $user->setAttribute('otp_code', $existingOtp?->code); // For testing/debugging purposes
        if ($existingOtp) {
            $code = $existingOtp->code; // reuse the existing code
        } else {
            //  Generate new 6-digit OTP
            $code = random_int(100000, 999999);

            //  Store new OTP in database
         $otp =    $this->otpRepository->create([
                'identifier' => $dto->identifier,
                'code' => $code,
                'type' => $dto->type,
                'user_id' => $user?->id,
                'expires_at' => now()->addMinutes(5),
                'used' => false,
            ]);
        }

        //  Send OTP via appropriate channel
        $sender = OtpSenderFactory::make($dto->identifier);
        $sender->send($dto->identifier, $code, $dto->type);
        return $existingOtp ?? $otp;
    }

    /**
     * Resend OTP (same logic as send)
     */
    public function resend(SendOtpDTO $dto,$user): void
    {
        // Just call send() - it will reuse unexpired OTP if available
        $this->send($dto,$user);
    }

    /**
     * Verify OTP
     *
     * @throws ValidationException
     */




public function verify(string $identifier, string $code): Otp
{
    // 1. Find OTP by identifier and code
    $otp = $this->otpRepository->findByIdentifierAndCode($identifier, $code);

    if (!$otp) {
        throw ValidationException::withMessages([
            'code' => __('auth.invalid_otp'),
        ]);
    }

    // 2. Check if already used
    if ($otp->used_at !== null) {
        throw ValidationException::withMessages([
            'code' => __('auth.otp_already_used'),
        ]);
    }

    // 3. Check expiration
    if ($otp->expires_at->isPast()) {
        throw ValidationException::withMessages([
            'code' => __('auth.otp_expired'),
        ]);
    }

    DB::transaction(function () use ($otp) {

        // 4. Mark OTP as used
        $this->otpRepository->markAsUsed($otp);

        // 5. Get related user
        $user = $otp->user;

        if (!$user) {
            throw ValidationException::withMessages([
                'identifier' => __('auth.user_not_found'),
            ]);
        }

        // 6. Update verification field based on OTP type
        if ($otp->type === OtpType::VERIFY_EMAIL) {
            $user->update([
                'email_verified_at' => now(),
            ]);
        }

        if ($otp->type === OtpType::VERIFY_PHONE) {
            $user->update([
                'phone_verified_at' => now(),
            ]);
        }
        });

    return $otp;
}
}
