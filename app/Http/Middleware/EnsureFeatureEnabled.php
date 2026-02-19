<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureEnabled
{
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // SuperAdmin bypass
        if (method_exists($user, 'hasRole') && $user->hasRole('SuperAdmin')) {
            return $next($request);
        }

        if (!method_exists($user, 'hasPlanFeature') || !$user->hasPlanFeature($featureKey)) {
            $locale = $request->route('locale') ?? session('locale_full', 'en-SA');

            return redirect()
                ->route('customer.subscription.index', ['locale' => $locale])
                ->with('error', __('dashboard.feature_not_available'));
        }

        return $next($request);
    }
}
