<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Observers\TenantObserver;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Services\PlanFeatureService;

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
        // ✅ Plan Repo Binding (REQUIRED)
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);

        // ✅ HR Repo Bindings (REQUIRED for HR module controllers)
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(JobTitleRepositoryInterface::class, JobTitleRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(LeaveRepositoryInterface::class, LeaveRepository::class);
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);

        /**
         * ✅ Backward/Name-compat binding (SAFE)
         * لو في كود قديم/كنترولرز بتطلب:
         * App\Repositories\Contracts\LeaveRequestRepositoryInterface
         * هنربطها على نفس LeaveRepository بدون ما نكسر شغل.
         *
         * IMPORTANT: استخدمنا string بدل ::class عشان الـ IDE ميعملش خط أحمر لو interface مش موجودة.
         */
        $legacyLeaveInterface = 'App\\Repositories\\Contracts\\LeaveRequestRepositoryInterface';
        if (interface_exists($legacyLeaveInterface)) {
            $this->app->bind($legacyLeaveInterface, LeaveRepository::class);
        }
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
