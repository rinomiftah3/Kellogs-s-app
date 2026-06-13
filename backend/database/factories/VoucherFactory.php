<?php

namespace Database\Factories;

use App\Models\Voucher;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $startAt = fake()->dateTimeBetween(
            '-15 days',
            '+15 days'
        );

        $endAt = (clone $startAt)
            ->modify(
                '+' . fake()->numberBetween(
                    7,
                    90
                ) . ' days'
            );

        return [

            /*
            |--------------------------------------------------------------------------
            | Voucher Information
            |--------------------------------------------------------------------------
            */

            'name'
                => strtoupper(
                    fake()->words(
                        2,
                        true
                    )
                ) . ' Voucher',

            'code'
                => strtoupper(
                    fake()->unique()->bothify(
                        'VCH-####-???'
                    )
                ),

            'description'
                => fake()->sentence(),

            /*
            |--------------------------------------------------------------------------
            | Voucher Type
            |--------------------------------------------------------------------------
            */

            'type'
                => Voucher::TYPE_FIXED,

            /*
            |--------------------------------------------------------------------------
            | Discount Rules
            |--------------------------------------------------------------------------
            */

            'discount_value'
                => fake()->randomFloat(
                    2,
                    10000,
                    100000
                ),

            'maximum_discount'
                => null,

            'minimum_purchase'
                => fake()->randomFloat(
                    2,
                    50000,
                    500000
                ),

            /*
            |--------------------------------------------------------------------------
            | Usage Rules
            |--------------------------------------------------------------------------
            */

            'usage_limit'
                => fake()->optional()
                    ->numberBetween(
                        100,
                        5000
                    ),

            'usage_per_user'
                => fake()->numberBetween(
                    1,
                    5
                ),

            /*
            |--------------------------------------------------------------------------
            | Cache Counter
            |--------------------------------------------------------------------------
            */

            'used_count'
                => 0,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active'
                => true,

            'is_public'
                => true,

            'is_stackable'
                => false,

            /*
            |--------------------------------------------------------------------------
            | Validity
            |--------------------------------------------------------------------------
            */

            'start_at'
                => $startAt,

            'end_at'
                => $endAt,

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
    | Voucher Types
    |--------------------------------------------------------------------------
    */

    public function fixed(): static
    {
        return $this->state(
            fn () => [

                'type'
                    => Voucher::TYPE_FIXED,

                'discount_value'
                    => fake()->randomFloat(
                        2,
                        10000,
                        100000
                    ),

                'maximum_discount'
                    => null,
            ]
        );
    }

    public function percentage(): static
    {
        return $this->state(
            fn () => [

                'type'
                    => Voucher::TYPE_PERCENTAGE,

                'discount_value'
                    => fake()->numberBetween(
                        5,
                        50
                    ),

                'maximum_discount'
                    => fake()->randomFloat(
                        2,
                        25000,
                        200000
                    ),
            ]
        );
    }

    public function freeShipping(): static
    {
        return $this->state(
            fn () => [

                'type'
                    => Voucher::TYPE_FREE_SHIPPING,

                'discount_value'
                    => 0,

                'maximum_discount'
                    => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Status States
    |--------------------------------------------------------------------------
    */

    public function active(): static
    {
        return $this->state(
            fn () => [

                'is_active' => true,
            ]
        );
    }

    public function inactive(): static
    {
        return $this->state(
            fn () => [

                'is_active' => false,
            ]
        );
    }

    public function public(): static
    {
        return $this->state(
            fn () => [

                'is_public' => true,
            ]
        );
    }

    public function private(): static
    {
        return $this->state(
            fn () => [

                'is_public' => false,
            ]
        );
    }

    public function stackable(): static
    {
        return $this->state(
            fn () => [

                'is_stackable' => true,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validity States
    |--------------------------------------------------------------------------
    */

    public function expired(): static
    {
        return $this->state(
            fn () => [

                'start_at'
                    => now()->subDays(30),

                'end_at'
                    => now()->subDay(),
            ]
        );
    }

    public function upcoming(): static
    {
        return $this->state(
            fn () => [

                'start_at'
                    => now()->addDays(7),

                'end_at'
                    => now()->addDays(30),
            ]
        );
    }

    public function valid(): static
    {
        return $this->state(
            fn () => [

                'start_at'
                    => now()->subDay(),

                'end_at'
                    => now()->addDays(30),

                'is_active'
                    => true,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Usage States
    |--------------------------------------------------------------------------
    */

    public function unlimited(): static
    {
        return $this->state(
            fn () => [

                'usage_limit' => null,
            ]
        );
    }

    public function exhausted(): static
    {
        return $this->state(
            fn () => [

                'usage_limit' => 100,

                'used_count' => 100,
            ]
        );
    }
}