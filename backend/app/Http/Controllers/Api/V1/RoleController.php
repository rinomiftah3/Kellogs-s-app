<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponse;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

use App\Http\Resources\V1\RoleResource;

use App\Services\RoleService;

use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly RoleService $roleService
    ) {}

    /**
     * Display listing.
     */
    public function index(
        Request $request
    ) {

        $roles = $this->roleService
            ->paginate(
                filters: [
                    'search' =>
                        $request->get(
                            'search'
                        ),
                ],
                perPage: min(
                    (int) $request->get(
                        'per_page',
                        10
                    ),
                    100
                )
            );

        return $this->successResponse(
            RoleResource::collection(
                $roles
            ),
            'Data role berhasil diambil'
        );
    }

    /**
     * Store role.
     */
    public function store(
        StoreRoleRequest $request
    ) {

        $role = $this->roleService
            ->create(
                data:
                    $request->validated(),

                actor:
                    $request->user(),

                request:
                    $request
            );

        return $this->successResponse(
            new RoleResource(
                $role
            ),
            'Role berhasil dibuat',
            201
        );
    }

    /**
     * Show role.
     */
    public function show(
        Role $role
    ) {

        return $this->successResponse(
            new RoleResource(
                $this->roleService
                    ->find($role)
            ),
            'Detail role berhasil diambil'
        );
    }

    /**
     * Update role.
     */
    public function update(
        UpdateRoleRequest $request,
        Role $role
    ) {

        $role = $this->roleService
            ->update(
                role:
                    $role,

                data:
                    $request->validated(),

                actor:
                    $request->user(),

                request:
                    $request
            );

        return $this->successResponse(
            new RoleResource(
                $role
            ),
            'Role berhasil diperbarui'
        );
    }

    /**
     * Delete role.
     */
    public function destroy(
        Role $role,
        Request $request
    ) {

        $this->roleService
            ->delete(
                role:
                    $role,

                actor:
                    $request->user(),

                request:
                    $request
            );

        return $this->successResponse(
            null,
            'Role berhasil dihapus'
        );
    }
}