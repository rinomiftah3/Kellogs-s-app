<?php

namespace App\Policies;

use App\Models\ProductOption;
use App\Models\User;

use Illuminate\Auth\Access\Response;

class ProductOptionPolicy
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
            )
            &&
            $user->hasRole(
                'Super Admin'
            )
        ) {

            return true;
        }

        return null;
    }

    /**
     * View product option list.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /**
     * View product option detail.
     */
    public function view(
        User $user,
        ProductOption $productOption
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /**
     * Create product option.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Update product option.
     */
    public function update(
        User $user,
        ProductOption $productOption
    ): Response {

        if (
            ! $user->can(
                'products.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin mengubah opsi produk.'
            );
        }

        return Response::allow();
    }

    /**
     * Delete product option.
     */
    public function delete(
        User $user,
        ProductOption $productOption
    ): Response {

        if (
            ! $user->can(
                'products.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus opsi produk.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Product Option Value Protection
        |--------------------------------------------------------------------------
        */

        if (
            $productOption
                ->values()
                ->exists()
        ) {

            return Response::deny(
                'Opsi produk masih memiliki nilai.'
            );
        }

        return Response::allow();
    }

    /**
     * Activate product option.
     */
    public function activate(
        User $user,
        ProductOption $productOption
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Deactivate product option.
     */
    public function deactivate(
        User $user,
        ProductOption $productOption
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Mark option as required.
     */
    public function markAsRequired(
        User $user,
        ProductOption $productOption
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Mark option as optional.
     */
    public function markAsOptional(
        User $user,
        ProductOption $productOption
    ): bool {

        return $user->can(
            'products.update'
        );
    }
}