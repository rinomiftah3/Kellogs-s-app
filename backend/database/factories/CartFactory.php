<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CustomerProfile;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $totalItems = fake()->numberBetween(0, 10);

        return [

            'customer_profile_id' => CustomerProfile::factory(),

            'total_items' => $totalItems,

            'subtotal' => $totalItems > 0
                ? fake()->randomFloat(
                    2,
                    10000,
                    500000
                )
                : 0,

            'is_active' => true,

            'last_activity_at' => fake()
                ->dateTimeBetween(
                    '-7 days',
                    'now'
                ),

            'expires_at' => now()
                ->addDays(
                    fake()->numberBetween(
                        1,
                        30
                    )
                ),
        ];
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

    /*
    |--------------------------------------------------------------------------
    | Cart Content States
    |--------------------------------------------------------------------------
    */

    public function empty(): static
    {
        return $this->state(
            fn () => [

                'total_items' => 0,

                'subtotal' => 0,
            ]
        );
    }

    public function withItems(
        int $items = 3
    ): static {

        return $this->state(
            fn () => [

                'total_items' => $items,

                'subtotal' => fake()
                    ->randomFloat(
                        2,
                        50000,
                        1000000
                    ),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Expiration States
    |--------------------------------------------------------------------------
    */

    public function expired(): static
    {
        return $this->state(
            fn () => [

                'expires_at' => now()
                    ->subDay(),
            ]
        );
    }

    public function notExpired(): static
    {
        return $this->state(
            fn () => [

                'expires_at' => now()
                    ->addDays(7),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Activity States
    |--------------------------------------------------------------------------
    */

    public function abandoned(): static
    {
        return $this->state(
            fn () => [

                'last_activity_at' => now()
                    ->subHours(24),
            ]
        );
    }

    public function recentlyActive(): static
    {
        return $this->state(
            fn () => [

                'last_activity_at' => now(),
            ]
        );
    }
}