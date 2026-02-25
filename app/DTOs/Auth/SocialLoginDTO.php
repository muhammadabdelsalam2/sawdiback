<?php

namespace App\DTOs\Auth;
class SocialLoginDTO
{
    public function __construct(
        public readonly string $provider,
        public readonly string $providerId,
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $avatar,
    ) {
    }
}