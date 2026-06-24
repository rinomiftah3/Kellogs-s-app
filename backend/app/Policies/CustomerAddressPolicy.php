<?php

namespace App\Policies;

use App\Models\CustomerAddress;
use App\Models\CustomerProfile;
use App\Models\User;

class CustomerAddressPolicy
{
    /**
     * Before.
     */
    public function before(
        User $user,
        string $ability
    ): bool|null {

        if (
            $user->hasRole('Super Admin')
        ) {
            return true;
        }

        return null;
    }

    /**
     * View any addresses.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'customer_addresses.view'
        );
    }

    /**
     * View address detail.
     */
    public function view(
        User $user,
        CustomerAddress $address
    ): bool {

        if (
            $user->can(
                'customer_addresses.view'
            )
        ) {
            return true;
        }

        return $this->ownsAddress(
            $user,
            $address
        );
    }

    /**
     * Create address.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'customer_addresses.update'
        )
        ||
        $this->hasCustomerProfile(
            $user
        );
    }

    /**
     * Update address.
     */
    public function update(
        User $user,
        CustomerAddress $address
    ): bool {

        if (
            $user->can(
                'customer_addresses.update'
            )
        ) {
            return true;
        }

        return $this->ownsAddress(
            $user,
            $address
        );
    }

    /**
     * Delete address.
     */
    public function delete(
        User $user,
        CustomerAddress $address
    ): bool {

        if (
            $user->can(
                'customer_addresses.update'
            )
        ) {
            return true;
        }

        return $this->ownsAddress(
            $user,
            $address
        );
    }

    /**
     * Restore address.
     */
    public function restore(
        User $user,
        CustomerAddress $address
    ): bool {

        return $user->can(
            'customer_addresses.update'
        );
    }

    /**
     * Permanently delete address.
     */
    public function forceDelete(
        User $user,
        CustomerAddress $address
    ): bool {

        return false;
    }

    /**
     * Set default address.
     */
    public function setDefault(
        User $user,
        CustomerAddress $address
    ): bool {

        if (
            ! $address->isActive()
        ) {
            return false;
        }

        if (
            $user->can(
                'customer_addresses.update'
            )
        ) {
            return true;
        }

        return $this->ownsAddress(
            $user,
            $address
        );
    }

    /**
     * Activate address.
     */
    public function activate(
        User $user,
        CustomerAddress $address
    ): bool {

        if (
            $user->can(
                'customer_addresses.update'
            )
        ) {
            return true;
        }

        return $this->ownsAddress(
            $user,
            $address
        );
    }

    /**
     * Deactivate address.
     */
    public function deactivate(
        User $user,
        CustomerAddress $address
    ): bool {

        if (
            $user->can(
                'customer_addresses.update'
            )
        ) {
            return true;
        }

        return $this->ownsAddress(
            $user,
            $address
        );
    }

    /**
     * View addresses by customer.
     */
    public function viewByCustomer(
        User $user,
        CustomerProfile $customer
    ): bool {

        if (
            $user->can(
                'customer_addresses.view'
            )
        ) {
            return true;
        }

        return $customer->user_id
            === $user->id;
    }

    /**
     * View default address by customer.
     */
    public function viewDefaultAddress(
        User $user,
        CustomerProfile $customer
    ): bool {

        if (
            $user->can(
                'customer_addresses.view'
            )
        ) {
            return true;
        }

        return $customer->user_id
            === $user->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function ownsAddress(
        User $user,
        CustomerAddress $address
    ): bool {

        return $address->customer?->user_id
            === $user->id;
    }

    protected function hasCustomerProfile(
        User $user
    ): bool {

        return CustomerProfile::query()

            ->where(
                'user_id',
                $user->id
            )

            ->exists();
    }
}