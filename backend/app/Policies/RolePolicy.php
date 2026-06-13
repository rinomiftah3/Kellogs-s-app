<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\Response;

use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Super Admin bypass.
     */
    public function before(
        User $user,
        string $ability
    ): bool|null {

        if (
            method_exists(
                $user,
                'hasRole'
            ) &&
            $user->hasRole(
                'Super Admin'
            )
        ) {

            return true;
        }

        return null;
    }

    /**
     * View role list.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'roles.view'
        );
    }

    /**
     * View role detail.
     */
    public function view(
        User $user,
        Role $role
    ): bool {

        return $user->can(
            'roles.view'
        );
    }

    /**
     * Create role.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'roles.create'
        );
    }

    /**
     * Update role.
     */
    public function update(
        User $user,
        Role $role
    ): Response {

        if (
            !$user->can(
                'roles.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin mengubah role.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | System Role Protection
        |--------------------------------------------------------------------------
        */

        if (
            $this->isSystemRole(
                $role
            )
        ) {

            return Response::deny(
                'Role sistem tidak dapat diubah.'
            );
        }

        return Response::allow();
    }

    /**
     * Delete role.
     */
    public function delete(
        User $user,
        Role $role
    ): Response {

        if (
            !$user->can(
                'roles.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus role.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | System Role Protection
        |--------------------------------------------------------------------------
        */

        if (
            $this->isSystemRole(
                $role
            )
        ) {

            return Response::deny(
                'Role sistem tidak dapat dihapus.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Assigned User Protection
        |--------------------------------------------------------------------------
        */

        if (
            method_exists(
                $role,
                'users'
            ) &&
            $role->users()->exists()
        ) {

            return Response::deny(
                'Role masih digunakan oleh user.'
            );
        }

        return Response::allow();
    }

    /**
     * Restore role.
     */
    public function restore(
        User $user,
        Role $role
    ): bool {

        return $user->can(
            'roles.restore'
        );
    }

    /**
     * Force delete role.
     */
    public function forceDelete(
        User $user,
        Role $role
    ): Response {

        if (
            !$user->can(
                'roles.force-delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus permanen role.'
            );
        }

        if (
            $this->isSystemRole(
                $role
            )
        ) {

            return Response::deny(
                'Role sistem tidak dapat dihapus permanen.'
            );
        }

        return Response::allow();
    }

    /**
     * Manage role permissions.
     */
    public function syncPermissions(
        User $user,
        Role $role
    ): Response {

        if (
            !$user->can(
                'roles.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin mengelola permission role.'
            );
        }

        if (
            $this->isSystemRole(
                $role
            )
        ) {

            return Response::deny(
                'Permission role sistem tidak dapat diubah.'
            );
        }

        return Response::allow();
    }

    /**
     * Check system role.
     */
    private function isSystemRole(
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
}