<?php

namespace App\DTOs\Auth;

class VerifyOtpDTO
{
    public function __construct(
        public string $identifier,
        public string $code,
        public string $type
    ) {
    }
}