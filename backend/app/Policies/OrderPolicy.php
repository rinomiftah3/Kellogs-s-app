<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /*
    |--------------------------------------------------------------------------
    | View Any
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('orders.view');
    }

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can view the order.
     */
    public function view(
        User $user,
        Order $order
    ): bool {

        /*
        |----------------------------------------------------------------------
        | Internal Staff/Admin
        |----------------------------------------------------------------------
        */

        if ($user->can('orders.view')) {
            return true;
        }

        /*
        |----------------------------------------------------------------------
        | Customer Ownership
        |----------------------------------------------------------------------
        */

        return $order->customer_profile_id
            === $user->customerProfile?->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can create orders.
     */
    public function create(
                User $user
            ): bool
            {
                return

                    $user->hasRole(
                        'customer'
                    )

                    ||

                    $user->can(
                        'orders.create'
                    );
            }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can update the order.
     */
    public function update(
        User $user,
        Order $order
    ): bool {

        if (! $user->can('orders.update')) {
            return false;
        }

        /*
        |----------------------------------------------------------------------
        | Completed orders cannot be updated.
        |----------------------------------------------------------------------
        */

        return ! $order->isCompleted();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(
        User $user,
        Order $order
    ): bool {

        if (! $user->can('orders.delete')) {
            return false;
        }

        /*
        |----------------------------------------------------------------------
        | Paid orders cannot be deleted.
        |----------------------------------------------------------------------
        */

        if ($order->isPaid()) {
            return false;
        }

        /*
        |----------------------------------------------------------------------
        | Completed orders cannot be deleted.
        |----------------------------------------------------------------------
        */

        if ($order->isCompleted()) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can restore the order.
     */
    public function restore(
        User $user,
        Order $order
    ): bool {

        return $user->can('orders.delete');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can permanently delete the order.
     */
    public function forceDelete(
        User $user,
        Order $order
    ): bool {

        return $user->can('orders.delete');
    }

    /*
    |--------------------------------------------------------------------------
    | Update Status
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can update order status.
     */
    public function updateStatus(
        User $user,
        Order $order
    ): bool {

        if (! $user->can('orders.update')) {
            return false;
        }

        /*
        |----------------------------------------------------------------------
        | Final statuses cannot be changed.
        |----------------------------------------------------------------------
        */

        return ! $order->isCompleted()
            && ! $order->isCancelled();
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(
        User $user,
        Order $order
    ): bool {

        /*
        |----------------------------------------------------------------------
        | Internal User
        |----------------------------------------------------------------------
        */

        if ($user->can('orders.update')) {

            return ! $order->isPaid()
                && ! $order->isCompleted()
                && ! $order->isCancelled();
        }

        /*
        |----------------------------------------------------------------------
        | Customer Ownership
        |----------------------------------------------------------------------
        */

        if (
            $order->customer_profile_id
            !== $user->customerProfile?->id
        ) {
            return false;
        }

        /*
        |----------------------------------------------------------------------
        | Customer may only cancel unpaid orders.
        |----------------------------------------------------------------------
        */

        return ! $order->isPaid()
            && $order->isPending();
    }

    /*
    |--------------------------------------------------------------------------
    | Mark As Processing
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can mark the order as processing.
     */
    public function markProcessing(
        User $user,
        Order $order
    ): bool {

        return $user->can('orders.update')
            && $order->status === Order::STATUS_CONFIRMED;
    }

    /*
    |--------------------------------------------------------------------------
    | Mark As Shipped
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can mark the order as shipped.
     */
    public function markShipped(
        User $user,
        Order $order
    ): bool {

        return $user->can('orders.update')
            && $order->status === Order::STATUS_PROCESSING;
    }

    /*
    |--------------------------------------------------------------------------
    | Mark As Completed
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can mark the order as completed.
     */
    public function markCompleted(
        User $user,
        Order $order
    ): bool {

        /*
        |----------------------------------------------------------------------
        | Internal User
        |----------------------------------------------------------------------
        */

        if ($user->can('orders.update')) {

            return $order->status
                === Order::STATUS_SHIPPED;
        }

        /*
        |----------------------------------------------------------------------
        | Customer Confirmation
        |----------------------------------------------------------------------
        */

        return $order->customer_profile_id
            === $user->customerProfile?->id
            && $order->status
                === Order::STATUS_SHIPPED;
    }
}