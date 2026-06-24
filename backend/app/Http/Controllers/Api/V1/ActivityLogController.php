<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\FilterActivityLogRequest;

use App\Http\Resources\V1\ActivityLogResource;

use App\Models\Activity;

use App\Services\ActivityLogService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityLogController extends Controller implements HasMiddleware
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {
    }

    /**
     * Controller middleware.
     */
    public static function middleware(): array
{
    return [

        new Middleware(
            'permission:activity_logs.view',
            only: [
                'index',
                'show',
                'latest',
                'statistics',
                'dashboardSummary',
                'availableEvents',
                'availableLogNames',
            ]
        ),

        new Middleware(
            'permission:activity_logs.delete',
            only: [
                'clean',
                'truncate',
            ]
        ),
    ];
}

    /**
     * Display a listing of activity logs.
     */
    public function index(
        FilterActivityLogRequest $request
    ): JsonResponse {

        $filters = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Compatibility
        |--------------------------------------------------------------------------
        |
        | Service menggunakan key "sort"
        | sedangkan request menggunakan "sort_order".
        |
        */

        $filters['sort'] =
            $filters['sort_order']
            ?? 'desc';

        $activities = $this->activityLogService
            ->paginate(
                $filters,
                $filters['per_page'] ?? 25
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Activity logs retrieved successfully.',

            'data' => ActivityLogResource::collection(
                $activities
            ),

            'meta' => [

                'current_page' =>
                    $activities->currentPage(),

                'last_page' =>
                    $activities->lastPage(),

                'per_page' =>
                    $activities->perPage(),

                'total' =>
                    $activities->total(),
            ],
        ]);
    }

    /**
     * Display the specified activity log.
     */
    public function show(
        Activity $activityLog
    ): JsonResponse {

        $activity = $this->activityLogService
            ->findOrFail(
                $activityLog->id
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Activity log retrieved successfully.',

            'data' => new ActivityLogResource(
                $activity
            ),
        ]);
    }

    /**
     * Get latest activity logs.
     */
    public function latest(
        Request $request
    ): JsonResponse {

        $limit = min(
            max(
                (int) $request->integer(
                    'limit',
                    10
                ),
                1
            ),
            100
        );

        $activities = $this->activityLogService
            ->latest($limit);

        return response()->json([

            'success' => true,

            'message' =>
                'Latest activity logs retrieved successfully.',

            'data' => ActivityLogResource::collection(
                $activities
            ),
        ]);
    }

    /**
     * Get activity statistics.
     */
    public function statistics(): JsonResponse
    {
        return response()->json([

            'success' => true,

            'message' =>
                'Activity statistics retrieved successfully.',

            'data' => $this->activityLogService
                ->statistics(),
        ]);
    }

    /**
     * Get dashboard summary.
     */
    public function dashboardSummary(): JsonResponse
    {
        $summary = $this->activityLogService
            ->dashboardSummary();

        return response()->json([

            'success' => true,

            'message' =>
                'Dashboard summary retrieved successfully.',

            'data' => [

                'today' =>
                    $summary['today'],

                'this_week' =>
                    $summary['this_week'],

                'this_month' =>
                    $summary['this_month'],

                'latest' => ActivityLogResource::collection(
                    collect(
                        $summary['latest']
                    )
                ),
            ],
        ]);
    }

    /**
     * Get available events.
     */
    public function availableEvents(): JsonResponse
    {
        return response()->json([

            'success' => true,

            'message' =>
                'Available events retrieved successfully.',

            'data' => $this->activityLogService
                ->availableEvents(),
        ]);
    }

    /**
     * Get available log names.
     */
    public function availableLogNames(): JsonResponse
    {
        return response()->json([

            'success' => true,

            'message' =>
                'Available log names retrieved successfully.',

            'data' => $this->activityLogService
                ->availableLogNames(),
        ]);
    }

    /**
     * Clean old activity logs.
     */
    public function clean(
        Request $request
    ): JsonResponse {

        $validated = $request->validate([

            'days' => [
                'sometimes',
                'integer',
                'min:1',
            ],
        ]);

        $days = $validated['days']
            ?? 365;

        $deleted = $this->activityLogService
            ->clean($days);

        return response()->json([

            'success' => true,

            'message' =>
                'Old activity logs cleaned successfully.',

            'data' => [

                'deleted_count' =>
                    $deleted,

                'older_than_days' =>
                    $days,
            ],
        ]);
    }

    /**
     * Truncate all activity logs.
     */
    public function truncate(): JsonResponse
    {
        $this->activityLogService
            ->truncate();

        return response()->json([

            'success' => true,

            'message' =>
                'All activity logs have been deleted successfully.',
        ]);
    }
}