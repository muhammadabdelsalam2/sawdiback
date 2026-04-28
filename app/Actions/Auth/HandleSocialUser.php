<?php

namespace App\Actions\Auth;

use App\DTOs\Api\Auth\SocialUserDTO;
use App\Models\User;


class HandleSocialUser
{
    public function execute(SocialUserDTO $dto): User
    {
        // Existing provider
        $user = User::where('provider', $dto->provider)
            ->where('provider_id', $dto->providerId)
            ->first();

        if ($user)
            return $user;

        // Existing email
        if ($dto->email) {
            $user = User::where('email', $dto->email)->first();

            if ($user) {
                $user->update([
                    'provider' => $dto->provider,
                    'provider_id' => $dto->providerId,
                    'avatar' => $dto->avatar,
                ]);

                return $user;
            }
        }

        // Create new
        return User::create([
            'name' => $dto->name ?? 'User',
            'email' => $dto->email,
            'provider' => $dto->provider,
            'provider_id' => $dto->providerId,
            'avatar' => $dto->avatar,
            'password' => bcrypt(str()->random(16)),
        ]);
    }
}