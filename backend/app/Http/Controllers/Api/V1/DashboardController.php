<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Services\DashboardService;

use App\Traits\ApiResponse;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    /**
     * Dashboard overview.
     */
    public function index()
    {
        return $this->successResponse(
            $this->dashboardService->overview(),
            'Dashboard data berhasil diambil'
        );
    }
}