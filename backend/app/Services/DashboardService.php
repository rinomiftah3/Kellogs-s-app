<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\Payment;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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
        'users'          => User::count(),
        'customers'      => CustomerProfile::count(),
        'categories'     => Category::count(),
        'products'       => Product::count(),
        'orders'         => Order::count(),
        'payments'       => Payment::count(),
        'activity_logs'  => Activity::count(),
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
                    Carbon::today()
                )->count(),

            'new_products_today' =>
                Product::whereDate(
                    'created_at',
                    Carbon::today()
                )->count(),

            'new_activities_today' =>
                Activity::whereDate(
                    'created_at',
                    Carbon::today()
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

            'products_by_category_chart' =>
                $this->productsByCategoryChart(),
        ];
    }

    /**
     * Activity chart (last 7 days).
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
     * User registration chart (last 7 days).
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
     * Products grouped by category.
     */
    private function productsByCategoryChart(): array
    {
        return Category::query()
            ->withCount('products')
            ->orderByDesc(
                'products_count'
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
     * Recent activities widget.
     */
    private function recentActivities(): array
    {
        return Activity::query()
            ->with('causer')
            ->latest()
            ->limit(10)
            ->get()
            ->map(
                fn (
                    Activity $activity
                ) => [
                    'id' =>
                        $activity->id,

                    'event' =>
                        $activity->event,

                    'description' =>
                        $activity->description,

                    'causer' => [
                        'id' =>
                            $activity->causer?->id,

                        'name' =>
                            $activity->causer?->name,
                    ],

                    'created_at' =>
                        $activity->created_at
                            ?->toISOString(),
                ]
            )
            ->values()
            ->toArray();
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