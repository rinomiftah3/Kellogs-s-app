<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

use App\Http\Resources\V1\UserResource;

use App\Models\User;

use App\Services\UserService;

use App\Traits\ApiResponse;

use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Display a listing of users.
     */
    public function index(
        Request $request
    ) {
        $users = $this->userService->paginate(
            filters: $request->only([
                'search',
            ]),
            perPage: min(
                $request->integer(
                    'per_page',
                    (int) env(
                        'DEFAULT_PER_PAGE',
                        15
                    )
                ),
                (int) env(
                    'MAX_PER_PAGE',
                    100
                )
            )
        );

        $users->setCollection(
            $users->getCollection()
                ->map(
                    fn (User $user) => new UserResource(
                        $user
                    )
                )
        );

        return $this->paginatedResponse(
            $users,
            'Data user berhasil diambil'
        );
    }

    /**
     * Store a newly created user.
     */
    public function store(
        StoreUserRequest $request
    ) {
        $user = $this->userService->create(
            data: $request->validated(),
            actor: $request->user(),
            request: $request
        );

        return $this->createdResponse(
            new UserResource(
                $user
            ),
            'User berhasil dibuat'
        );
    }

    /**
     * Display the specified user.
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
     * Update the specified user.
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
     * Remove the specified user.
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

        return $this->deletedResponse(
            'User berhasil dihapus'
        );
    }
}