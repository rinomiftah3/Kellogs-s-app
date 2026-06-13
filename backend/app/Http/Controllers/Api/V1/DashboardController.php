<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponse;

use App\Services\DashboardService;

use App\Http\Controllers\Controller;

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