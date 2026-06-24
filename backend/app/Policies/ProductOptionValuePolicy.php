<?php

namespace App\Policies;

use App\Models\ProductOptionValue;
use App\Models\User;

use Illuminate\Auth\Access\Response;

class ProductOptionValuePolicy
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
     * View option value list.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /**
     * View option value detail.
     */
    public function view(
        User $user,
        ProductOptionValue $productOptionValue
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /**
     * Create option value.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Update option value.
     */
    public function update(
        User $user,
        ProductOptionValue $productOptionValue
    ): Response {

        if (
            ! $user->can(
                'products.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin mengubah option value.'
            );
        }

        return Response::allow();
    }

    /**
     * Delete option value.
     */
    public function delete(
        User $user,
        ProductOptionValue $productOptionValue
    ): Response {

        if (
            ! $user->can(
                'products.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus option value.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SKU Usage Protection
        |--------------------------------------------------------------------------
        */

        if (
            $productOptionValue
                ->skuValues()
                ->exists()
        ) {

            return Response::deny(
                'Option value sudah digunakan oleh SKU.'
            );
        }

        return Response::allow();
    }

    /**
     * Activate option value.
     */
    public function activate(
        User $user,
        ProductOptionValue $productOptionValue
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Deactivate option value.
     */
    public function deactivate(
        User $user,
        ProductOptionValue $productOptionValue
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * View used option values.
     */
    public function used(
        User $user
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /**
     * View unused option values.
     */
    public function unused(
        User $user
    ): bool {

        return $user->can(
            'products.view'
        );
    }
}