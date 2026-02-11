<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //   

        View::composer('*', function ($view) {
            $lang = session('locale', 'en');
            $localeFull = session('locale_full', 'en-SA');
            $view->with([
                'currentLocale' => session('locale_full', 'en-SA'),
                'currentLang' => session('locale', 'en'),
                'currentCurrency' => session('currency'),
                'direction' => in_array($lang, ['ar']) ? 'rtl' : 'ltr',
            ]);
        });

        Authenticate::redirectUsing(function ($request) {
            $locale = $request->segment(1) ?? session('locale') ?? config('app.locale');

            return route('login.submit', ['locale' => $locale]);
        });
    }
}
