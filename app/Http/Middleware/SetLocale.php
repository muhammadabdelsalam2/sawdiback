<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang_country = $request->route('locale'); // e.g., "en-SA"

        // Validate locale pattern
        if (!preg_match('/^(en|ar)-(SA|EG)$/', $lang_country)) {
            return redirect('/en-SA'); // fallback
        }

        [$lang, $country] = explode('-', $lang_country);

        // Set app locale and store in session
        app()->setLocale($lang);
        session(['locale' => $lang, 'locale_full' => $lang_country]);

        // Map country to currency code
        $currencyMap = [
            'SA' => 'SAR',
            'EG' => 'EGP',
        ];

        $currencyCode = $currencyMap[$country] ?? 'SAR';

        // Fetch currency from DB dynamically
        $currency = Currency::where('symbol', $currencyCode)
            ->orderBy('is_default', 'desc') // optional, default currency first
            ->first();

        if ($currency) {
            session([
                'currency' => $currency->toArray(), // full currency info
                'currency_id' => $currency->id,     // store currency_id for models
            ]);
        } else {
            // fallback if not found
            session([
                'currency' => [
                    'code' => $currencyCode,
                    'symbol' => $currencyCode,
                ],
                'currency_id' => null,
            ]);
        }

        // Extra locale data for views
        $request->localeData = [
            'currentLocale' => session('locale_full', 'en-SA'),
            'currentLang' => $lang,
            'currentCurrency' => session('currency', ['code' => 'USD', 'symbol' => '$']),
            'direction' => in_array($lang, ['ar']) ? 'rtl' : 'ltr',
        ];

        return $next($request);
    }
}
