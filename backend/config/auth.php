<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [

        /*
         * Web authentication default.
         */
        'guard' => env(
            'AUTH_GUARD',
            'web'
        ),

        /*
         * Password broker default.
         */
        'passwords' => env(
            'AUTH_PASSWORD_BROKER',
            'users'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Web:
    | Session authentication.
    |
    | API:
    | Laravel Sanctum authentication.
    |
    */

    'guards' => [

        'web' => [

            'driver' => 'session',

            'provider' => 'users',
        ],

        'sanctum' => [

            'driver' => 'sanctum',

            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'users' => [

            'driver' => 'eloquent',

            'model' => env(
                'AUTH_MODEL',
                User::class
            ),
        ],

        /*
        'users' => [

            'driver' => 'database',

            'table' => 'users',
        ],
        */
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [

        'users' => [

            'provider' => 'users',

            'table' => env(
                'AUTH_PASSWORD_RESET_TOKEN_TABLE',
                'password_reset_tokens'
            ),

            'expire' => env(
            'AUTH_PASSWORD_RESET_EXPIRE',
            60
        ),

        'throttle' => env(
            'AUTH_PASSWORD_RESET_THROTTLE',
            60
        ),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env(
        'AUTH_PASSWORD_TIMEOUT',
        10800
    ),

    /*
    |--------------------------------------------------------------------------
    | Additional Authentication Settings
    |--------------------------------------------------------------------------
    |
    | Digunakan oleh AuthService untuk menentukan apakah user
    | wajib melakukan verifikasi email sebelum login.
    |
    */

    'require_verified_email' => env(
        'AUTH_REQUIRE_VERIFIED_EMAIL',
        false
    ),

];