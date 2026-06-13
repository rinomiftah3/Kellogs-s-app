<?php

namespace App\Events;

use App\Models\User;

use Carbon\CarbonImmutable;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Event version.
     */
    public readonly string $eventVersion;

    /**
     * Event timestamp.
     */
    public readonly CarbonImmutable $occurredAt;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly User $user
    ) {

        $this->eventVersion = '1.0';

        $this->occurredAt = CarbonImmutable::now();
    }

    /**
     * Event name.
     */
    public function eventName(): string
    {
        return 'user.updated';
    }

    /**
     * Event payload.
     */
    public function payload(): array
    {
        return [

            'user_id' =>
                $this->user->id,

            'name' =>
                $this->user->name,

            'email' =>
                $this->user->email,

            'is_active' =>
                $this->user->is_active,

            'event' =>
                $this->eventName(),

            'version' =>
                $this->eventVersion,

            'occurred_at' =>
                $this->occurredAt
                    ->toIso8601String(),
        ];
    }
}