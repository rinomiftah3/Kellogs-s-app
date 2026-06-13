<?php

namespace Database\Factories;

use App\Models\Promotion;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promotion>
 */
class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

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
                '+' .
                fake()->numberBetween(
                    7,
                    90
                ) .
                ' days'
            );

        return [

            /*
            |--------------------------------------------------------------------------
            | Promotion Information
            |--------------------------------------------------------------------------
            */

            'name' => ucwords(
                fake()->words(
                    3,
                    true
                )
            ),

            'code' => strtoupper(
                fake()->unique()->bothify(
                    'PROMO-####-???'
                )
            ),

            'description' => fake()->sentence(),

            /*
            |--------------------------------------------------------------------------
            | Promotion Type
            |--------------------------------------------------------------------------
            */

            'type'
                => Promotion::TYPE_FIXED_DISCOUNT,

            /*
            |--------------------------------------------------------------------------
            | Discount Configuration
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
            | Buy X Get Y
            |--------------------------------------------------------------------------
            */

            'buy_quantity' => null,

            'free_quantity' => null,

            /*
            |--------------------------------------------------------------------------
            | Usage Limitation
            |--------------------------------------------------------------------------
            */

            'usage_limit'
                => fake()->optional()
                    ->numberBetween(
                        100,
                        10000
                    ),

            /*
            |--------------------------------------------------------------------------
            | Cache Counter
            |--------------------------------------------------------------------------
            */

            'used_count' => 0,

            /*
            |--------------------------------------------------------------------------
            | Flags
            |--------------------------------------------------------------------------
            */

            'is_active' => true,

            'is_featured' => false,

            'is_stackable' => false,

            /*
            |--------------------------------------------------------------------------
            | Schedule
            |--------------------------------------------------------------------------
            */

            'start_at' => $startAt,

            'end_at' => $endAt,

            /*
            |--------------------------------------------------------------------------
            | Display
            |--------------------------------------------------------------------------
            */

            'banner_image'
                => fake()->optional()
                    ->randomElement([
                        'promotions/banner-1.jpg',
                        'promotions/banner-2.jpg',
                        'promotions/banner-3.jpg',
                        null,
                    ]),

            'sort_order'
                => fake()->numberBetween(
                    0,
                    100
                ),

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
    | Promotion Types
    |--------------------------------------------------------------------------
    */

    public function fixedDiscount(): static
    {
        return $this->state(
            fn () => [

                'type'
                    => Promotion::TYPE_FIXED_DISCOUNT,

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

    public function percentageDiscount(): static
    {
        return $this->state(
            fn () => [

                'type'
                    => Promotion::TYPE_PERCENTAGE_DISCOUNT,

                'discount_value'
                    => fake()->numberBetween(
                        5,
                        50
                    ),

                'maximum_discount'
                    => fake()->randomFloat(
                        2,
                        25000,
                        300000
                    ),
            ]
        );
    }

    public function flashSale(): static
    {
        return $this->state(
            fn () => [

                'type'
                    => Promotion::TYPE_FLASH_SALE,

                'discount_value'
                    => fake()->numberBetween(
                        10,
                        70
                    ),

                'maximum_discount'
                    => fake()->randomFloat(
                        2,
                        50000,
                        500000
                    ),

                'is_featured' => true,
            ]
        );
    }

    public function buyXGetY(): static
    {
        return $this->state(
            fn () => [

                'type'
                    => Promotion::TYPE_BUY_X_GET_Y,

                'discount_value' => 0,

                'maximum_discount' => null,

                'buy_quantity'
                    => fake()->numberBetween(
                        2,
                        5
                    ),

                'free_quantity'
                    => fake()->numberBetween(
                        1,
                        3
                    ),
            ]
        );
    }

    public function freeShipping(): static
    {
        return $this->state(
            fn () => [

                'type'
                    => Promotion::TYPE_FREE_SHIPPING,

                'discount_value' => 0,

                'maximum_discount' => null,
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

    public function featured(): static
    {
        return $this->state(
            fn () => [
                'is_featured' => true,
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
    | Schedule States
    |--------------------------------------------------------------------------
    */

    public function running(): static
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