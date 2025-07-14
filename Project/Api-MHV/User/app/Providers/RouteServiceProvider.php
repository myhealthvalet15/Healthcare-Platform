<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';
    private const API_V1_PREFIX = 'V1';
    private const API_V1_ROUTES_DIR = 'routes/V1/modules';
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->routes(function () {
            // Web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            // API routes
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            // Version-1 API routes
            Route::middleware(['api'])
                ->prefix(self::API_V1_PREFIX)
                ->group(function () {
                    // Auth Routes
                    Route::middleware('throttle:auth')
                        ->prefix('auth')->group(function () {
                            Route::prefix('reset')->group(base_path(self::API_V1_ROUTES_DIR . '/Auth/reset.routes.php'));
                        });
                    // HRA Routes
                    Route::middleware('throttle:hra')
                        ->prefix('hra')->group(function () {
                            Route::prefix('factors')->group(base_path(self::API_V1_ROUTES_DIR . '/Hra/hra-factors.routes.php'));
                            Route::prefix('questions')->group(base_path(self::API_V1_ROUTES_DIR . '/Hra/hra-questions.routes.php'));
                            Route::prefix('templates')->group(base_path(self::API_V1_ROUTES_DIR . '/Hra/hra-templates.routes.php'));
                            Route::prefix('master-tests')->group(base_path(self::API_V1_ROUTES_DIR . '/Hra/hra-master-tests.routes.php'));
                        });
                    // Corporate Routes
                    Route::middleware('throttle:corporate')
                        ->prefix('corporate')->group(function () {
                            Route::prefix('corporate-components')->group(base_path(self::API_V1_ROUTES_DIR . '/CorporateComponents/cm.routes.php'));
                        });
                    // Corporate Stub Routes
                    Route::middleware('throttle:corporate-stubs')
                    ->prefix('corporate-stubs')->group(function () {
                        Route::prefix('stubs')->group(base_path(self::API_V1_ROUTES_DIR . '/Corporate/corporate.routes.php'));
                    });
                    Route::middleware('throttle:master-user')
                    ->prefix('master-user')->group(function () {
                        Route::prefix('masteruser')->group(base_path(self::API_V1_ROUTES_DIR . '/MasterUser/employeeuser.routes.php'));
                    });
                    // Corporate employees
                    Route::middleware('throttle:employee-types')
                    ->prefix('employee-types')->group(function () {
                        Route::prefix('employees')->group(base_path(self::API_V1_ROUTES_DIR . '/Corporate/Employees.routes.php'));
                    });
                    // Corporate Users
                    Route::middleware('throttle:corporate-users')
                    ->prefix('corporate-users')->group(function () {
                        Route::prefix('users')->group(base_path(self::API_V1_ROUTES_DIR . '/CorporateUsers/corporateusers.routes.php'));
                    });
                });
        });
    }
    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Default API rate limiter
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(env('API_RATE_LIMIT', 500))
                ->by($request->user()?->id ?: $request->ip());
        });
        // Auth-specific rate limiter
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(env('AUTH_RATE_LIMIT', 500))
                ->by($request->user()?->id ?: $request->ip());
        });
        // HRA-specific rate limiter
        RateLimiter::for('hra', function (Request $request) {
            return Limit::perMinute(env('HRA_RATE_LIMIT', 500))
                ->by($request->user()?->id ?: $request->ip());
        });
        // Corporate-specific rate limiter
        RateLimiter::for('corporate', function (Request $request) {
            return Limit::perMinute(env('CORPORATE_RATE_LIMIT', 500))
                ->by($request->user()?->id ?: $request->ip());
        });
        // Corporate-specific rate limiter
        RateLimiter::for('corporate-stubs', function (Request $request) {
            return Limit::perMinute(env('CORPORATE_STUBS_RATE_LIMIT', 500))
                ->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('master-user', function (Request $request) {
            return Limit::perMinute(env('MASTER_USER_RATE_LIMIT', 500))
                ->by($request->user()?->id ?: $request->ip());
        });
        // Corporate-specific rate limiter
        RateLimiter::for('employee-types', function (Request $request) {
            return Limit::perMinute(env('EMPLOYEES_RATE_LIMIT', 500))
                ->by($request->user()?->id ?: $request->ip());
        });
        // Corporate-users rate limiter
        RateLimiter::for('corporate-users', function (Request $request) {
            return Limit::perMinute(env('CORPORATE_USERS_RATE_LIMIT', 500))
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
