<?php

namespace App\Observers;

use App\Models\Category;

use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    /**
     * Dashboard cache key.
     */
    private const DASHBOARD_CACHE_KEY =
        'dashboard.overview';

    /**
     * Category statistics cache key.
     */
    private const CATEGORY_STATISTICS_CACHE_KEY =
        'category.statistics';

    /**
     * Handle category created event.
     */
    public function created(
        Category $category
    ): void {

        $this->clearCaches();
    }

    /**
     * Handle category updated event.
     */
    public function updated(
        Category $category
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Clear cache only when important attributes changed
        |--------------------------------------------------------------------------
        */

        if (
            ! $category->wasChanged([
                'name',
                'slug',
                'description',
                'image',
                'parent_id',
                'is_active',
                'sort_order',
            ])
        ) {
            return;
        }

        $this->clearCaches();
    }

    /**
     * Handle category deleted event.
     */
    public function deleted(
        Category $category
    ): void {

        $this->clearCaches();
    }

    /**
     * Handle category restored event.
     */
    public function restored(
        Category $category
    ): void {

        $this->clearCaches();
    }

    /**
     * Handle category force deleted event.
     */
    public function forceDeleted(
        Category $category
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
            self::CATEGORY_STATISTICS_CACHE_KEY
        );
    }
}