<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
    {
        // Get locale from route
        $locale = $request->route('locale'); // "en" or "ar"

        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid locale',
            ], 400);
        }

        // Set app locale
        app()->setLocale($locale);

        // Attach locale info to request
        $request->merge([
            'localeData' => [
                'currentLang' => $locale,
                'direction' => $locale === 'ar' ? 'rtl' : 'ltr',
            ],
        ]);
        // Show Current Locale in response header for debugging

        return $next($request);
    }
}
