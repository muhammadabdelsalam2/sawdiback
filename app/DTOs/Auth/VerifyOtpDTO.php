<?php

namespace App\DTOs\Auth;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
class VerifyOtpDTO
{
    public function __construct(
        public string $identifier,
        public string $code,
    ) {}

    public static function fromRequest(VerifyOtpRequest $request): self
    {
        return new self(
            identifier: $request->identifier,
            code: $request->code,
        );
    }
}