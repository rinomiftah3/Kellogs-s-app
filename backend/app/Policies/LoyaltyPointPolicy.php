<?php

namespace App\Policies;

use App\Models\LoyaltyPoint;
use App\Models\User;

class LoyaltyPointPolicy
{
    /**
     * Super Admin bypass.
     */
    public function before(
        User $user,
        string $ability
    ): ?bool {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    /**
     * View loyalty list.
     */
    public function viewAny(
        User $user
    ): bool {
        return $user->can(
            'loyalty_points.view'
        );
    }

    /**
     * View loyalty detail.
     */
    public function view(
        User $user,
        LoyaltyPoint $loyaltyPoint
    ): bool {

        if (
            $user->can('loyalty_points.view')
        ) {
            return true;
        }

        return
            $user->customerProfile
            &&
            $user->customerProfile->id ===
            $loyaltyPoint->customer_profile_id;
    }

    /**
     * Create loyalty account.
     */
    public function create(
        User $user
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }

    /**
     * Update loyalty account.
     */
    public function update(
        User $user,
        LoyaltyPoint $loyaltyPoint
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }

    /**
     * Delete loyalty account.
     */
    public function delete(
        User $user,
        LoyaltyPoint $loyaltyPoint
    ): bool {

        return
            $user->can('loyalty_points.update')
            &&
            ! $loyaltyPoint
                ->transactions()
                ->exists();
    }

    /**
     * Restore loyalty account.
     */
    public function restore(
        User $user,
        LoyaltyPoint $loyaltyPoint
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }

    /**
     * Force delete loyalty account.
     */
    public function forceDelete(
        User $user,
        LoyaltyPoint $loyaltyPoint
    ): bool {
        return false;
    }

    /**
     * Activate loyalty account.
     */
    public function activate(
        User $user,
        LoyaltyPoint $loyaltyPoint
    ): bool {

        return
            $user->can('loyalty_points.update')
            &&
            ! $loyaltyPoint->is_active;
    }

    /**
     * Deactivate loyalty account.
     */
    public function deactivate(
        User $user,
        LoyaltyPoint $loyaltyPoint
    ): bool {

        return
            $user->can('loyalty_points.update')
            &&
            $loyaltyPoint->is_active;
    }

    /**
     * Publish loyalty account.
     */
    public function publish(
        User $user,
        LoyaltyPoint $loyaltyPoint
    ): bool {

        return
            $user->can('loyalty_points.update')
            &&
            ! $loyaltyPoint->isPublished();
    }

    /**
     * View own balance.
     */
    public function myBalance(
        User $user
    ): bool {

        return
            $user->customerProfile !== null;
    }

    /**
     * Earn points.
     */
    public function earn(
        User $user
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }

    /**
     * Redeem points.
     */
    public function redeem(
        User $user,
        LoyaltyPoint $loyaltyPoint
    ): bool {

        return
            $user->customerProfile
            &&
            $user->customerProfile->id
                ===
            $loyaltyPoint->customer_profile_id
            &&
            $loyaltyPoint->is_active;
    }

    /**
     * Refund points.
     */
    public function refund(
        User $user
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }

    /**
     * Bonus points.
     */
    public function bonus(
        User $user
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }

    /**
     * Manual adjustment.
     */
    public function adjust(
        User $user
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }

    /**
     * Expire points.
     */
    public function expire(
        User $user
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }

    /**
     * View transactions.
     */
    public function transactions(
        User $user
    ): bool {

        return
            $user->can(
                'point_transactions.view'
            );
    }

    /**
     * Approve transaction.
     */
    public function approveTransaction(
        User $user
    ): bool {

        return
            $user->can('loyalty_points.update')
            &&
            $user->can('point_transactions.view');
    }

    /**
     * Cancel transaction.
     */
    public function cancelTransaction(
        User $user
    ): bool {

        return
            $user->can('loyalty_points.update')
            &&
            $user->can('point_transactions.view');
    }

    /**
     * Manual tier upgrade.
     */
    public function upgradeTier(
        User $user
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }

    /**
     * Downgrade expired tiers.
     */
    public function downgradeExpiredTiers(
        User $user
    ): bool {
        return $user->can(
            'loyalty_points.update'
        );
    }
}