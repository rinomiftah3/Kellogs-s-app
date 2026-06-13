<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;

class SendNotificationListener implements ShouldQueue
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
    public string $queue = 'notifications';

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
        10,
        30,
        60,
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

        /*
        |----------------------------------------------------------------------
        | Notification Pipeline Placeholder
        |----------------------------------------------------------------------
        |
        | Future integrations:
        | - Mail
        | - WhatsApp
        | - Telegram
        | - Discord
        | - Slack
        | - Push Notification
        |
        */

        Log::channel('stack')->info(
            'Notification event triggered.',
            [

                'event' => $this->resolveEventName(
                    $event
                ),

                'payload' => $this->resolvePayload(
                    $event
                ),
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
            'Notification listener failed.',
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

    /**
     * Resolve event payload.
     */
    private function resolvePayload(
        object $event
    ): array {

        if (
            method_exists(
                $event,
                'payload'
            )
        ) {
            return $event->payload();
        }

        return [];
    }
}