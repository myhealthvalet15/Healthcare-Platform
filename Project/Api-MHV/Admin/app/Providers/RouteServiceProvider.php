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
                            Route::prefix('corporate-components')->group(base_path(self::API_V1_ROUTES_DIR . '/CorporateModules/cm.routes.php'));
                        });
                    // Corporate Stub Routes
                    Route::middleware('throttle:corporate-stubs')
                        ->prefix('corporate-stubs')->group(function () {
                            Route::prefix('stubs')->group(base_path(self::API_V1_ROUTES_DIR . '/Corporate/corporate.routes.php'));
                        });
                    // Drug Routes
                    Route::middleware('throttle:drugs-stubs')
                        ->prefix('drugs-stubs')->group(function () {
                            Route::prefix('drugs-stubs')->group(base_path(self::API_V1_ROUTES_DIR . '/Drug/drug.routes.php'));
                        });
                    Route::middleware('throttle:forms-stubs')
                        ->prefix('forms-stubs')->group(function () {
                            Route::prefix('forms-stubs')->group(base_path(self::API_V1_ROUTES_DIR . '/Forms/forms.routes.php'));
                        });
                    // Medical Condition Routes
                    Route::middleware('throttle:medicalcondition-stubs')
                    ->prefix('medicalcondition-stubs')->group(function () {
                        Route::prefix('medicalcondition-stubs')->group(base_path(self::API_V1_ROUTES_DIR . '/MedicalCondition/medicalcondition.routes.php'));
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
            return Limit::perMinute(env('API_RATE_LIMIT', 1000))
                ->by($request->user()?->id ?: $request->ip());
        });
        // Auth-specific rate limiter
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(env('AUTH_RATE_LIMIT', 1000))
                ->by($request->user()?->id ?: $request->ip());
        });
        // HRA-specific rate limiter
        RateLimiter::for('hra', function (Request $request) {
            return Limit::perMinute(env('HRA_RATE_LIMIT', 1000))
                ->by($request->user()?->id ?: $request->ip());
        });
        // Corporate-specific rate limiter
        RateLimiter::for('corporate', function (Request $request) {
            return Limit::perMinute(env('CORPORATE_RATE_LIMIT', 1000))
                ->by($request->user()?->id ?: $request->ip());
        });
        // Corporate-specific rate limiter
        RateLimiter::for('corporate-stubs', function (Request $request) {
            return Limit::perMinute(env('CORPORATE_STUBS_RATE_LIMIT', 1000))
                ->by($request->user()?->id ?: $request->ip());
        });
        // drug rate limiter
        RateLimiter::for('drugs-stubs', function (Request $request) {
            return Limit::perMinute(env('DRUG_STUBS_RATE_LIMIT', 1000))
                ->by($request->user()?->id ?: $request->ip());
        });
         // forms rate limiter
        RateLimiter::for('forms-stubs', function (Request $request) {
            return Limit::perMinute(env('FORMS_STUBS_RATE_LIMIT', 1000))
                ->by($request->user()?->id ?: $request->ip());
        });
        // Medical Condition rate limiter
        RateLimiter::for('medicalcondition-stubs', function (Request $request) {
            return Limit::perMinute(env('MEDOCALCONDITION_STUBS_RATE_LIMIT', 1000))
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
