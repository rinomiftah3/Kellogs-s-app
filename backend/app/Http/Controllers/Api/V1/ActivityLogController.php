<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Traits\ApiResponse;

use App\Services\ActivityLogService;

use App\Http\Controllers\Controller;

use App\Http\Resources\V1\ActivityLogResource;

use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ActivityLogService $activityLogService
    ) {}

    /**
     * Activity log list.
     */
    public function index(
        Request $request
    ) {

        $logs =
            $this->activityLogService->paginate(
                filters: [

                    'search' =>
                        $request->search,

                    'event' =>
                        $request->event,

                    'log_name' =>
                        $request->log_name,

                    'causer_id' =>
                        $request->causer_id,

                    'subject_id' =>
                        $request->subject_id,

                    'subject_type' =>
                        $request->subject_type,

                    'date_from' =>
                        $request->date_from,

                    'date_to' =>
                        $request->date_to,

                    'sort' =>
                        $request->get(
                            'sort',
                            'desc'
                        ),

                ],

                perPage: min(
                    max(
                        (int) $request->get(
                            'per_page',
                            15
                        ),
                        1
                    ),
                    100
                )
            );

        return $this->successResponse(
            [

                'items' =>
                    ActivityLogResource::collection(
                        $logs
                    ),

                'meta' => [

                    'current_page' =>
                        $logs->currentPage(),

                    'last_page' =>
                        $logs->lastPage(),

                    'per_page' =>
                        $logs->perPage(),

                    'total' =>
                        $logs->total(),

                    'from' =>
                        $logs->firstItem(),

                    'to' =>
                        $logs->lastItem(),
                ],
            ],
            'Activity logs berhasil diambil'
        );
    }

    /**
     * Activity log detail.
     */
    public function show(
        Activity $activity_log
    ) {

        $activity_log =
            $this->activityLogService->find(
                $activity_log
            );

        return $this->successResponse(
            new ActivityLogResource(
                $activity_log
            ),
            'Detail activity log berhasil diambil'
        );
    }

    /**
     * Activity log statistics.
     */
    public function statistics()
    {

        return $this->successResponse(
            $this->activityLogService
                ->statistics(),
            'Statistik activity log berhasil diambil'
        );
    }
}