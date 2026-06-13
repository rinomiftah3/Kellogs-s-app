<?php

namespace App\Providers;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Events\UserCreated;
use App\Events\UserUpdated;

use App\Listeners\ClearCacheListener;
use App\Listeners\SendNotificationListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Event listener mappings.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

        /*
        |--------------------------------------------------------------------------
        | User Events
        |--------------------------------------------------------------------------
        */

        UserCreated::class => [

            SendNotificationListener::class,

            ClearCacheListener::class,
        ],

        UserUpdated::class => [

            SendNotificationListener::class,

            ClearCacheListener::class,
        ],

        /*
        |--------------------------------------------------------------------------
        | Product Events
        |--------------------------------------------------------------------------
        */

        ProductCreated::class => [

            SendNotificationListener::class,

            ClearCacheListener::class,
        ],

        ProductUpdated::class => [

            SendNotificationListener::class,

            ClearCacheListener::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap events.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be auto-discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}