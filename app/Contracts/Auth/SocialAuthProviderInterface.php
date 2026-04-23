<?php

namespace App\Contracts\Auth;

use App\DTOs\Api\Auth\SocialUserDTO;

interface SocialAuthProviderInterface
{
    public function user(): SocialUserDTO;
}