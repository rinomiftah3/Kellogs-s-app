<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Cache;

use Spatie\Activitylog\Models\Activity;

class ActivityLogService
{
    /**
     * Paginated activity logs.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        $sortDirection =
            ($filters['sort'] ?? 'desc') === 'asc'
                ? 'asc'
                : 'desc';

        return Activity::query()

            ->with([
                'causer',
                'subject',
            ])

            ->when(
                filled($filters['search'] ?? null),
                function ($query) use ($filters) {

                    $search =
                        $filters['search'];

                    $query->where(
                        function ($q) use ($search) {

                            $q->where(
                                'description',
                                'like',
                                "%{$search}%"
                            )

                            ->orWhere(
                                'event',
                                'like',
                                "%{$search}%"
                            )

                            ->orWhere(
                                'log_name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            )

            ->when(
                filled($filters['event'] ?? null),
                fn ($query) =>
                    $query->where(
                        'event',
                        $filters['event']
                    )
            )

            ->when(
                filled($filters['log_name'] ?? null),
                fn ($query) =>
                    $query->where(
                        'log_name',
                        $filters['log_name']
                    )
            )

            ->when(
                filled($filters['causer_id'] ?? null),
                fn ($query) =>
                    $query->where(
                        'causer_id',
                        $filters['causer_id']
                    )
            )

            ->when(
                filled($filters['subject_id'] ?? null),
                fn ($query) =>
                    $query->where(
                        'subject_id',
                        $filters['subject_id']
                    )
            )

            ->when(
                filled($filters['subject_type'] ?? null),
                fn ($query) =>
                    $query->where(
                        'subject_type',
                        $filters['subject_type']
                    )
            )

            ->when(
                filled($filters['date_from'] ?? null),
                fn ($query) =>
                    $query->whereDate(
                        'created_at',
                        '>=',
                        $filters['date_from']
                    )
            )

            ->when(
                filled($filters['date_to'] ?? null),
                fn ($query) =>
                    $query->whereDate(
                        'created_at',
                        '<=',
                        $filters['date_to']
                    )
            )

            ->orderBy(
                'created_at',
                $sortDirection
            )

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Find activity log.
     */
    public function find(
        Activity $activity
    ): Activity {

        return $activity->load([
            'causer',
            'subject',
        ]);
    }

    /**
     * Latest activities.
     */
    public function latest(
        int $limit = 10
    ) {

        return Cache::remember(
            "activity.latest.{$limit}",
            now()->addMinutes(5),

            fn () => Activity::query()

                ->with([
                    'causer',
                    'subject',
                ])

                ->latest()

                ->limit($limit)

                ->get()
        );
    }

    /**
     * Activity statistics.
     */
    public function statistics(): array
    {
        return Cache::remember(
            'activity.statistics',
            now()->addMinutes(10),

            function () {

                return [

                    'total_logs' =>
                        Activity::count(),

                    'today_logs' =>
                        Activity::whereDate(
                            'created_at',
                            today()
                        )->count(),

                    'this_week_logs' =>
                        Activity::where(
                            'created_at',
                            '>=',
                            now()->startOfWeek()
                        )->count(),

                    'this_month_logs' =>
                        Activity::where(
                            'created_at',
                            '>=',
                            now()->startOfMonth()
                        )->count(),

                    'login_logs' =>
                        Activity::where(
                            'event',
                            'login'
                        )->count(),

                    'logout_logs' =>
                        Activity::where(
                            'event',
                            'logout'
                        )->count(),

                    'created_logs' =>
                        Activity::where(
                            'event',
                            'like',
                            '%created%'
                        )->count(),

                    'updated_logs' =>
                        Activity::where(
                            'event',
                            'like',
                            '%updated%'
                        )->count(),

                    'deleted_logs' =>
                        Activity::where(
                            'event',
                            'like',
                            '%deleted%'
                        )->count(),
                ];
            }
        );
    }

    /**
     * Available events.
     */
    public function availableEvents(): array
    {
        return Activity::query()

            ->whereNotNull('event')

            ->distinct()

            ->orderBy('event')

            ->pluck('event')

            ->values()

            ->toArray();
    }

    /**
     * Available log names.
     */
    public function availableLogNames(): array
    {
        return Activity::query()

            ->whereNotNull('log_name')

            ->distinct()

            ->orderBy('log_name')

            ->pluck('log_name')

            ->values()

            ->toArray();
    }

    /**
     * Clean old logs.
     */
    public function clean(
        int $days = 365
    ): int {

        $deleted =
            Activity::query()

                ->where(
                    'created_at',
                    '<',
                    now()->subDays(
                        $days
                    )
                )

                ->delete();

        $this->clearCaches();

        return $deleted;
    }

    /**
     * Truncate all logs.
     */
    public function truncate(): void
    {
        Activity::truncate();

        $this->clearCaches();
    }

    /**
     * Dashboard summary.
     */
    public function dashboardSummary(): array
    {
        return Cache::remember(
            'activity.dashboard',
            now()->addMinutes(5),

            fn () => [

                'today' =>
                    Activity::whereDate(
                        'created_at',
                        today()
                    )->count(),

                'this_week' =>
                    Activity::where(
                        'created_at',
                        '>=',
                        now()->startOfWeek()
                    )->count(),

                'this_month' =>
                    Activity::where(
                        'created_at',
                        '>=',
                        now()->startOfMonth()
                    )->count(),
            ]
        );
    }

    /**
     * Clear caches.
     */
    private function clearCaches(): void
    {
        Cache::forget(
            'activity.statistics'
        );

        Cache::forget(
            'activity.dashboard'
        );

        Cache::forget(
            'dashboard.overview'
        );
    }
}