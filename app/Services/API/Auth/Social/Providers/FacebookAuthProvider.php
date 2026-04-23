<?php

namespace App\Services\API\Auth\Social\Providers;

use App\Contracts\Auth\SocialAuthProviderInterface;
use App\DTOs\Api\Auth\SocialUserDTO;
use Laravel\Socialite\Facades\Socialite;



class FacebookAuthProvider implements SocialAuthProviderInterface
{
    const FACEBOOK_PATIENT_REDIRECT = "api/v1/en/auth/social/facebook/callback";
    public function user(): SocialUserDTO
    {
        $user = Socialite::driver('facebook')
            ->redirectUrl(config('app.url') . self::FACEBOOK_PATIENT_REDIRECT)
            ->stateless()
            ->user();

        return new SocialUserDTO(
            'facebook',
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getAvatar()
        );
    }
}