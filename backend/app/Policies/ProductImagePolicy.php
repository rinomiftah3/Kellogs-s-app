<?php

namespace App\Policies;

use App\Models\ProductImage;
use App\Models\User;

class ProductImagePolicy
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
                User::ROLE_SUPER_ADMIN
            )
        ) {

            return true;
        }

        return null;
    }

    /**
     * View product image listing.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /**
     * View product image detail.
     */
    public function view(
        User $user,
        ProductImage $productImage
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /**
     * Create product image.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'products.create'
        );
    }

    /**
     * Update product image.
     */
    public function update(
        User $user,
        ProductImage $productImage
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /**
     * Delete product image.
     */
    public function delete(
        User $user,
        ProductImage $productImage
    ): bool {

        return $user->can(
            'products.delete'
        );
    }

    /**
     * Set primary product image.
     */
    public function setPrimary(
        User $user,
        ProductImage $productImage
    ): bool {

        return $user->can(
            'products.update'
        );
    }
}