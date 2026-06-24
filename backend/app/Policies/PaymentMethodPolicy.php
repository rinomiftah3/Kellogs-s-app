<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentMethodPolicy
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
    | View Available Gateways
    |--------------------------------------------------------------------------
    */

    public function gateways(
        User $user
    ): bool {

        return $user->can(
            'payments.view'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View Available Methods
    |--------------------------------------------------------------------------
    */

    public function methods(
        User $user
    ): bool {

        return $user->can(
            'payments.view'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Snap Token
    |--------------------------------------------------------------------------
    */

    public function snapToken(
        User $user,
        Payment $payment
    ): bool {

        return $user->can(
            'payments.update'
        )
        && $payment->isPending();
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Redirect URL
    |--------------------------------------------------------------------------
    */

    public function redirectUrl(
        User $user,
        Payment $payment
    ): bool {

        return $user->can(
            'payments.update'
        )
        && $payment->isPending();
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Gateway Callback
    |--------------------------------------------------------------------------
    */

    public function callback(
        ?User $user = null
    ): bool {

        /*
         * Callback berasal dari Midtrans,
         * bukan dari user yang login.
         */

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | View Payment Configuration
    |--------------------------------------------------------------------------
    */

    public function configuration(
        User $user
    ): bool {

        return $user->can(
            'payments.view'
        );
    }
}