<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Observers\TenantObserver;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Services\PlanFeatureService;
use App\Services\SalesDistribution\Accounting\AccountingGateway;
use App\Services\SalesDistribution\Accounting\NullAccountingGateway;

// Plan Repo
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\PlanRepository;

// HR Contracts
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Repositories\Contracts\JobTitleRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\LeaveRepositoryInterface;
use App\Repositories\Contracts\AttendanceRepositoryInterface;

// HR Repos
use App\Repositories\DepartmentRepository;
use App\Repositories\JobTitleRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\LeaveRepository;
use App\Repositories\AttendanceRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AccountingGateway::class, NullAccountingGateway::class);
    }

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

            // ✅ Subscription + Features Context
            $service = app(PlanFeatureService::class);
            $ctx = $service->contextFor(Auth::user());

            $view->with([
                'hasActiveSubscription' => $ctx['hasActiveSubscription'] ?? false,
                'activeSubscription' => $ctx['activeSubscription'] ?? null,
                'planFeatures' => $ctx['planFeatures'] ?? [],
                'featureFlags' => $ctx['featureFlags'] ?? [],
            ]);
        });

        Tenant::observe(TenantObserver::class);
    }
}
