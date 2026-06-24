<?php

namespace App\Services;

use App\Models\Activity;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Cache;

class ActivityLogService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'causer',
        'subject',
    ];

    /**
     * Get paginated activity logs.
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

            ->with($this->relations)

            ->when(
                filled($filters['search'] ?? null),
                function ($query) use ($filters) {

                    $search = trim(
                        $filters['search']
                    );

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
                fn ($query)
                    => $query->event(
                        $filters['event']
                    )
            )

            ->when(
                filled($filters['log_name'] ?? null),
                fn ($query)
                    => $query->logName(
                        $filters['log_name']
                    )
            )

            ->when(
                filled($filters['causer_id'] ?? null),
                fn ($query)
                    => $query->causer(
                        $filters['causer_id']
                    )
            )

            ->when(
                filled($filters['subject_id'] ?? null),
                fn ($query)
                    => $query->where(
                        'subject_id',
                        $filters['subject_id']
                    )
            )

            ->when(
                filled($filters['subject_type'] ?? null),
                fn ($query)
                    => $query->where(
                        'subject_type',
                        $filters['subject_type']
                    )
            )

            ->when(
                filled($filters['date_from'] ?? null),
                fn ($query)
                    => $query->whereDate(
                        'created_at',
                        '>=',
                        $filters['date_from']
                    )
            )

            ->when(
                filled($filters['date_to'] ?? null),
                fn ($query)
                    => $query->whereDate(
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
     * Get all activity logs.
     */
    public function all(
        array $filters = []
    ): Collection {

        return Activity::query()

            ->with($this->relations)

            ->when(
                filled($filters['event'] ?? null),
                fn ($query)
                    => $query->event(
                        $filters['event']
                    )
            )

            ->when(
                filled($filters['log_name'] ?? null),
                fn ($query)
                    => $query->logName(
                        $filters['log_name']
                    )
            )

            ->latest()

            ->get();
    }
    /**
     * Find activity by ID.
     */
    public function find(
        int $id
    ): ?Activity {

        return Activity::query()

            ->with($this->relations)

            ->find($id);
    }

    /**
     * Find activity or fail.
     */
    public function findOrFail(
        int $id
    ): Activity {

        return Activity::query()

            ->with($this->relations)

            ->findOrFail($id);
    }

    /**
     * Get latest activities.
     */
    public function latest(
        int $limit = 10
    ): Collection {

        return Cache::remember(

            "activity.latest.{$limit}",

            now()->addMinutes(5),

            fn () => Activity::query()

                ->with($this->relations)

                ->latest()

                ->limit($limit)

                ->get()
        );
    }

    /**
     * Create activity log.
     */
    public function log(
        string $description,
        ?string $event = null,
        ?string $logName = null,
        ?Model $subject = null,
        ?Model $causer = null,
        ?array $attributeChanges = null,
        ?array $properties = null,
        ?string $batchUuid = null
    ): Activity {

        $activity = Activity::create([

            /*
            |--------------------------------------------------------------------------
            | Activity Information
            |--------------------------------------------------------------------------
            */

            'log_name'
                => $logName,

            'description'
                => trim($description),

            'event'
                => $event,

            /*
            |--------------------------------------------------------------------------
            | Subject
            |--------------------------------------------------------------------------
            */

            'subject_type'
                => $subject?->getMorphClass(),

            'subject_id'
                => $subject?->getKey(),

            /*
            |--------------------------------------------------------------------------
            | Causer
            |--------------------------------------------------------------------------
            */

            'causer_type'
                => $causer?->getMorphClass(),

            'causer_id'
                => $causer?->getKey(),

            /*
            |--------------------------------------------------------------------------
            | Activity Data
            |--------------------------------------------------------------------------
            */

            'attribute_changes'
                => $attributeChanges,

            'properties'
                => $properties,

            /*
            |--------------------------------------------------------------------------
            | Batch
            |--------------------------------------------------------------------------
            */

            'batch_uuid'
                => $batchUuid,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Clear Activity Cache
        |--------------------------------------------------------------------------
        */

        $this->clearCaches();

        Cache::forget(
            'activity.latest.10'
        );

        return $activity

            ->fresh()

            ->load($this->relations);
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

                    'total_logs'
                        => Activity::query()
                            ->count(),

                    'today_logs'
                        => Activity::query()
                            ->today()
                            ->count(),

                    'this_week_logs'
                        => Activity::query()
                            ->thisWeek()
                            ->count(),

                    'this_month_logs'
                        => Activity::query()
                            ->thisMonth()
                            ->count(),

                    'login_logs'
                        => Activity::query()
                            ->event(
                                Activity::EVENT_LOGIN
                            )
                            ->count(),

                    'logout_logs'
                        => Activity::query()
                            ->event(
                                Activity::EVENT_LOGOUT
                            )
                            ->count(),

                    'created_logs'
                        => Activity::query()
                            ->event(
                                Activity::EVENT_CREATED
                            )
                            ->count(),

                    'updated_logs'
                        => Activity::query()
                            ->event(
                                Activity::EVENT_UPDATED
                            )
                            ->count(),

                    'deleted_logs'
                        => Activity::query()
                            ->event(
                                Activity::EVENT_DELETED
                            )
                            ->count(),

                    'approved_logs'
                        => Activity::query()
                            ->event(
                                Activity::EVENT_APPROVED
                            )
                            ->count(),

                    'rejected_logs'
                        => Activity::query()
                            ->event(
                                Activity::EVENT_REJECTED
                            )
                            ->count(),

                    'published_logs'
                        => Activity::query()
                            ->event(
                                Activity::EVENT_PUBLISHED
                            )
                            ->count(),

                    'cancelled_logs'
                        => Activity::query()
                            ->event(
                                Activity::EVENT_CANCELLED
                            )
                            ->count(),

                    'system_logs'
                        => Activity::query()

                            ->whereNull(
                                'causer_id'
                            )

                            ->count(),

                    'user_logs'
                        => Activity::query()

                            ->whereNotNull(
                                'causer_id'
                            )

                            ->count(),
                ];
            }
        );
    }

    /**
     * Available events.
     */
    public function availableEvents(): array
    {
        return Cache::remember(

            'activity.available_events',

            now()->addMinutes(30),

            fn () => Activity::query()

                ->whereNotNull(
                    'event'
                )

                ->distinct()

                ->orderBy(
                    'event'
                )

                ->pluck(
                    'event'
                )

                ->values()

                ->toArray()
        );
    }

    /**
     * Available log names.
     */
    public function availableLogNames(): array
    {
        return Cache::remember(

            'activity.available_log_names',

            now()->addMinutes(30),

            fn () => Activity::query()

                ->whereNotNull(
                    'log_name'
                )

                ->distinct()

                ->orderBy(
                    'log_name'
                )

                ->pluck(
                    'log_name'
                )

                ->values()

                ->toArray()
        );
    }
    /**
     * Clean old activity logs.
     */
    public function clean(
        int $days = 365
    ): int {

        $deleted = Activity::query()

            ->where(
                'created_at',
                '<',
                now()->subDays($days)
            )

            ->delete();

        $this->clearCaches();

        return $deleted;
    }

    /**
     * Truncate all activity logs.
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

                'today'
                    => Activity::query()
                        ->today()
                        ->count(),

                'this_week'
                    => Activity::query()
                        ->thisWeek()
                        ->count(),

                'this_month'
                    => Activity::query()
                        ->thisMonth()
                        ->count(),

                'latest'
                    => $this->latest(5),
            ]
        );
    }

    /**
     * Clear activity caches.
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
            'activity.available_events'
        );

        Cache::forget(
            'activity.available_log_names'
        );

        Cache::forget(
            'dashboard.overview'
        );

        /*
        |--------------------------------------------------------------------------
        | Clear latest caches
        |--------------------------------------------------------------------------
        |
        | latest() menggunakan key dinamis:
        | activity.latest.{limit}
        |
        | Yang umum dipakai kita bersihkan
        | untuk menghindari data stale.
        |
        */

        foreach ([5, 10, 15, 20, 50] as $limit) {

            Cache::forget(
                "activity.latest.{$limit}"
            );
        }
    }
}