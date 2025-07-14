<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Handles Trusting Proxies (e.g., AWS, Cloudflare)
        \App\Http\Middleware\TrustProxies::class,

        // Handles maintenance mode logic (during `artisan down`)
        \Illuminate\Http\Middleware\HandleMaintenanceMode::class,

        // Validates request sizes to prevent too large uploads
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trims extra spaces from user input
        \App\Http\Middleware\TrimStrings::class,

        // Converts empty input strings to null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * These middleware groups may be assigned to groups in routes/web.php or routes/api.php.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // Ensure CSRF token is valid
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Throttle API requests by default (e.g., 60 requests per minute)
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to individual routes or groups.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
      
        'verify' => \App\Http\Middleware\Authcheck::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to be executed in a specific order.
     *
     * @var array<int, class-string|string>
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        // \App\Http\Middleware\Authenticate::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
