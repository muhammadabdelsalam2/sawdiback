<?php

namespace App\Providers;

use App\Repositories\Contracts\OtpRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\LeaveRepository;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use App\Services\API\Auth\Contracts\OtpSenderFactory;
use App\Services\API\Auth\Contracts\OtpSenderInterface;
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
        UserRepositoryInterface::class => UserRepository::class,
        OtpRepositoryInterface::class => OtpRepository::class,
        // OTP Repository
    ];
    public function register(): void
    {
        $this->app->bind(OtpSenderInterface::class, function ($app) {
            return new class implements OtpSenderInterface {
                public function send(string $identifier, string $code): void
                {
                    $sender = OtpSenderFactory::make($identifier);
                    $sender->send($identifier, $code);
                }
            };
        });
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
