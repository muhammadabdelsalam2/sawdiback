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

        // Sales & Distribution Module
        \App\Repositories\Contracts\SalesDistribution\SalesCustomerRepositoryInterface::class => \App\Repositories\SalesDistribution\SalesCustomerRepository::class,
        \App\Repositories\Contracts\SalesDistribution\SalesContractRepositoryInterface::class => \App\Repositories\SalesDistribution\SalesContractRepository::class,
        \App\Repositories\Contracts\SalesDistribution\SalesOrderRepositoryInterface::class => \App\Repositories\SalesDistribution\SalesOrderRepository::class,
        \App\Repositories\Contracts\SalesDistribution\SalesShipmentRepositoryInterface::class => \App\Repositories\SalesDistribution\SalesShipmentRepository::class,
        \App\Repositories\Contracts\SalesDistribution\SalesInvoiceRepositoryInterface::class => \App\Repositories\SalesDistribution\SalesInvoiceRepository::class,
        \App\Repositories\Contracts\SalesDistribution\SalesPaymentRepositoryInterface::class => \App\Repositories\SalesDistribution\SalesPaymentRepository::class,
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
