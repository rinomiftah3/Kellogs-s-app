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
            !$user->can(
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
            !$user->can(
                'products.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus produk.'
            );
        }

        return Response::allow();
    }

    /**
     * Restore product.
     */
    public function restore(
        User $user,
        Product $product
    ): bool {

        return $user->can(
            'products.restore'
        );
    }

    /**
     * Force delete product.
     */
    public function forceDelete(
        User $user,
        Product $product
    ): Response {

        if (
            !$user->can(
                'products.force-delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus permanen produk.'
            );
        }

        return Response::allow();
    }

    /**
     * Activate product.
     */
    public function activate(
        User $user,
        Product $product
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Deactivate product.
     */
    public function deactivate(
        User $user,
        Product $product
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Upload product image.
     */
    public function uploadImage(
        User $user,
        Product $product
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Delete product image.
     */
    public function deleteImage(
        User $user,
        Product $product
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Increase stock.
     */
    public function increaseStock(
        User $user,
        Product $product
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Decrease stock.
     */
    public function decreaseStock(
        User $user,
        Product $product
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Manage inventory.
     */
    public function manageInventory(
        User $user,
        Product $product
    ): bool {

        return $user->can(
            'products.update'
        );
    }
}