<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\Bridge\UserRepository;
use App\Auth\CustomUserRepository;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Enable password grant
        // Passport::routes();
        Passport::enablePasswordGrant();

        // Optional: Token expiry settings
        // Passport::tokensExpireIn(now()->addDays(15));
        // Passport::refreshTokensExpireIn(now()->addDays(30));
        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }

    /**
     * Register custom bindings.
     */
    public function register(): void
    {
        // Bind custom user resolver for multi-provider auth
        $this->app->bind(UserRepository::class, CustomUserRepository::class);
    }
}
