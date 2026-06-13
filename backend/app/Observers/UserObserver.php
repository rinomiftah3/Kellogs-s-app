<?php

namespace App\Observers;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Models\User;

use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * Dashboard cache key.
     */
    private const DASHBOARD_CACHE_KEY =
        'dashboard.overview';

    /**
     * Handle user created event.
     */
    public function created(
        User $user
    ): void {

        $this->clearCaches();

        UserCreated::dispatch(
            $user
        )->afterCommit();
    }

    /**
     * Handle user updated event.
     */
    public function updated(
        User $user
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Dispatch only when important attributes changed
        |--------------------------------------------------------------------------
        */

        if (
            $user->wasChanged([
                'name',
                'email',
                'is_active',
            ])
        ) {

            UserUpdated::dispatch(
                $user
            )->afterCommit();
        }

        $this->clearCaches();
    }

    /**
     * Handle user deleted event.
     */
    public function deleted(
        User $user
    ): void {

        $this->clearCaches();
    }

    /**
     * Handle user restored event.
     */
    public function restored(
        User $user
    ): void {

        $this->clearCaches();
    }

    /**
     * Handle user force deleted event.
     */
    public function forceDeleted(
        User $user
    ): void {

        $this->clearCaches();
    }

    /**
     * Clear related caches.
     */
    private function clearCaches(): void
    {
        Cache::forget(
            self::DASHBOARD_CACHE_KEY
        );
    }
}