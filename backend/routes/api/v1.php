<?php

use Illuminate\Support\Facades\Route;

use Spatie\Permission\Models\Permission;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProfileController;

use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\RoleController;

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;

use App\Http\Controllers\Api\V1\ActivityLogController;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/login',
        [AuthController::class, 'login']
    )->middleware(
        'throttle:login'
    );

    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'auth:sanctum',
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Auth
        |--------------------------------------------------------------------------
        */

        Route::controller(
            AuthController::class
        )->group(function () {

            Route::get(
                '/me',
                'me'
            );

            Route::post(
                '/logout',
                'logout'
            );

            Route::post(
                '/logout-all',
                'logoutAll'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'profile'
        )
        ->controller(
            ProfileController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'show'
            );

            Route::put(
                '/',
                'update'
            );

            Route::put(
                '/password',
                'updatePassword'
            );

            Route::post(
                '/avatar',
                'uploadAvatar'
            );

            Route::delete(
                '/avatar',
                'deleteAvatar'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )
        ->middleware(
            'permission:dashboard.view'
        );

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/permissions',
            function () {

                return response()->json([
                    'success' => true,

                    'message' =>
                        'Permissions berhasil diambil',

                    'data' =>
                        Permission::all(),
                ]);
            }
        )
        ->middleware(
            'permission:roles.view'
        );

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'roles'
        )
        ->controller(
            RoleController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            )
            ->middleware(
                'permission:roles.view'
            );

            Route::post(
                '/',
                'store'
            )
            ->middleware(
                'permission:roles.create'
            );

            Route::put(
                '/{role}',
                'update'
            )
            ->middleware(
                'permission:roles.update'
            );

            Route::delete(
                '/{role}',
                'destroy'
            )
            ->middleware(
                'permission:roles.delete'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'categories'
        )
        ->controller(
            CategoryController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            )
            ->middleware(
                'permission:categories.view'
            );

            Route::get(
                '/{category}',
                'show'
            )
            ->middleware(
                'permission:categories.view'
            );

            Route::post(
                '/',
                'store'
            )
            ->middleware(
                'permission:categories.create'
            );

            Route::put(
                '/{category}',
                'update'
            )
            ->middleware(
                'permission:categories.update'
            );

            Route::delete(
                '/{category}',
                'destroy'
            )
            ->middleware(
                'permission:categories.delete'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'products'
        )
        ->controller(
            ProductController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            )
            ->middleware(
                'permission:products.view'
            );

            Route::get(
                '/{product}',
                'show'
            )
            ->middleware(
                'permission:products.view'
            );

            Route::post(
                '/',
                'store'
            )
            ->middleware(
                'permission:products.create'
            );

            Route::put(
                '/{product}',
                'update'
            )
            ->middleware(
                'permission:products.update'
            );

            Route::delete(
                '/{product}',
                'destroy'
            )
            ->middleware(
                'permission:products.delete'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'users'
        )
        ->controller(
            UserController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            )
            ->middleware(
                'permission:users.view'
            );

            Route::get(
                '/{user}',
                'show'
            )
            ->middleware(
                'permission:users.view'
            );

            Route::post(
                '/',
                'store'
            )
            ->middleware(
                'permission:users.create'
            );

            Route::put(
                '/{user}',
                'update'
            )
            ->middleware(
                'permission:users.update'
            );

            Route::delete(
                '/{user}',
                'destroy'
            )
            ->middleware(
                'permission:users.delete'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Activity Logs
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'activity-logs'
        )
        ->controller(
            ActivityLogController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            )
            ->middleware(
                'permission:activity_logs.view'
            );

            Route::get(
                '/{activity_log}',
                'show'
            )
            ->middleware(
                'permission:activity_logs.view'
            );
        });
    });
});