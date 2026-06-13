<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\Response;

class UserPolicy
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
     * View user list.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'users.view'
        );
    }

    /**
     * View user detail.
     */
    public function view(
        User $user,
        User $model
    ): bool {

        return
            $user->can(
                'users.view'
            );
    }

    /**
     * Create user.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'users.create'
        );
    }

    /**
     * Update user.
     */
    public function update(
        User $user,
        User $model
    ): bool {

        return $user->can(
            'users.update'
        );
    }

    /**
     * Delete user.
     */
    public function delete(
        User $user,
        User $model
    ): Response {

        if (
            !$user->can(
                'users.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus user.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Self Delete Protection
        |--------------------------------------------------------------------------
        */

        if (
            $user->id ===
            $model->id
        ) {

            return Response::deny(
                'Anda tidak dapat menghapus akun sendiri.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Last Super Admin Protection
        |--------------------------------------------------------------------------
        */

        if (
            $model->hasRole(
                'Super Admin'
            )
        ) {

            $superAdminCount =
                User::role(
                    'Super Admin'
                )->count();

            if (
                $superAdminCount <= 1
            ) {

                return Response::deny(
                    'Minimal harus ada satu Super Admin.'
                );
            }
        }

        return Response::allow();
    }

    /**
     * Restore user.
     */
    public function restore(
        User $user,
        User $model
    ): bool {

        return $user->can(
            'users.restore'
        );
    }

    /**
     * Force delete user.
     */
    public function forceDelete(
        User $user,
        User $model
    ): Response {

        if (
            !$user->can(
                'users.force-delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus permanen user.'
            );
        }

        if (
            $user->id ===
            $model->id
        ) {

            return Response::deny(
                'Anda tidak dapat menghapus akun sendiri.'
            );
        }

        return Response::allow();
    }

    /**
     * Change user role.
     */
    public function changeRole(
        User $user,
        User $model
    ): bool {

        return $user->can(
            'users.update'
        );
    }

    /**
     * Change user status.
     */
    public function changeStatus(
        User $user,
        User $model
    ): bool {

        return $user->can(
            'users.update'
        );
    }
}