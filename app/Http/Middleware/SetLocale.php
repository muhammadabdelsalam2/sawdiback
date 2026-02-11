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

        // Map country to currency
        $currencyMap = [
            'SA' => 'SAR',
            'EG' => 'EGP',
        ];

        $currencyCode = $currencyMap[$country] ?? 'SAR';
        $request->localeData = [
            'currentLocale' => session('locale_full', 'en-SA'),
            'currentLang' => $lang,
            'currentCurrency' => session('currency', 'USD'),
            'direction' => in_array($lang, ['ar']) ? 'rtl' : 'ltr',
        ];
        // Fetch currency from DB
        $currency = Currency::where('code', $currencyCode)->first();
        if ($currency) {
            session(['currency' => $currency]);
        } else {
            session([
                'currency' => [
                    'code' => $currencyCode,
                    'symbol' => $currencyCode,
                ]
            ]);
        }
        return $next($request);
    }
}
