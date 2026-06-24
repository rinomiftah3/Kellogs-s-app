<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;

class CartPolicy
{
    /*
    |--------------------------------------------------------------------------
    | Global Authorization
    |--------------------------------------------------------------------------
    */

    public function before(
        User $user,
        string $ability
    ): ?bool {

        if (
            method_exists($user, 'hasRole')
            &&
            $user->hasRole('Super Admin')
        ) {

            return true;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | View Any
    |--------------------------------------------------------------------------
    */

    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'carts.view'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View Cart
    |--------------------------------------------------------------------------
    */

    public function view(
        User $user,
        Cart $cart
    ): bool {

        /*
        |----------------------------------------------------------------------
        | Admin / Staff
        |----------------------------------------------------------------------
        */

        if (
            $user->can('carts.view')
        ) {

            return true;
        }

        /*
        |----------------------------------------------------------------------
        | Customer Own Cart
        |----------------------------------------------------------------------
        */

        return $this->ownsCart(
            $user,
            $cart
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Cart
    |--------------------------------------------------------------------------
    */

    public function create(
        User $user
    ): bool {

        return $this->hasCustomerProfile(
            $user
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Cart
    |--------------------------------------------------------------------------
    */

    public function update(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Cart
    |--------------------------------------------------------------------------
    */

    public function delete(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(
        User $user,
        Cart $cart
    ): bool {

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(
        User $user,
        Cart $cart
    ): bool {

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Operations
    |--------------------------------------------------------------------------
    */

    public function myCart(
        User $user
    ): bool {

        return $this->hasCustomerProfile(
            $user
        );
    }

    public function addToCart(
        User $user
    ): bool {

        return $this->hasCustomerProfile(
            $user
        );
    }

    public function clearCart(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    public function touchActivity(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    public function updateItem(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    public function removeItem(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    public function bulkRemoveItems(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    public function selectItem(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    public function unselectItem(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    public function selectAll(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    public function unselectAll(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsActiveCart(
            $user,
            $cart
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function hasCustomerProfile(
        User $user
    ): bool {

        return ! is_null(
            $user->customerProfile
        );
    }

    protected function ownsCart(
        User $user,
        Cart $cart
    ): bool {

        return $this->hasCustomerProfile($user)

            &&

            $cart->customer_profile_id
                ===
            $user->customerProfile->id;
    }

    protected function ownsActiveCart(
        User $user,
        Cart $cart
    ): bool {

        return $this->ownsCart(
            $user,
            $cart
        )

        &&

        $cart->isActive()

        &&

        ! $cart->isExpired();
    }
}