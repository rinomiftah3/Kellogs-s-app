<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $amount = fake()->randomFloat(
            2,
            50000,
            5000000
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Relationship
            |--------------------------------------------------------------------------
            */

            'order_id'
                => Order::factory(),

            /*
            |--------------------------------------------------------------------------
            | Payment Information
            |--------------------------------------------------------------------------
            */

            'payment_number'
                => 'PAY-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    fake()->unique()->bothify(
                        '#####'
                    )
                ),

            'gateway'
                => fake()->randomElement([
                    'midtrans',
                    'xendit',
                    'tripay',
                ]),

            'method'
                => fake()->randomElement([
                    'bank_transfer',
                    'virtual_account',
                    'ewallet',
                    'qris',
                    'credit_card',
                ]),

            /*
            |--------------------------------------------------------------------------
            | Amount
            |--------------------------------------------------------------------------
            */

            'amount'
                => $amount,

            'paid_amount'
                => 0,

            'fee_amount'
                => fake()->randomFloat(
                    2,
                    0,
                    10000
                ),

            'refund_amount'
                => 0,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status'
                => Payment::STATUS_PENDING,

            /*
            |--------------------------------------------------------------------------
            | Gateway Reference
            |--------------------------------------------------------------------------
            */

            'gateway_transaction_id'
                => null,

            'gateway_order_id'
                => null,

            /*
            |--------------------------------------------------------------------------
            | Payment URL
            |--------------------------------------------------------------------------
            */

            'payment_url'
                => fake()->url(),

            /*
            |--------------------------------------------------------------------------
            | Activity
            |--------------------------------------------------------------------------
            */

            'paid_at'
                => null,

            'expired_at'
                => now()->addDay(),

            /*
            |--------------------------------------------------------------------------
            | Additional Information
            |--------------------------------------------------------------------------
            */

            'metadata' => [
                'source' => 'factory',
            ],

            'notes' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Payment States
    |--------------------------------------------------------------------------
            */

    public function pending(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Payment::STATUS_PENDING,

                'paid_amount' => 0,

                'paid_at' => null,
            ]
        );
    }

    public function paid(): static
    {
        return $this->state(
            function (array $attributes) {

                return [

                    'status'
                        => Payment::STATUS_PAID,

                    'paid_amount'
                        => $attributes['amount'],

                    'paid_at'
                        => now(),

                    'gateway_transaction_id'
                        => strtoupper(
                            fake()->bothify(
                                'TXN########'
                            )
                        ),

                    'gateway_order_id'
                        => strtoupper(
                            fake()->bothify(
                                'ORD########'
                            )
                        ),
                ];
            }
        );
    }

    public function failed(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Payment::STATUS_FAILED,
            ]
        );
    }

    public function expired(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Payment::STATUS_EXPIRED,

                'expired_at'
                    => now()->subHour(),
            ]
        );
    }

    public function cancelled(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Payment::STATUS_CANCELLED,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refund States
    |--------------------------------------------------------------------------
    */

    public function refunded(): static
    {
        return $this->state(
            function (array $attributes) {

                return [

                    'status'
                        => Payment::STATUS_REFUNDED,

                    'paid_amount'
                        => $attributes['amount'],

                    'refund_amount'
                        => $attributes['amount'],

                    'paid_at'
                        => now()->subDays(3),
                ];
            }
        );
    }

    public function partialRefund(): static
    {
        return $this->state(
            function (array $attributes) {

                $refund = round(
                    $attributes['amount'] * 0.5,
                    2
                );

                return [

                    'status'
                        => Payment::STATUS_PARTIAL_REFUND,

                    'paid_amount'
                        => $attributes['amount'],

                    'refund_amount'
                        => $refund,

                    'paid_at'
                        => now()->subDays(2),
                ];
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Gateway States
    |--------------------------------------------------------------------------
    */

    public function midtrans(): static
    {
        return $this->state(
            fn () => [
                'gateway' => 'midtrans',
            ]
        );
    }

    public function xendit(): static
    {
        return $this->state(
            fn () => [
                'gateway' => 'xendit',
            ]
        );
    }

    public function tripay(): static
    {
        return $this->state(
            fn () => [
                'gateway' => 'tripay',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Method States
    |--------------------------------------------------------------------------
    */

    public function qris(): static
    {
        return $this->state(
            fn () => [
                'method' => 'qris',
            ]
        );
    }

    public function ewallet(): static
    {
        return $this->state(
            fn () => [
                'method' => 'ewallet',
            ]
        );
    }

    public function bankTransfer(): static
    {
        return $this->state(
            fn () => [
                'method' => 'bank_transfer',
            ]
        );
    }
}