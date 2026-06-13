<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;

use Carbon\Carbon;

use Illuminate\Support\Facades\Cache;

use Spatie\Activitylog\Models\Activity;

class DashboardService
{
    /**
     * Cache key.
     */
    private const CACHE_KEY =
        'dashboard.overview';

    /**
     * Cache TTL (seconds).
     */
    private const CACHE_TTL = 300;

    /**
     * Dashboard overview.
     */
    public function overview(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL,
            fn () => [

                'statistics' =>
                    $this->statistics(),

                'growth' =>
                    $this->growth(),

                'charts' =>
                    $this->charts(),

                'low_stock_products' =>
                    $this->lowStockProducts(),

                'recent_activities' =>
                    $this->recentActivities(),
            ]
        );
    }

    /**
     * Statistics widget.
     */
    private function statistics(): array
    {
        return [

            'users' =>
                User::count(),

            'categories' =>
                Category::count(),

            'products' =>
                Product::count(),

            'activity_logs' =>
                Activity::count(),
        ];
    }

    /**
     * Growth widget.
     */
    private function growth(): array
    {
        return [

            'new_users_today' =>
                User::whereDate(
                    'created_at',
                    today()
                )->count(),

            'new_products_today' =>
                Product::whereDate(
                    'created_at',
                    today()
                )->count(),

            'new_activities_today' =>
                Activity::whereDate(
                    'created_at',
                    today()
                )->count(),
        ];
    }

    /**
     * Dashboard charts.
     */
    private function charts(): array
    {
        return [

            'activity_chart' =>
                $this->activityChart(),

            'user_chart' =>
                $this->userChart(),

            'product_chart' =>
                $this->productChart(),
        ];
    }

    /**
     * Activity chart.
     */
    private function activityChart(): array
    {
        $chart = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::now()
                ->subDays($i);

            $chart[] = [

                'date' =>
                    $date->format('d M'),

                'total' =>
                    Activity::whereDate(
                        'created_at',
                        $date->toDateString()
                    )->count(),
            ];
        }

        return $chart;
    }

    /**
     * User chart.
     */
    private function userChart(): array
    {
        $chart = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::now()
                ->subDays($i);

            $chart[] = [

                'date' =>
                    $date->format('d M'),

                'total' =>
                    User::whereDate(
                        'created_at',
                        $date->toDateString()
                    )->count(),
            ];
        }

        return $chart;
    }

    /**
     * Product chart.
     */
    private function productChart(): array
    {
        return Category::query()

            ->withCount(
                'products'
            )

            ->get()

            ->map(
                fn (
                    Category $category
                ) => [

                    'name' =>
                        $category->name,

                    'total' =>
                        $category->products_count,
                ]
            )

            ->values()

            ->toArray();
    }

    /**
     * Low stock widget.
     */
    private function lowStockProducts()
    {
        return Product::query()

            ->with(
                'category'
            )

            ->where(
                'stock',
                '<=',
                10
            )

            ->latest()

            ->limit(10)

            ->get([
                'id',
                'category_id',
                'name',
                'stock',
            ]);
    }

    /**
     * Recent activity widget.
     */
    private function recentActivities()
    {
        return Activity::query()

            ->with(
                'causer'
            )

            ->latest()

            ->limit(10)

            ->get();
    }

    /**
     * Clear dashboard cache.
     */
    public function clearCache(): void
    {
        Cache::forget(
            self::CACHE_KEY
        );
    }
}