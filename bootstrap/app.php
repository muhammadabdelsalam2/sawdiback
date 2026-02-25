<?php

use App\Http\Middleware\ApiErrorMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\EnsureFeatureEnabled;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {

            // ===== Web Routes =====
            Route::middleware('web')->group(function () {

                // Load main web.php file
                require base_path('routes/web.php');

                // Load extra web routes in routes/web/*.php
                foreach (glob(base_path('routes/web/*.php')) as $file) {
                    require $file;
                }
            });

            // ===== API Routes =====
            Route::prefix('api')->middleware('api')->group(function () {

                // Load main api.php file
                require base_path('routes/api.php');

                // Load extra api routes in routes/api/*.php if you have them
                foreach (glob(base_path('routes/api/*.php')) as $file) {
                    require $file;
                }
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request): string {
            $locale = $request->segment(1);

            if (!is_string($locale) || !preg_match('/^[a-z]{2}-[A-Z]{2}$/', $locale)) {
                $locale = session('locale_full', 'en-SA');
            }

            return route('login.form', ['locale' => $locale]);
        });

        $middleware->alias([
            'set.locale' => SetLocale::class,
            'auth' => Authenticate::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,

            // ✅ REQUIRED FOR FEATURE GATING
            'feature' => EnsureFeatureEnabled::class,
        ]);

        // Append API error middleware
        $middleware->appendToGroup('api', [
            ApiErrorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();