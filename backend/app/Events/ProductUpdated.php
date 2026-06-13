<?php

namespace App\Events;

use App\Models\Product;

use Carbon\CarbonImmutable;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated
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
     * Changed attributes.
     */
    public readonly array $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Product $product
    ) {

        $this->eventVersion = '1.0';

        $this->occurredAt = CarbonImmutable::now();

        /*
        |--------------------------------------------------------------------------
        | Changed Attributes Snapshot
        |--------------------------------------------------------------------------
        */

        $this->changes = array_keys(
            $this->product->getChanges()
        );
    }

    /**
     * Event name.
     */
    public function eventName(): string
    {
        return 'product.updated';
    }

    /**
     * Event payload.
     */
    public function payload(): array
    {
        return [

            'product_id' =>
                $this->product->id,

            'name' =>
                $this->product->name,

            'slug' =>
                $this->product->slug,

            'category_id' =>
                $this->product->category_id,

            'status' =>
                $this->product->status,

            'is_featured' =>
                (bool) $this->product->is_featured,

            'is_active' =>
                (bool) $this->product->is_active,

            'published_at' =>
                $this->product->published_at
                    ?->toIso8601String(),

            'changes' =>
                $this->changes,

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