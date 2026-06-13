<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;

use Illuminate\Http\Request;

use App\Services\UserService;

use App\Traits\ApiResponse;

use App\Http\Controllers\Controller;

use App\Http\Resources\V1\UserResource;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * User list.
     */
    public function index(
        Request $request
    ) {

        $users = $this->userService->paginate(
            filters: $request->only([
                'search',
            ]),
            perPage: min(
                (int) $request->get(
                    'per_page',
                    10
                ),
                100
            )
        );

        return $this->successResponse(
            UserResource::collection(
                $users
            ),
            'Data user berhasil diambil'
        );
    }

    /**
     * Create user.
     */
    public function store(
        StoreUserRequest $request
    ) {

        $user = $this->userService->create(
            data: $request->validated(),
            actor: $request->user(),
            request: $request
        );

        return $this->successResponse(
            new UserResource(
                $user
            ),
            'User berhasil dibuat',
            201
        );
    }

    /**
     * User detail.
     */
    public function show(
        User $user
    ) {

        return $this->successResponse(
            new UserResource(
                $this->userService->find(
                    $user
                )
            ),
            'Detail user berhasil diambil'
        );
    }

    /**
     * Update user.
     */
    public function update(
        UpdateUserRequest $request,
        User $user
    ) {

        $updatedUser =
            $this->userService->update(
                user: $user,
                data: $request->validated(),
                actor: $request->user(),
                request: $request
            );

        return $this->successResponse(
            new UserResource(
                $updatedUser
            ),
            'User berhasil diperbarui'
        );
    }

    /**
     * Delete user.
     */
    public function destroy(
        User $user,
        Request $request
    ) {

        $this->userService->delete(
            user: $user,
            actor: $request->user(),
            request: $request
        );

        return $this->successResponse(
            null,
            'User berhasil dihapus'
        );
    }
}