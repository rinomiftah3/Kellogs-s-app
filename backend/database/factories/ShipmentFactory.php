<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShippingMethod;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        $shipmentNumber =
            'SHP-' .
            strtoupper(
                fake()->bothify('########')
            );

        $trackingNumber =
            strtoupper(
                fake()->bothify(
                    'TRK##########'
                )
            );

        $shippingCost =
            fake()->numberBetween(
                10000,
                50000
            );

        $insured =
            fake()->boolean(40);

        return [

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            'order_id' =>
                Order::factory(),

            'shipping_method_id' =>
                ShippingMethod::factory(),

            /*
            |--------------------------------------------------------------------------
            | Shipment Information
            |--------------------------------------------------------------------------
            */

            'shipment_number' =>
                $shipmentNumber,

            'tracking_number' =>
                $trackingNumber,

            /*
            |--------------------------------------------------------------------------
            | Courier Snapshot
            |--------------------------------------------------------------------------
            */

            'courier_name' =>
                fake()->randomElement([
                    'JNE',
                    'J&T Express',
                    'SiCepat',
                    'AnterAja',
                    'POS Indonesia',
                ]),

            'courier_code' =>
                fake()->randomElement([
                    'jne',
                    'jnt',
                    'sicepat',
                    'anteraja',
                    'pos',
                ]),

            'service_name' =>
                fake()->randomElement([
                    'Regular',
                    'Express',
                    'Economy',
                    'Same Day',
                ]),

            'service_code' =>
                fake()->randomElement([
                    'REG',
                    'YES',
                    'ECO',
                    'SDS',
                ]),

            'tracking_url' =>
                'https://cekresi.com/?no=' .
                $trackingNumber,

            /*
            |--------------------------------------------------------------------------
            | Shipping Cost
            |--------------------------------------------------------------------------
            */

            'shipping_cost' =>
                $shippingCost,

            'insurance_cost' =>
                $insured
                    ? fake()->numberBetween(
                        1000,
                        10000
                    )
                    : 0,

            'is_insured' =>
                $insured,

            /*
            |--------------------------------------------------------------------------
            | Package Information
            |--------------------------------------------------------------------------
            */

            'weight' =>
                fake()->randomFloat(
                    2,
                    0.25,
                    25
                ),

            'item_count' =>
                fake()->numberBetween(
                    1,
                    10
                ),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' =>
                Shipment::STATUS_PENDING,

            /*
            |--------------------------------------------------------------------------
            | Recipient Snapshot
            |--------------------------------------------------------------------------
            */

            'recipient_name' =>
                fake()->name(),

            'recipient_phone' =>
                fake()->phoneNumber(),

            'recipient_address' =>
                fake()->address(),

            'recipient_city' =>
                fake()->city(),

            'recipient_province' =>
                fake()->state(),

            'recipient_postal_code' =>
                fake()->postcode(),

            /*
            |--------------------------------------------------------------------------
            | Timeline
            |--------------------------------------------------------------------------
            */

            'pickup_at' => null,

            'shipped_at' => null,

            'estimated_delivery_at' =>
                now()->addDays(
                    fake()->numberBetween(
                        2,
                        7
                    )
                ),

            'delivered_at' => null,

            /*
            |--------------------------------------------------------------------------
            | Analytics
            |--------------------------------------------------------------------------
            */

            'delivery_attempts' => 0,

            'delivery_duration_hours'
                => null,

            'last_tracking_sync_at'
                => now(),

            /*
            |--------------------------------------------------------------------------
            | Delivery Information
            |--------------------------------------------------------------------------
            */

            'received_by' => null,

            'signed_proof' => null,

            /*
            |--------------------------------------------------------------------------
            | Failure / Return
            |--------------------------------------------------------------------------
            */

            'failed_reason' => null,

            'return_reason' => null,

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'notes' => null,

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'metadata' => [

                'source' => 'factory',

                'generated_by'
                    => 'ShipmentFactory',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Status States
    |--------------------------------------------------------------------------
    */

    public function pending(): static
    {
        return $this->state(fn () => [

            'status'
                => Shipment::STATUS_PENDING,
        ]);
    }

    public function readyToShip(): static
    {
        return $this->state(fn () => [

            'status'
                => Shipment::STATUS_READY_TO_SHIP,
        ]);
    }

    public function pickedUp(): static
    {
        return $this->state(fn () => [

            'status'
                => Shipment::STATUS_PICKED_UP,

            'pickup_at'
                => now()->subHours(12),

            'shipped_at'
                => now()->subHours(12),
        ]);
    }

    public function inTransit(): static
    {
        return $this->state(fn () => [

            'status'
                => Shipment::STATUS_IN_TRANSIT,

            'pickup_at'
                => now()->subDays(2),

            'shipped_at'
                => now()->subDays(2),
        ]);
    }

    public function outForDelivery(): static
    {
        return $this->state(fn () => [

            'status'
                => Shipment::STATUS_OUT_FOR_DELIVERY,

            'pickup_at'
                => now()->subDays(3),

            'shipped_at'
                => now()->subDays(3),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn () => [

            'status'
                => Shipment::STATUS_DELIVERED,

            'pickup_at'
                => now()->subDays(4),

            'shipped_at'
                => now()->subDays(4),

            'delivered_at'
                => now(),

            'received_by'
                => fake()->name(),

            'delivery_duration_hours'
                => fake()->numberBetween(
                    24,
                    120
                ),

            'signed_proof'
                => 'proofs/signature.jpg',
        ]);
    }

    public function failedDelivery(): static
    {
        return $this->state(fn () => [

            'status'
                => Shipment::STATUS_FAILED_DELIVERY,

            'delivery_attempts'
                => fake()->numberBetween(
                    1,
                    3
                ),

            'failed_reason'
                => fake()->sentence(),
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn () => [

            'status'
                => Shipment::STATUS_RETURNED,

            'return_reason'
                => fake()->sentence(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [

            'status'
                => Shipment::STATUS_CANCELLED,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature States
    |--------------------------------------------------------------------------
    */

    public function insured(): static
    {
        return $this->state(fn () => [

            'is_insured' => true,

            'insurance_cost'
                => fake()->numberBetween(
                    1000,
                    10000
                ),
        ]);
    }

    public function withoutTracking(): static
    {
        return $this->state(fn () => [

            'tracking_number' => null,

            'tracking_url' => null,
        ]);
    }
}