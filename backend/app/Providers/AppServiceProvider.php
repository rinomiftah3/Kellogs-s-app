<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\CheckoutItem;
use App\Models\CheckoutSession;
use App\Models\Courier;
use App\Models\CustomerAddress;
use App\Models\CustomerDevice;
use App\Models\CustomerNotification;
use App\Models\CustomerProfile;
use App\Models\Inventory;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\PaymentCallback;
use App\Models\PaymentTransaction;
use App\Models\PointTransaction;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductReview;
use App\Models\ProductReviewImage;
use App\Models\ProductSku;
use App\Models\ProductSkuValue;
use App\Models\PromoCategory;
use App\Models\PromoProduct;
use App\Models\PromoSku;
use App\Models\Promotion;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Models\ShippingMethod;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\Wishlist;

use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /*
    |--------------------------------------------------------------------------
    | Application Constants
    |--------------------------------------------------------------------------
    */

    private const LOGIN_RATE_LIMIT = 5;

    private const API_RATE_LIMIT = 120;

    private const SLOW_QUERY_MS = 500;

    private const VERY_SLOW_QUERY_MS = 1000;

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Force HTTPS
        |--------------------------------------------------------------------------
        */

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        /*
        |--------------------------------------------------------------------------
        | Default String Length
        |--------------------------------------------------------------------------
        */

        Schema::defaultStringLength(191);

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        Paginator::useBootstrapFive();

        /*
        |--------------------------------------------------------------------------
        | Observer Registration
        |--------------------------------------------------------------------------
        */

        User::observe(UserObserver::class);

        Category::observe(CategoryObserver::class);

        Product::observe(ProductObserver::class);

        /*
        |--------------------------------------------------------------------------
        | Morph Map
        |--------------------------------------------------------------------------
        */

        Relation::enforceMorphMap([

            'activity' => Activity::class,

            'user' => User::class,

            'category' => Category::class,
            'product' => Product::class,
            'product_image' => ProductImage::class,
            'product_option' => ProductOption::class,
            'product_option_value' => ProductOptionValue::class,
            'product_sku' => ProductSku::class,
            'product_sku_value' => ProductSkuValue::class,
            'product_review' => ProductReview::class,
            'product_review_image' => ProductReviewImage::class,

            'inventory' => Inventory::class,
            'stock_movement' => StockMovement::class,
            'stock_adjustment' => StockAdjustment::class,
            'stock_opname' => StockOpname::class,

            'customer_profile' => CustomerProfile::class,
            'customer_address' => CustomerAddress::class,
            'customer_device' => CustomerDevice::class,
            'customer_notification' => CustomerNotification::class,

            'wishlist' => Wishlist::class,
            'cart' => Cart::class,
            'cart_item' => CartItem::class,

            'checkout_session' => CheckoutSession::class,
            'checkout_item' => CheckoutItem::class,

            'voucher' => Voucher::class,
            'voucher_usage' => VoucherUsage::class,

            'promotion' => Promotion::class,
            'promo_category' => PromoCategory::class,
            'promo_product' => PromoProduct::class,
            'promo_sku' => PromoSku::class,

            'order' => Order::class,
            'order_item' => OrderItem::class,
            'order_history' => OrderHistory::class,
            'order_status_log' => OrderStatusLog::class,

            'payment' => Payment::class,
            'payment_transaction' => PaymentTransaction::class,
            'payment_callback' => PaymentCallback::class,

            'courier' => Courier::class,
            'shipping_method' => ShippingMethod::class,
            'shipment' => Shipment::class,
            'shipment_tracking' => ShipmentTracking::class,

            'loyalty_point' => LoyaltyPoint::class,
            'point_transaction' => PointTransaction::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Login Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('login', function (Request $request) {

            return Limit::perMinute(
                self::LOGIN_RATE_LIMIT
            )
                ->by(
                    strtolower(
                        (string) $request->input('email')
                    ) . '|' . $request->ip()
                )
                ->response(
                    fn () => response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam 1 menit.',
                    ], 429)
                );
        });

        /*
        |--------------------------------------------------------------------------
        | API Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('api', function (Request $request) {

            return Limit::perMinute(
                self::API_RATE_LIMIT
            )->by(
                $request->user()?->id
                    ?: $request->ip()
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Strict Development Mode
        |--------------------------------------------------------------------------
        */

        if (! app()->isProduction()) {

            Model::preventLazyLoading();

            Model::preventAccessingMissingAttributes();

            Model::preventSilentlyDiscardingAttributes();
        }

        /*
        |--------------------------------------------------------------------------
        | Slow Query Monitoring
        |--------------------------------------------------------------------------
        */

        DB::whenQueryingForLongerThan(
            self::SLOW_QUERY_MS,
            function (
                $connection,
                QueryExecuted $event
            ) {

                Log::warning(
                    'Slow Query Detected',
                    [
                        'sql' => $event->sql,
                        'bindings' => $event->bindings,
                        'time_ms' => $event->time,
                        'connection' => $connection->getName(),
                    ]
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Very Slow Query Monitoring
        |--------------------------------------------------------------------------
        */

        if (! app()->isProduction()) {

            DB::listen(
                function (QueryExecuted $query) {

                    if (
                        $query->time >= self::VERY_SLOW_QUERY_MS
                    ) {

                        Log::warning(
                            'Very Slow Query',
                            [
                                'sql' => $query->sql,
                                'bindings' => $query->bindings,
                                'time_ms' => $query->time,
                            ]
                        );
                    }
                }
            );
        }
    }
}