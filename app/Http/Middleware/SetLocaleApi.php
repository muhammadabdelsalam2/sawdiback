<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleApi
{
    protected array $supportedLocales = ['en', 'ar'];
    protected string $defaultLocale = 'en';

    public function handle(Request $request, Closure $next): Response
    {
        // 1. Get locale from route
        $locale = $request->route('locale');

        // 2. If not exists → get from header
        if (!$locale) {
            $locale = $request->header('Accept-Language');
        }

        // 3. Clean header (ar-EG → ar)
        if ($locale && str_contains($locale, '-')) {
            $locale = explode('-', $locale)[0];
        }

        // 4. Validate locale
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = $this->defaultLocale;
        }

        // 5. Set locale
        app()->setLocale($locale);

        // 6. Attach data to request
        $request->merge([
            'localeData' => [
                'currentLang' => $locale,
                'direction' => $locale === 'ar' ? 'rtl' : 'ltr',
            ],
        ]);

        // 7. Continue request
        $response = $next($request);

        // 8. Add debug header (optional but useful)
        $response->headers->set('X-Locale', $locale);

        return $response;
    }
}