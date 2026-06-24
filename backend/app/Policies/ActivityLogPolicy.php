<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

use Illuminate\Auth\Access\Response;

class ActivityLogPolicy
{
    /*
    |--------------------------------------------------------------------------
    | Before
    |--------------------------------------------------------------------------
    */

    public function before(
        User $user,
        string $ability
    ): bool|null {

        if (
            method_exists($user, 'isSuperAdmin')
            && $user->isSuperAdmin()
        ) {
            return true;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | View Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'activity_logs.view'
        );
    }

    public function view(
        User $user,
        Activity $activity
    ): bool {

        return $user->can(
            'activity_logs.view'
        );
    }

    public function latest(
        User $user
    ): bool {

        return $user->can(
            'activity_logs.view'
        );
    }

    public function statistics(
        User $user
    ): bool {

        return $user->can(
            'activity_logs.view'
        );
    }

    public function dashboardSummary(
        User $user
    ): bool {

        return $user->can(
            'activity_logs.view'
        );
    }

    public function availableEvents(
        User $user
    ): bool {

        return $user->can(
            'activity_logs.view'
        );
    }

    public function availableLogNames(
        User $user
    ): bool {

        return $user->can(
            'activity_logs.view'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Maintenance Permissions
    |--------------------------------------------------------------------------
    */

    public function clean(
        User $user
    ): Response {

        if (
            ! $user->can(
                'activity_logs.clean'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin membersihkan activity log.'
            );
        }

        return Response::allow();
    }

    public function truncate(
        User $user
    ): Response {

        if (
            ! $user->can(
                'activity_logs.truncate'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus seluruh activity log.'
            );
        }

        return Response::allow();
    }
}