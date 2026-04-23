<?php

namespace App\Services\API\Auth\Social\Providers;


use App\Contracts\Auth\SocialAuthProviderInterface;
use App\DTOs\Api\Auth\SocialUserDTO;
use Laravel\Socialite\Facades\Socialite;


class GoogleAuthProvider implements SocialAuthProviderInterface
{
    const GOOGLE_PATIENT_REDIRECT = "api/v1/en/auth/social/google/callback";
    public function user(): SocialUserDTO
    {
        $user = Socialite::driver('google')
            ->redirectUrl(config('app.url') . self::GOOGLE_PATIENT_REDIRECT)
            ->stateless()
            ->user();

        return new SocialUserDTO(
            'google',
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getAvatar()
        );
    }
}