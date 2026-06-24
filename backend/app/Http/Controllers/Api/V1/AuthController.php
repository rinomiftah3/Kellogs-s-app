<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\LoginRequest;

use App\Services\AuthService;

use App\Traits\ApiResponse;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    /**
     * Login user.
     */
    public function login(
        LoginRequest $request
    ): JsonResponse {

        $result = $this->authService
            ->login(
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

        return $this->successResponse(
            $result,
            'Login berhasil'
        );
    }

    /**
     * Get authenticated user.
     */
    public function me(
        Request $request
    ): JsonResponse {

        $user = $this->authService
            ->me(
                $request->user()
            );

        return $this->successResponse(
            $user,
            'Data user berhasil diambil'
        );
    }

    /**
     * Logout current device.
     */
    public function logout(
        Request $request
    ): JsonResponse {

        $this->authService
            ->logout(
                $request->user(),
                $request->ip(),
                $request->userAgent()
            );

        return $this->successResponse(
            null,
            'Logout berhasil'
        );
    }

    /**
     * Logout all devices.
     */
    public function logoutAll(
        Request $request
    ): JsonResponse {

        $this->authService
            ->logoutAll(
                $request->user(),
                $request->ip(),
                $request->userAgent()
            );

        return $this->successResponse(
            null,
            'Logout semua device berhasil'
        );
    }
}