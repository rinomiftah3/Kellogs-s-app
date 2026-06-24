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
            $user->hasRole(
                User::ROLE_SUPER_ADMIN
            )
        ) {

            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'users.view'
        );
    }

    /**
     * Determine whether the user can view the user.
     */
    public function view(
        User $user,
        User $model
    ): bool {

        return $user->can(
            'users.view'
        );
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'users.create'
        );
    }

    /**
     * Determine whether the user can update the user.
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
     * Determine whether the user can delete the user.
     */
    public function delete(
        User $user,
        User $model
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */

        if (
            ! $user->can(
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
                User::ROLE_SUPER_ADMIN
            )
        ) {

            $superAdminCount =
                User::role(
                    User::ROLE_SUPER_ADMIN
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
}