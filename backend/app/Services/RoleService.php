<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    /**
     * System roles.
     */
    private const SYSTEM_ROLES = [
        User::ROLE_SUPER_ADMIN,
        User::ROLE_ADMIN,
        User::ROLE_STAFF,
        User::ROLE_CUSTOMER,
    ];

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
                filled($filters['search'] ?? null),
                fn ($query) => $query->where(
                    'name',
                    'like',
                    '%' . $filters['search'] . '%'
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
                /*
                |--------------------------------------------------------------------------
                | Prevent Recreating System Roles
                |--------------------------------------------------------------------------
                */

                if (
                    in_array(
                        $data['name'],
                        self::SYSTEM_ROLES,
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'name' => [
                            'Role sistem tidak dapat dibuat ulang.',
                        ],
                    ]);
                }

                $role = Role::create([
                    'name' => $data['name'],

                    'guard_name' => 'web',
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
                    $role->name === User::ROLE_SUPER_ADMIN
                    &&
                    $data['name'] !== User::ROLE_SUPER_ADMIN
                ) {
                    throw ValidationException::withMessages([
                        'name' => [
                            'Role Super Admin tidak boleh diubah namanya.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Converting Other Roles Into Super Admin
                |--------------------------------------------------------------------------
                */

                if (
                    $role->name !== User::ROLE_SUPER_ADMIN
                    &&
                    $data['name'] === User::ROLE_SUPER_ADMIN
                ) {
                    throw ValidationException::withMessages([
                        'name' => [
                            'Role Super Admin tidak dapat dibuat melalui perubahan nama.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Protect Super Admin Permissions
                |--------------------------------------------------------------------------
                */

                if (
                    $role->name === User::ROLE_SUPER_ADMIN
                ) {
                    $allPermissions = Permission::query()
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
                | Protect System Roles
                |--------------------------------------------------------------------------
                */

                if (
                    $this->isProtectedRole(
                        $role
                    )
                ) {
                    throw ValidationException::withMessages([
                        'role' => [
                            'Role sistem tidak boleh dihapus.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Deleting Assigned Roles
                |--------------------------------------------------------------------------
                */

                if (
                    $role->users()->exists()
                ) {
                    throw ValidationException::withMessages([
                        'role' => [
                            'Role masih digunakan oleh user.',
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
    private function isProtectedRole(
        Role $role
    ): bool {
        return in_array(
            $role->name,
            self::SYSTEM_ROLES,
            true
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