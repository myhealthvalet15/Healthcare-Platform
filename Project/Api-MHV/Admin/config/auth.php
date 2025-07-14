<?php

return [

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'mhv_admins', // Updated default password reset provider
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'mhv_admins',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'mhv_admins',
        ],

        'mhvadmin' => [
            'driver' => 'session',
            'provider' => 'mhv_admins',
        ],

        'corporate_admin' => [
            'driver' => 'passport',  // Using Passport for API auth
            'provider' => 'mhv_admins',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'mhv_admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Mhvadmin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'mhv_admins' => [
            'provider' => 'mhv_admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'mhv_admins' => [
            'provider' => 'mhv_admins', // Added for password resets
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
