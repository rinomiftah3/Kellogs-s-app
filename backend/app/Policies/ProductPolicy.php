<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

use Illuminate\Auth\Access\Response;

class ProductPolicy
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
     * View product list.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /**
     * View product detail.
     */
    public function view(
        User $user,
        Product $product
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /**
     * Create product.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'products.create'
        );
    }

    /**
     * Update product.
     */
    public function update(
        User $user,
        Product $product
    ): Response {

        if (
            ! $user->can(
                'products.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin mengubah produk.'
            );
        }

        return Response::allow();
    }

    /**
     * Delete product.
     */
    public function delete(
        User $user,
        Product $product
    ): Response {

        if (
            ! $user->can(
                'products.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus produk.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SKU Protection
        |--------------------------------------------------------------------------
        */

        if (
            $product->hasSku()
        ) {

            return Response::deny(
                'Produk tidak dapat dihapus karena masih memiliki SKU.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Review Protection
        |--------------------------------------------------------------------------
        */

        if (
            $product->hasReviews()
        ) {

            return Response::deny(
                'Produk tidak dapat dihapus karena masih memiliki review.'
            );
        }

        return Response::allow();
    }
}