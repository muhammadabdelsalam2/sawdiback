<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Models\LivestockAnimal;
use App\Observers\LivestockAnimalObserver;
use App\Observers\TenantObserver;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\PlanRepository;
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
        $this->app->bind(
            PlanRepositoryInterface::class,
            PlanRepository::class


        );
        $this->app->bind(
    \App\Repositories\Contracts\CustomerSubscriptionRepositoryInterface::class,
    \App\Repositories\CustomerSubscriptionRepository::class
);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $lang = session('locale', 'en');
            $view->with([
                'currentLocale' => session('locale_full', 'en-SA'),
                'currentLang' => session('locale', 'en'),
                'currentCurrency' => session('currency'),
                'direction' => in_array($lang, ['ar']) ? 'rtl' : 'ltr',
            ]);
        });
        Tenant::observe(TenantObserver::class);
        LivestockAnimal::observe(LivestockAnimalObserver::class);
    }
}
