<?php

namespace App\DTOs\Auth;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use App\Http\Requests\Api\Password\ResetPasswordRequest;
class VerifyForgetPasswordOtpDTO
{
    public function __construct(
        public string $code,
    ) {}

    public static function fromRequest(ResetPasswordRequest $request): self
    {
        return new self(
            code: $request->code,
        );
    }
}
