<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearCacheListener implements ShouldQueue
{
    use InteractsWithQueue;

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */

    /**
     * Execute listener after database transaction committed.
     */
    public bool $afterCommit = true;

    /**
     * Queue name.
     */
    public string $queue = 'cache';

    /**
     * Maximum attempts.
     */
    public int $tries = 3;

    /**
     * Timeout (seconds).
     */
    public int $timeout = 30;

    /**
     * Backoff strategy.
     */
    public array $backoff = [
        5,
        15,
        30,
    ];

    /*
    |--------------------------------------------------------------------------
    | Cache Keys
    |--------------------------------------------------------------------------
    */

    private const CACHE_KEYS = [

        'dashboard.overview',

        'category.statistics',

        'product.statistics',

        'activity.statistics',

        'activity.dashboard',

        'activity.latest.10',
    ];

    /**
     * Create listener instance.
     */
    public function __construct()
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Event
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the event.
     */
    public function handle(
        object $event
    ): void {

        foreach (
            self::CACHE_KEYS
            as $cacheKey
        ) {
            Cache::forget(
                $cacheKey
            );
        }

        Log::info(
            'Application caches cleared.',
            [

                'event' => $this->resolveEventName(
                    $event
                ),

                'cache_keys' => self::CACHE_KEYS,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Failed Listener
    |--------------------------------------------------------------------------
    */

    /**
     * Handle a listener failure.
     */
    public function failed(
        object $event,
        \Throwable $exception
    ): void {

        Log::error(
            'Cache clear listener failed.',
            [

                'event' => $this->resolveEventName(
                    $event
                ),

                'exception' => get_class(
                    $exception
                ),

                'message' => $exception->getMessage(),

                'file' => $exception->getFile(),

                'line' => $exception->getLine(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve event name.
     */
    private function resolveEventName(
        object $event
    ): string {

        if (
            method_exists(
                $event,
                'eventName'
            )
        ) {
            return $event->eventName();
        }

        return class_basename(
            $event
        );
    }
}