<?php
namespace App\DTOs\Auth;

use App\Enums\OtpType;


class SendOtpDTO
{
    public function __construct(
        public string $identifier,
        public string $type = OtpType::LOGIN
    ) {}
}

