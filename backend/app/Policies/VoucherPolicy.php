<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Voucher;

class VoucherPolicy
{
    /**
     * View voucher list (admin).
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'vouchers.view'
        );
    }

    /**
     * View voucher detail.
     */
    public function view(
        User $user,
        Voucher $voucher
    ): bool {

        return $user->can(
            'vouchers.view'
        );
    }

    /**
     * Create voucher.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'vouchers.create'
        );
    }

    /**
     * Update voucher.
     */
    public function update(
        User $user,
        Voucher $voucher
    ): bool {

        return $user->can(
            'vouchers.update'
        );
    }

    /**
     * Delete voucher.
     *
     * Voucher yang sudah pernah digunakan
     * tidak boleh dihapus.
     */
    public function delete(
        User $user,
        Voucher $voucher
    ): bool {

        if (
            ! $user->can(
                'vouchers.delete'
            )
        ) {
            return false;
        }

        return ! $voucher
            ->usages()
            ->exists();
    }

    /**
     * Restore voucher.
     */
    public function restore(
        User $user,
        Voucher $voucher
    ): bool {

        return $user->can(
            'vouchers.delete'
        );
    }

    /**
     * Force delete voucher.
     *
     * Tidak diperbolehkan apabila voucher
     * pernah digunakan.
     */
    public function forceDelete(
        User $user,
        Voucher $voucher
    ): bool {

        if (
            ! $user->can(
                'vouchers.delete'
            )
        ) {
            return false;
        }

        return ! $voucher
            ->usages()
            ->withTrashed()
            ->exists();
    }

    /**
     * Activate voucher.
     */
    public function activate(
        User $user,
        Voucher $voucher
    ): bool {

        if (
            ! $user->can(
                'vouchers.update'
            )
        ) {
            return false;
        }

        return ! $voucher->is_active;
    }

    /**
     * Deactivate voucher.
     */
    public function deactivate(
        User $user,
        Voucher $voucher
    ): bool {

        if (
            ! $user->can(
                'vouchers.update'
            )
        ) {
            return false;
        }

        return $voucher->is_active;
    }

    /**
     * View public vouchers.
     */
    public function viewPublic(
        ?User $user = null
    ): bool {

        return true;
    }

    /**
     * Apply voucher.
     *
     * Validasi voucher dilakukan oleh
     * VoucherService.
     */
    public function apply(
        User $user
    ): bool {

        return $user !== null;
    }
}