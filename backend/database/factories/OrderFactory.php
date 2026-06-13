<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\CustomerProfile;
use App\Models\CustomerAddress;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(
            2,
            50000,
            3000000
        );

        $shippingCost = fake()->randomFloat(
            2,
            10000,
            75000
        );

        $discountAmount = fake()->randomFloat(
            2,
            0,
            100000
        );

        $taxAmount = fake()->randomFloat(
            2,
            0,
            50000
        );

        $grandTotal =
            $subtotal
            +
            $shippingCost
            +
            $taxAmount
            -
            $discountAmount;

        return [

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            'customer_profile_id'
                => CustomerProfile::factory(),

            'shipping_address_id'
                => CustomerAddress::factory(),

            /*
            |--------------------------------------------------------------------------
            | Order Information
            |--------------------------------------------------------------------------
            */

            'order_number'
                => 'ORD-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    fake()->unique()->bothify(
                        '#####'
                    )
                ),

            'status'
                => Order::STATUS_PENDING,

            'payment_status'
                => Order::PAYMENT_PENDING,

            'fulfillment_status'
                => Order::FULFILLMENT_PENDING,

            /*
            |--------------------------------------------------------------------------
            | Customer Snapshot
            |--------------------------------------------------------------------------
            */

            'customer_name'
                => fake()->name(),

            'customer_email'
                => fake()->safeEmail(),

            'customer_phone'
                => fake()->phoneNumber(),

            /*
            |--------------------------------------------------------------------------
            | Recipient Snapshot
            |--------------------------------------------------------------------------
            */

            'recipient_name'
                => fake()->name(),

            'recipient_phone'
                => fake()->phoneNumber(),

            'shipping_address'
                => fake()->address(),

            'province'
                => fake()->state(),

            'city'
                => fake()->city(),

            'district'
                => fake()->citySuffix(),

            'postal_code'
                => fake()->postcode(),

            /*
            |--------------------------------------------------------------------------
            | Pricing Summary
            |--------------------------------------------------------------------------
            */

            'subtotal'
                => $subtotal,

            'shipping_cost'
                => $shippingCost,

            'discount_amount'
                => $discountAmount,

            'tax_amount'
                => $taxAmount,

            'grand_total'
                => max(
                    0,
                    $grandTotal
                ),

            /*
            |--------------------------------------------------------------------------
            | Voucher Snapshot
            |--------------------------------------------------------------------------
            */

            'voucher_code'
                => fake()->optional()
                    ->bothify(
                        'VOUCHER-###'
                    ),

            'voucher_discount'
                => fake()->randomFloat(
                    2,
                    0,
                    100000
                ),

            /*
            |--------------------------------------------------------------------------
            | Shipping Snapshot
            |--------------------------------------------------------------------------
            */

            'courier_code'
                => fake()->randomElement([
                    'jne',
                    'jnt',
                    'sicepat',
                    'anteraja',
                    'pos',
                ]),

            'courier_service'
                => fake()->randomElement([
                    'REG',
                    'YES',
                    'ECO',
                    'NEXT DAY',
                ]),

            'tracking_number'
                => null,

            'total_weight'
                => fake()->numberBetween(
                    100,
                    10000
                ),

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'customer_notes'
                => fake()->optional()
                    ->sentence(),

            'admin_notes'
                => null,

            /*
            |--------------------------------------------------------------------------
            | Activity
            |--------------------------------------------------------------------------
            */

            'ordered_at'
                => now(),

            'paid_at'
                => null,

            'shipped_at'
                => null,

            'completed_at'
                => null,

            'cancelled_at'
                => null,

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'metadata' => [
                'source' => 'factory',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Order States
    |--------------------------------------------------------------------------
    */

    public function pending(): static
    {
        return $this->state(
            fn () => [
                'status'
                    => Order::STATUS_PENDING,
            ]
        );
    }

    public function confirmed(): static
    {
        return $this->state(
            fn () => [
                'status'
                    => Order::STATUS_CONFIRMED,
            ]
        );
    }

    public function processing(): static
    {
        return $this->state(
            fn () => [
                'status'
                    => Order::STATUS_PROCESSING,
            ]
        );
    }

    public function shipped(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Order::STATUS_SHIPPED,

                'fulfillment_status'
                    => Order::FULFILLMENT_SHIPPED,

                'shipped_at'
                    => now(),
            ]
        );
    }

    public function completed(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Order::STATUS_COMPLETED,

                'payment_status'
                    => Order::PAYMENT_PAID,

                'fulfillment_status'
                    => Order::FULFILLMENT_DELIVERED,

                'paid_at'
                    => now()->subDays(3),

                'shipped_at'
                    => now()->subDays(2),

                'completed_at'
                    => now(),
            ]
        );
    }

    public function cancelled(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Order::STATUS_CANCELLED,

                'cancelled_at'
                    => now(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Payment States
    |--------------------------------------------------------------------------
    */

    public function paid(): static
    {
        return $this->state(
            fn () => [

                'payment_status'
                    => Order::PAYMENT_PAID,

                'paid_at'
                    => now(),
            ]
        );
    }

    public function failedPayment(): static
    {
        return $this->state(
            fn () => [

                'payment_status'
                    => Order::PAYMENT_FAILED,
            ]
        );
    }

    public function refunded(): static
    {
        return $this->state(
            fn () => [

                'payment_status'
                    => Order::PAYMENT_REFUNDED,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfillment States
    |--------------------------------------------------------------------------
    */

    public function packed(): static
    {
        return $this->state(
            fn () => [

                'fulfillment_status'
                    => Order::FULFILLMENT_PACKED,
            ]
        );
    }

    public function delivered(): static
    {
        return $this->state(
            fn () => [

                'fulfillment_status'
                    => Order::FULFILLMENT_DELIVERED,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Shipping States
    |--------------------------------------------------------------------------
    */

    public function withTracking(): static
    {
        return $this->state(
            fn () => [

                'tracking_number'
                    => strtoupper(
                        fake()->bothify(
                            'TRK########'
                        )
                    ),
            ]
        );
    }

    public function withoutVoucher(): static
    {
        return $this->state(
            fn () => [

                'voucher_code' => null,

                'voucher_discount' => 0,
            ]
        );
    }
}