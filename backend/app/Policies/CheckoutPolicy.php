<?php

namespace App\Policies;

use App\Models\CheckoutSession;
use App\Models\User;

class CheckoutPolicy
{
    /*
    |--------------------------------------------------------------------------
    | View Any Checkout Sessions
    |--------------------------------------------------------------------------
    */

    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'checkouts.view'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View Checkout Session
    |--------------------------------------------------------------------------
    */

    public function view(
        User $user,
        CheckoutSession $checkoutSession
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Admin / Super Admin
        |--------------------------------------------------------------------------
        */

        if (
            $user->can(
                'checkouts.view'
            )
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Customer Ownership
        |--------------------------------------------------------------------------
        */

        return $user->customerProfile?->id
            === $checkoutSession->customer_profile_id;
    }

    /*
    |--------------------------------------------------------------------------
    | Start Checkout
    |--------------------------------------------------------------------------
    */

    public function create(
        User $user
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Customer must have profile
        |--------------------------------------------------------------------------
        */

        return filled(
            $user->customerProfile?->id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Apply Voucher
    |--------------------------------------------------------------------------
    */

    public function applyVoucher(
        User $user,
        CheckoutSession $checkoutSession
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Ownership Validation
        |--------------------------------------------------------------------------
        */

        if (

            $user->customerProfile?->id
            !==
            $checkoutSession->customer_profile_id

        ) {

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Cannot modify completed checkout
        |--------------------------------------------------------------------------
        */

        if (
            $checkoutSession->isCheckedOut()
        ) {

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Cannot modify expired checkout
        |--------------------------------------------------------------------------
        */

        if (
            $checkoutSession->isExpired()
        ) {

            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Checkout
    |--------------------------------------------------------------------------
    */

    public function validate(
        User $user,
        CheckoutSession $checkoutSession
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Ownership Validation
        |--------------------------------------------------------------------------
        */

        if (

            $user->customerProfile?->id
            !==
            $checkoutSession->customer_profile_id

        ) {

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Already checked out
        |--------------------------------------------------------------------------
        */

        if (
            $checkoutSession->isCheckedOut()
        ) {

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Expired checkout
        |--------------------------------------------------------------------------
        */

        if (
            $checkoutSession->isExpired()
        ) {

            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Place Order
    |--------------------------------------------------------------------------
    */

    public function placeOrder(
        User $user,
        CheckoutSession $checkoutSession
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Ownership Validation
        |--------------------------------------------------------------------------
        */

        if (

            $user->customerProfile?->id
            !==
            $checkoutSession->customer_profile_id

        ) {

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Business Rule Validation
        |--------------------------------------------------------------------------
        */

        return $checkoutSession
            ->canCheckout();
    }
}