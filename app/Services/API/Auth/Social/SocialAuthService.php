<?php

namespace App\Services\API\Auth\Social;

use App\Actions\Auth\HandleSocialUser;
use App\Contracts\Auth\SocialAuthProviderInterface;
use App\Models\User;

class SocialAuthService
{
    public function __construct(
        private HandleSocialUser $handleSocialUser
    ) {
    }

    public function login(SocialAuthProviderInterface $provider): array
    {
        $dto = $provider->user();
        $user = $this->handleSocialUser->execute($dto);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}