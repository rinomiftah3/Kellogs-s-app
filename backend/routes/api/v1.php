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
use App\Http\Controllers\Api\V1\ProductImageController;
use App\Http\Controllers\Api\V1\ProductOptionController;
use App\Http\Controllers\Api\V1\ProductOptionValueController;
use App\Http\Controllers\Api\V1\ProductSkuController;
use App\Http\Controllers\Api\V1\ProductReviewController;
use App\Http\Controllers\Api\V1\ProductReviewImageController;

use App\Http\Controllers\Api\V1\CustomerProfileController;
use App\Http\Controllers\Api\V1\CustomerAddressController;

use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CheckoutController;

use App\Http\Controllers\Api\V1\OrderController;

use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PaymentMethodController;

use App\Http\Controllers\Api\V1\VoucherController;

use App\Http\Controllers\Api\V1\LoyaltyPointController;

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
    ])->group(function () {

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

            Route::get(
                '/{role}',
                'show'
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
        | Product Images
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'products'
        )
        ->controller(
            ProductImageController::class
        )
        ->group(function () {

            Route::get(
                '/{product}/images',
                'index'
            );

            Route::post(
                '/{product}/images',
                'store'
            );

            Route::get(
                '/images/{productImage}',
                'show'
            );

            Route::put(
                '/images/{productImage}',
                'update'
            );

            Route::delete(
                '/images/{productImage}',
                'destroy'
            );

            Route::patch(
                '/images/{productImage}/primary',
                'setPrimary'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Product Options
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'product-options'
        )
        ->controller(
            ProductOptionController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/',
                'store'
            );

            Route::get(
                '/{productOption}',
                'show'
            );

            Route::put(
                '/{productOption}',
                'update'
            );

            Route::delete(
                '/{productOption}',
                'destroy'
            );

            Route::patch(
                '/{productOption}/activate',
                'activate'
            );

            Route::patch(
                '/{productOption}/deactivate',
                'deactivate'
            );

            Route::patch(
                '/{productOption}/required',
                'markAsRequired'
            );

            Route::patch(
                '/{productOption}/optional',
                'markAsOptional'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Product Option Values
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'product-option-values'
        )
        ->controller(
            ProductOptionValueController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/',
                'store'
            );

            Route::get(
                '/used',
                'used'
            );

            Route::get(
                '/unused',
                'unused'
            );

            Route::get(
                '/{productOptionValue}',
                'show'
            );

            Route::put(
                '/{productOptionValue}',
                'update'
            );

            Route::delete(
                '/{productOptionValue}',
                'destroy'
            );

            Route::patch(
                '/{productOptionValue}/activate',
                'activate'
            );

            Route::patch(
                '/{productOptionValue}/deactivate',
                'deactivate'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Product SKUs
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'product-skus'
        )
        ->controller(
            ProductSkuController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/',
                'store'
            );

            Route::get(
                '/{product_sku}',
                'show'
            );

            Route::put(
                '/{product_sku}',
                'update'
            );

            Route::delete(
                '/{product_sku}',
                'destroy'
            );

            Route::patch(
                '/{product_sku}/activate',
                'activate'
            );

            Route::patch(
                '/{product_sku}/deactivate',
                'deactivate'
            );

            Route::patch(
                '/{product_sku}/publish',
                'publish'
            );

            Route::patch(
                '/{product_sku}/archive',
                'archive'
            );

            Route::patch(
                '/{product_sku}/default',
                'setDefault'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Product Reviews
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'product-reviews'
        )
        ->controller(
            ProductReviewController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/',
                'store'
            );

            Route::get(
                '/{productReview}',
                'show'
            );

            Route::put(
                '/{productReview}',
                'update'
            );

            Route::delete(
                '/{productReview}',
                'destroy'
            );

            Route::patch(
                '/{productReview}/approve',
                'approve'
            );

            Route::patch(
                '/{productReview}/reject',
                'reject'
            );

            Route::post(
                '/{productReview}/helpful',
                'increaseHelpful'
            );
            
        });
        Route::delete(
                '/product-review-images/{productReviewImage}',
                [
                    ProductReviewImageController::class,
                    'destroy',
                ]
            );
    
            /*
        |--------------------------------------------------------------------------
        | Customer Profiles
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'customer-profiles'
        )
        ->controller(
            CustomerProfileController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/',
                'store'
            );

            Route::get(
                '/{customerProfile}',
                'show'
            );

            Route::put(
                '/{customerProfile}',
                'update'
            );

            Route::delete(
                '/{customerProfile}',
                'destroy'
            );

            Route::patch(
                '/{customerProfile}/activate',
                'activate'
            );

            Route::patch(
                '/{customerProfile}/deactivate',
                'deactivate'
            );

            Route::patch(
                '/{customerProfile}/membership',
                'changeMembership'
            );

            Route::patch(
                '/{customerProfile}/increase-points',
                'increasePoints'
            );

            Route::patch(
                '/{customerProfile}/increase-orders',
                'increaseOrderCount'
            );

            Route::patch(
                '/{customerProfile}/last-order',
                'updateLastOrder'
            );

            Route::patch(
                '/{customerProfile}/subscribe-email',
                'subscribeEmail'
            );

            Route::patch(
                '/{customerProfile}/unsubscribe-email',
                'unsubscribeEmail'
            );

            Route::patch(
                '/{customerProfile}/subscribe-sms',
                'subscribeSms'
            );

            Route::patch(
                '/{customerProfile}/unsubscribe-sms',
                'unsubscribeSms'
            );

            Route::patch(
                '/{customerProfile}/subscribe-push',
                'subscribePush'
            );

            Route::patch(
                '/{customerProfile}/unsubscribe-push',
                'unsubscribePush'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Customer Addresses
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'customer-addresses'
        )
        ->controller(
            CustomerAddressController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/',
                'store'
            );

            Route::get(
                '/customer/{customerProfile}',
                'byCustomer'
            );

            Route::get(
                '/customer/{customerProfile}/default',
                'defaultAddress'
            );

            Route::get(
                '/{customerAddress}',
                'show'
            );

            Route::put(
                '/{customerAddress}',
                'update'
            );

            Route::delete(
                '/{customerAddress}',
                'destroy'
            );

            Route::patch(
                '/{customerAddress}/default',
                'setDefault'
            );

            Route::patch(
                '/{customerAddress}/activate',
                'activate'
            );

            Route::patch(
                '/{customerAddress}/deactivate',
                'deactivate'
            );
        });
        /*
        |--------------------------------------------------------------------------
        | Cart
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'carts'
        )
        ->controller(
            CartController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::get(
                '/my-cart',
                'myCart'
            );

            Route::get(
                '/{cart}',
                'show'
            );

            Route::post(
                '/add',
                'addToCart'
            );

            Route::patch(
                '/items/{cartItem}',
                'updateItem'
            );

            Route::delete(
                '/items/{cartItem}',
                'removeItem'
            );

            Route::delete(
                '/items',
                'bulkRemoveItems'
            );

            Route::delete(
                '/clear',
                'clearCart'
            );

            Route::patch(
                '/items/{cartItem}/select',
                'selectItem'
            );

            Route::patch(
                '/items/{cartItem}/unselect',
                'unselectItem'
            );

            Route::patch(
                '/select-all',
                'selectAll'
            );

            Route::patch(
                '/unselect-all',
                'unselectAll'
            );

            Route::patch(
                '/touch',
                'touchActivity'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Checkout
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'checkouts'
        )
        ->controller(
            CheckoutController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/',
                'store'
            );

            Route::get(
                '/{checkout}',
                'show'
            );

            Route::post(
                '/apply-voucher',
                'applyVoucher'
            );

            Route::post(
                '/validate',
                'validateCheckout'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        Route::prefix('orders')
            ->controller(OrderController::class)
            ->group(function () {

                Route::get('/', 'index');

                Route::get('/statistics', 'statistics');

                Route::get('/my-orders', 'myOrders');

                Route::get('/my-orders/{order}', 'myOrder');

                Route::get('/{order}', 'show');

                Route::get('/{order}/history', 'history');

                Route::get('/{order}/payment', 'payment');

                Route::get('/{order}/tracking', 'tracking');

                Route::patch('/{order}/status', 'updateStatus');

                Route::patch('/{order}/tracking', 'updateTracking');

                Route::patch('/{order}/cancel', 'cancel');

                Route::post('/{order}/reorder', 'reorder');
            });
        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'payments'
        )
        ->controller(
            PaymentController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/',
                'store'
            );

            Route::get(
                '/{payment}',
                'show'
            );

            Route::patch(
                '/{payment}/status',
                'updateStatus'
            );

            Route::post(
                '/{payment}/refund',
                'refund'
            );

            Route::delete(
                '/{payment}',
                'destroy'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Payment Methods
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'payment-methods'
        )
        ->controller(
            PaymentMethodController::class
        )
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Available Gateways
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/gateways',
                'gateways'
            );

            /*
            |--------------------------------------------------------------------------
            | Available Methods by Gateway
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/methods/{gateway}',
                'methods'
            );

            /*
            |--------------------------------------------------------------------------
            | Generate Midtrans Snap Token
            |--------------------------------------------------------------------------
            */

            Route::post(
                '/{payment}/snap-token',
                'snapToken'
            );

            /*
            |--------------------------------------------------------------------------
            | Generate Midtrans Redirect URL
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/{payment}/redirect-url',
                'redirectUrl'
            );

            /*
            |--------------------------------------------------------------------------
            | Midtrans Callback / Webhook
            |--------------------------------------------------------------------------
            */

            Route::post(
                '/callback',
                'callback'
            );

            /*
            |--------------------------------------------------------------------------
            | Midtrans Configuration
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/configuration',
                'configuration'
            );
        });
        /*
        |--------------------------------------------------------------------------
        | Vouchers
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'vouchers'
        )
        ->controller(
            VoucherController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::get(
                '/public',
                'public'
            );

            Route::get(
                '/{voucher}',
                'show'
            );

            Route::post(
                '/',
                'store'
            );

            Route::put(
                '/{voucher}',
                'update'
            );

            Route::delete(
                '/{voucher}',
                'destroy'
            );

            Route::patch(
                '/{voucher}/activate',
                'activate'
            );

            Route::patch(
                '/{voucher}/deactivate',
                'deactivate'
            );

            Route::post(
                '/apply',
                'apply'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Loyalty Points
        |--------------------------------------------------------------------------
        */

        Route::prefix(
            'loyalty-points'
        )
        ->controller(
            LoyaltyPointController::class
        )
        ->group(function () {

            Route::get(
                '/',
                'index'
            );

            Route::post(
                '/',
                'store'
            );

            Route::get(
                '/my-balance',
                'myBalance'
            );

            Route::get(
                '/{loyaltyPoint}',
                'show'
            );

            Route::put(
                '/{loyaltyPoint}',
                'update'
            );

            Route::delete(
                '/{loyaltyPoint}',
                'destroy'
            );

            Route::patch(
                '/{loyaltyPoint}/activate',
                'activate'
            );

            Route::patch(
                '/{loyaltyPoint}/deactivate',
                'deactivate'
            );

            Route::patch(
                '/{loyaltyPoint}/publish',
                'publish'
            );

            Route::post(
                '/earn',
                'earn'
            );

            Route::post(
                '/redeem',
                'redeem'
            );

            Route::post(
                '/refund',
                'refund'
            );

            Route::post(
                '/bonus',
                'bonus'
            );

            Route::post(
                '/adjust',
                'adjust'
            );

            Route::post(
                '/expire',
                'expire'
            );

            Route::get(
                '/transactions',
                'transactions'
            );

            Route::patch(
                '/transactions/{transaction}/approve',
                'approveTransaction'
            );

            Route::patch(
                '/transactions/{transaction}/cancel',
                'cancelTransaction'
            );

            Route::patch(
                '/{loyaltyPoint}/upgrade-tier',
                'upgradeTier'
            );

            Route::post(
                '/downgrade-expired-tiers',
                'downgradeExpiredTiers'
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
            );

            Route::get(
                '/latest',
                'latest'
            );

            Route::get(
                '/statistics',
                'statistics'
            );

            Route::get(
                '/dashboard-summary',
                'dashboardSummary'
            );

            Route::get(
                '/available-events',
                'availableEvents'
            );

            Route::get(
                '/available-log-names',
                'availableLogNames'
            );

            Route::delete(
                '/clean',
                'clean'
            );

            Route::delete(
                '/truncate',
                'truncate'
            );

            Route::get(
                '/{activityLog}',
                'show'
            );
        });
    });
});