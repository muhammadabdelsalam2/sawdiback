<?php

namespace App\Providers;

use App\Repositories\LeaveRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected array $repositories = [
        // Plan Module
        \App\Repositories\Contracts\PlanRepositoryInterface::class => \App\Repositories\PlanRepository::class,

        // HR Module
        \App\Repositories\Contracts\DepartmentRepositoryInterface::class => \App\Repositories\DepartmentRepository::class,
        \App\Repositories\Contracts\JobTitleRepositoryInterface::class => \App\Repositories\JobTitleRepository::class,
        \App\Repositories\Contracts\EmployeeRepositoryInterface::class => \App\Repositories\EmployeeRepository::class,
        \App\Repositories\Contracts\LeaveRepositoryInterface::class => \App\Repositories\LeaveRepository::class,
        \App\Repositories\Contracts\AttendanceRepositoryInterface::class => \App\Repositories\AttendanceRepository::class,

        // Customer Module
        \App\Repositories\Contracts\CustomerRepositoryInterface::class => \App\Repositories\CustomerRepository::class,
        \App\Repositories\Contracts\CustomerSubscriptionRepositoryInterface::class => \App\Repositories\CustomerSubscriptionRepository::class,
    ];
    public function register(): void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }

        $legacyLeaveInterface = 'App\\Repositories\\Contracts\\LeaveRequestRepositoryInterface';
        if (interface_exists($legacyLeaveInterface)) {
            $this->app->bind($legacyLeaveInterface, LeaveRepository::class);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
