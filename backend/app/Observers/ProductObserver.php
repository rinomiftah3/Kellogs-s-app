<?php

namespace App\Observers;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;

use App\Models\Product;

use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Dashboard cache key.
     */
    private const DASHBOARD_CACHE_KEY =
        'dashboard.overview';

    /**
     * Product statistics cache key.
     */
    private const PRODUCT_STATISTICS_CACHE_KEY =
        'product.statistics';

    /**
     * Handle product created event.
     */
    public function created(
        Product $product
    ): void {

        $this->clearCaches();

        ProductCreated::dispatch(
            $product
        );
    }

    /**
     * Handle product updated event.
     */
    public function updated(
        Product $product
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Dispatch event only when important attributes changed
        |--------------------------------------------------------------------------
        */

        if (
            $product->wasChanged([
                'category_id',
                'name',
                'slug',
                'short_description',
                'description',
                'thumbnail',
                'status',
                'is_featured',
                'is_active',
                'published_at',
            ])
        ) {

            ProductUpdated::dispatch(
                $product
            );
        }

        $this->clearCaches();
    }

    /**
     * Handle product deleted event.
     */
    public function deleted(
        Product $product
    ): void {

        $this->clearCaches();
    }

    /**
     * Handle product restored event.
     */
    public function restored(
        Product $product
    ): void {

        $this->clearCaches();
    }

    /**
     * Handle product force deleted event.
     */
    public function forceDeleted(
        Product $product
    ): void {

        $this->clearCaches();
    }

    /**
     * Clear related caches.
     */
    private function clearCaches(): void
    {
        Cache::forget(
            self::DASHBOARD_CACHE_KEY
        );

        Cache::forget(
            self::PRODUCT_STATISTICS_CACHE_KEY
        );
    }
}