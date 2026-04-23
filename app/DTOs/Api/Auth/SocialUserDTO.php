<?php

namespace App\DTOs\Api\Auth;

class SocialUserDTO
{
    public function __construct(
        public string $provider,
        public string $providerId,
        public ?string $name,
        public ?string $email,
        public ?string $avatar,
    ) {
    }
}