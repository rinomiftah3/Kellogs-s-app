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
            $user->hasRole(
                User::ROLE_SUPER_ADMIN
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
            ! $user->can(
                'roles.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin mengubah role.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Protected Role Protection
        |--------------------------------------------------------------------------
        |
        | Sinkron dengan RoleService:
        | hanya Customer, Admin, Staff,
        | dan Super Admin yang dianggap
        | protected role.
        |
        | Business rule detail tetap
        | ditangani oleh service.
        |
        */

        if (
            $this->isProtectedRole(
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
            ! $user->can(
                'roles.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus role.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Protected Role Protection
        |--------------------------------------------------------------------------
        */

        if (
            $this->isProtectedRole(
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
            $role->users()->exists()
        ) {

            return Response::deny(
                'Role masih digunakan oleh user.'
            );
        }

        return Response::allow();
    }

    /**
     * Check protected role.
     */
    private function isProtectedRole(
        Role $role
    ): bool {

        return in_array(
            $role->name,
            [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_ADMIN,
                User::ROLE_STAFF,
                User::ROLE_CUSTOMER,
            ],
            true
        );
    }
}