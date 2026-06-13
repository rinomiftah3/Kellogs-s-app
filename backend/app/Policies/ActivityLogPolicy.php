<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\Response;

class ActivityLogPolicy
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
                'Super Admin'
            )
        ) {

            return true;
        }

        return null;
    }

    /**
     * View activity log list.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'activity-logs.view'
        );
    }

    /**
     * View activity log detail.
     */
    public function view(
        User $user
    ): bool {

        return $user->can(
            'activity-logs.view'
        );
    }

    /**
     * Export activity logs.
     */
    public function export(
        User $user
    ): bool {

        return $user->can(
            'activity-logs.export'
        );
    }

    /**
     * Delete activity log.
     */
    public function delete(
        User $user
    ): Response {

        if (
            !$user->can(
                'activity-logs.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus activity log.'
            );
        }

        return Response::allow();
    }

    /**
     * Clean old activity logs.
     */
    public function clean(
        User $user
    ): Response {

        if (
            !$user->can(
                'activity-logs.clean'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin membersihkan activity log.'
            );
        }

        return Response::allow();
    }

    /**
     * Truncate activity logs.
     */
    public function truncate(
        User $user
    ): Response {

        if (
            !$user->can(
                'activity-logs.truncate'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus seluruh activity log.'
            );
        }

        return Response::allow();
    }

    /**
     * View activity statistics.
     */
    public function statistics(
        User $user
    ): bool {

        return $user->can(
            'activity-logs.view'
        );
    }

    /**
     * View dashboard activity widget.
     */
    public function dashboard(
        User $user
    ): bool {

        return $user->can(
            'activity-logs.view'
        );
    }
}