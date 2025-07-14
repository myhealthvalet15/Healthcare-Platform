<?php

return [

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'corporate_admin_users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'passport',
            'provider' => 'corporate_admin_users',
        ],
        'employee_api' => [
            'driver' => 'passport',
            'provider' => 'employee_users',
        ],
    ],

    'providers' => [
        'corporate_admin_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\CorporateAdminUser::class,
        ],
        'employee_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Corporate\MasterUser::class,
        ],
    ],

    'passwords' => [
        'corporate_admin_users' => [
            'provider' => 'corporate_admin_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'employee_users' => [
            'provider' => 'employee_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
