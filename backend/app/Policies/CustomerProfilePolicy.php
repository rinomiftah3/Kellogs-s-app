<?php

namespace App\Policies;

use App\Models\CustomerProfile;
use App\Models\User;

class CustomerProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'customers.view'
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.view'
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'customers.create'
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        if (
            ! $user->can(
                'customers.delete'
            )
        ) {
            return false;
        }

        /*
        |------------------------------------------------------------------
        | Business Rule
        |------------------------------------------------------------------
        |
        | Customer yang memiliki histori order
        | tidak boleh dihapus.
        |
        */

        return ! $customerProfile
            ->hasOrders();
    }

    /**
     * Determine whether the user can activate the customer.
     */
    public function activate(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        )
        && ! $customerProfile->isActive();
    }

    /**
     * Determine whether the user can deactivate the customer.
     */
    public function deactivate(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        )
        && $customerProfile->isActive();
    }

    /**
     * Determine whether the user can change membership.
     */
    public function changeMembership(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        );
    }

    /**
     * Determine whether the user can increase points.
     */
    public function increasePoints(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        );
    }

    /**
     * Determine whether the user can increase order statistics.
     */
    public function increaseOrderCount(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        );
    }

    /**
     * Determine whether the user can update
     * last order timestamp.
     */
    public function updateLastOrder(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        );
    }

    /**
     * Determine whether the user can subscribe email.
     */
    public function subscribeEmail(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        )
        && ! $customerProfile->isSubscribedEmail();
    }

    /**
     * Determine whether the user can unsubscribe email.
     */
    public function unsubscribeEmail(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        )
        && $customerProfile->isSubscribedEmail();
    }

    /**
     * Determine whether the user can subscribe SMS.
     */
    public function subscribeSms(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        )
        && ! $customerProfile->isSubscribedSms();
    }

    /**
     * Determine whether the user can unsubscribe SMS.
     */
    public function unsubscribeSms(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        )
        && $customerProfile->isSubscribedSms();
    }

    /**
     * Determine whether the user can subscribe push notification.
     */
    public function subscribePush(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        )
        && ! $customerProfile->isSubscribedPush();
    }

    /**
     * Determine whether the user can unsubscribe push notification.
     */
    public function unsubscribePush(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        )
        && $customerProfile->isSubscribedPush();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return $user->can(
            'customers.update'
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(
        User $user,
        CustomerProfile $customerProfile
    ): bool {

        return false;
    }
}