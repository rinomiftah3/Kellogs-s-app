<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

use App\Http\Resources\V1\RoleResource;

use App\Services\RoleService;

use App\Traits\ApiResponse;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly RoleService $roleService
    ) {
    }

    /**
     * Display role listing.
     */
    public function index(
        Request $request
    ) {

        $roles = $this->roleService->paginate(
            filters: [
                'search' => $request->input(
                    'search'
                ),
            ],
            perPage: min(
                (int) $request->input(
                    'per_page',
                    config(
                        'app.default_per_page',
                        15
                    )
                ),
                config(
                    'app.max_per_page',
                    100
                )
            )
        );

        $roles->setCollection(
            RoleResource::collection(
                $roles->getCollection()
            )->collection
        );

        return $this->paginatedResponse(
            $roles,
            'Data role berhasil diambil'
        );
    }

    /**
     * Store role.
     */
    public function store(
        StoreRoleRequest $request
    ) {

        $role = $this->roleService->create(
            data: $request->validated(),
            actor: $request->user(),
            request: $request
        );

        return $this->createdResponse(
            new RoleResource(
                $role
            ),
            'Role berhasil dibuat'
        );
    }

    /**
     * Show role detail.
     */
    public function show(
        Role $role
    ) {

        return $this->successResponse(
            new RoleResource(
                $this->roleService->find(
                    $role
                )
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

        $role = $this->roleService->update(
            role: $role,
            data: $request->validated(),
            actor: $request->user(),
            request: $request
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

        $this->roleService->delete(
            role: $role,
            actor: $request->user(),
            request: $request
        );

        return $this->deletedResponse(
            'Role berhasil dihapus'
        );
    }
}