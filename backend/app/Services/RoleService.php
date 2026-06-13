<?php

namespace App\Services;

use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use Illuminate\Validation\ValidationException;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    /**
     * Paginated roles.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ) {

        return Role::query()

            ->with('permissions')

            ->withCount([
                'users',
                'permissions',
            ])

            ->when(
                !empty($filters['search']),
                fn ($query) =>
                    $query->where(
                        'name',
                        'like',
                        '%' .
                        $filters['search']
                        . '%'
                    )
            )

            ->latest()

            ->paginate($perPage);
    }

    /**
     * Find role.
     */
    public function find(
        Role $role
    ): Role {

        return $role->load([
            'permissions',
        ]);
    }

    /**
     * Create role.
     */
    public function create(
        array $data,
        User $actor,
        Request $request
    ): Role {

        return DB::transaction(
            function () use (
                $data,
                $actor,
                $request
            ) {

                $role = Role::create([

                    'name' =>
                        $data['name'],

                    'guard_name' =>
                        'web',
                ]);

                $role->syncPermissions(
                    $data['permissions']
                    ?? []
                );

                $role->load(
                    'permissions'
                );

                activity()

                    ->causedBy($actor)

                    ->performedOn($role)

                    ->event(
                        'role_created'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'new' => [

                            'name' =>
                                $role->name,

                            'permissions' =>
                                $role
                                    ->permissions
                                    ->pluck('name')
                                    ->toArray(),
                        ],
                    ])

                    ->log(
                        'Role created'
                    );

                $this->clearCaches();

                return $role;
            }
        );
    }

    /**
     * Update role.
     */
    public function update(
        Role $role,
        array $data,
        User $actor,
        Request $request
    ): Role {

        return DB::transaction(
            function () use (
                $role,
                $data,
                $actor,
                $request
            ) {

                /*
                |--------------------------------------------------------------------------
                | Protect Super Admin Name
                |--------------------------------------------------------------------------
                */

                if (
                    strtolower(
                        $role->name
                    ) === 'super admin'
                    &&
                    strtolower(
                        $data['name']
                    ) !== 'super admin'
                ) {

                    throw ValidationException::withMessages([
                        'name' => [
                            'Role Super Admin tidak boleh diubah namanya.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Protect Super Admin Permissions
                |--------------------------------------------------------------------------
                */

                if (
                    strtolower(
                        $role->name
                    ) === 'super admin'
                ) {

                    $allPermissions =
                        Permission::query()
                            ->pluck('name')
                            ->toArray();

                    $incomingPermissions =
                        $data['permissions']
                        ?? [];

                    sort(
                        $allPermissions
                    );

                    sort(
                        $incomingPermissions
                    );

                    if (
                        $allPermissions
                        !==
                        $incomingPermissions
                    ) {

                        throw ValidationException::withMessages([
                            'permissions' => [
                                'Permission Super Admin tidak boleh dikurangi.',
                            ],
                        ]);
                    }
                }

                $oldData = [

                    'name' =>
                        $role->name,

                    'permissions' =>
                        $role
                            ->permissions
                            ->pluck('name')
                            ->toArray(),
                ];

                $role->update([

                    'name' =>
                        $data['name'],
                ]);

                $role->syncPermissions(
                    $data['permissions']
                    ?? []
                );

                $role->load(
                    'permissions'
                );

                activity()

                    ->causedBy($actor)

                    ->performedOn($role)

                    ->event(
                        'role_updated'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,

                        'new' => [

                            'name' =>
                                $role->name,

                            'permissions' =>
                                $role
                                    ->permissions
                                    ->pluck('name')
                                    ->toArray(),
                        ],
                    ])

                    ->log(
                        'Role updated'
                    );

                $this->clearCaches();

                return $role->fresh([
                    'permissions',
                ]);
            }
        );
    }

    /**
     * Delete role.
     */
    public function delete(
        Role $role,
        User $actor,
        Request $request
    ): void {

        DB::transaction(
            function () use (
                $role,
                $actor,
                $request
            ) {

                /*
                |--------------------------------------------------------------------------
                | Protect Super Admin Role
                |--------------------------------------------------------------------------
                */

                if (
                    strtolower(
                        $role->name
                    ) === 'super admin'
                ) {

                    throw ValidationException::withMessages([
                        'role' => [
                            'Role Super Admin tidak boleh dihapus.',
                        ],
                    ]);
                }

                $oldData = [

                    'name' =>
                        $role->name,

                    'permissions' =>
                        $role
                            ->permissions
                            ->pluck('name')
                            ->toArray(),
                ];

                activity()

                    ->causedBy($actor)

                    ->performedOn($role)

                    ->event(
                        'role_deleted'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,
                    ])

                    ->log(
                        'Role deleted'
                    );

                $role->delete();

                $this->clearCaches();
            }
        );
    }

    /**
     * Check protected role.
     */
    public function isProtectedRole(
        Role $role
    ): bool {

        return in_array(
            strtolower(
                $role->name
            ),
            [
                'super admin',
                'admin',
                'staff',
            ]
        );
    }

    /**
     * Clear caches.
     */
    private function clearCaches(): void
    {
        app(
            PermissionRegistrar::class
        )->forgetCachedPermissions();

        Cache::forget(
            'dashboard.overview'
        );
    }
}