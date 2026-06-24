<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /*
    |--------------------------------------------------------------------------
    | Before
    |--------------------------------------------------------------------------
    */

    public function before(
        User $user,
        string $ability
    ): ?bool {

        if ($user->isSuperAdmin()) {
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
            'payments.view'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    public function view(
        User $user,
        Payment $payment
    ): bool {

        if (
            $user->can('payments.view')
        ) {
            return true;
        }

        if (
            $user->isCustomer()
            && $user->customerProfile
        ) {

            return optional(
                $payment->order
            )->customer_profile_id
                ===
                $user->customerProfile->id;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        User $user
    ): bool {

        return $user->can(
            'payments.update'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Status
    |--------------------------------------------------------------------------
    */

    public function update(
        User $user,
        Payment $payment
    ): bool {

        if (
            ! $user->can('payments.update')
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------
        | Paid payments cannot be modified manually.
        |--------------------------------------------------------------
        */

        if (
            $payment->isPaid()
            || $payment->isRefunded()
        ) {

            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Refund
    |--------------------------------------------------------------------------
    */

    public function refund(
        User $user,
        Payment $payment
    ): bool {

        if (
            ! $user->can('payments.update')
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------
        | Only successful payments can be refunded.
        |--------------------------------------------------------------
        */

        return $payment->isPaid();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        User $user,
        Payment $payment
    ): bool {

        if (
            ! $user->can('payments.update')
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------
        | Prevent deletion of completed financial records.
        |--------------------------------------------------------------
        */

        return ! (
            $payment->isPaid()
            || $payment->isRefunded()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(
        User $user,
        Payment $payment
    ): bool {

        return $user->can(
            'payments.update'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(
        User $user,
        Payment $payment
    ): bool {

        return false;
    }
}