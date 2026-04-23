<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\API\Auth\Social\Providers\FacebookAuthProvider;
use App\Services\API\Auth\Social\Providers\GoogleAuthProvider;
use App\Services\API\Auth\Social\SocialAuthService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    //
    const GOOGLE_PATIENT_REDIRECT = "api/v1/en/auth/social/google/callback";
    const FACEBOOK_PATIENT_REDIRECT = "api/v1/en/auth/social/google/callback";
    const GOOGLE_PATIENT_CALLBACK = "api/v1/en/auth/social/google/callback";

    public function redirect($locale, string $provider)
    {

        $redirectLink = Socialite::driver($provider)
            ->stateless()
            ->redirectUrl(config('app.url') . "api/v1/en/auth/social/$provider/callback")
            ->redirect()->getTargetUrl();
        return response()->json($redirectLink);
    }

    public function callback($locale, string $provider, SocialAuthService $service)
    {
        $providerInstance = match ($provider) {
            'google' => new GoogleAuthProvider(),
            'facebook' => new FacebookAuthProvider(),
            default => abort(404),
        };
        $result = $service->login($providerInstance);
        return response()->json([
            'status' => true,
            'token' => $result['token'],
            'user' => new UserResource($result['user'])
        ]);
    }
}
