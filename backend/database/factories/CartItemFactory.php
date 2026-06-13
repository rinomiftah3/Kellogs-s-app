<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductSku;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CartItem>
 */
class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $price = fake()->randomFloat(
            2,
            10000,
            500000
        );

        $quantity = fake()->numberBetween(
            1,
            5
        );

        return [

            'cart_id' => Cart::factory(),

            'product_sku_id' => ProductSku::factory(),

            /*
            |--------------------------------------------------------------------------
            | Product Snapshot
            |--------------------------------------------------------------------------
            */

            'product_name' => fake()->words(
                3,
                true
            ),

            'sku' => strtoupper(
                fake()->bothify(
                    'SKU-####-???'
                )
            ),

            'thumbnail' => fake()->optional()
                ->randomElement([
                    'products/default.jpg',
                    'products/product-1.jpg',
                    'products/product-2.jpg',
                    null,
                ]),

            /*
            |--------------------------------------------------------------------------
            | Pricing Snapshot
            |--------------------------------------------------------------------------
            */

            'price' => $price,

            'quantity' => $quantity,

            'subtotal' => $price * $quantity,

            /*
            |--------------------------------------------------------------------------
            | Validation Flags
            |--------------------------------------------------------------------------
            */

            'is_available' => true,

            'is_selected' => true,

            /*
            |--------------------------------------------------------------------------
            | Additional Information
            |--------------------------------------------------------------------------
            */

            'notes' => fake()->optional()
                ->sentence(),

            'added_at' => fake()
                ->dateTimeBetween(
                    '-30 days',
                    'now'
                ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Availability States
    |--------------------------------------------------------------------------
    */

    public function available(): static
    {
        return $this->state(
            fn () => [

                'is_available' => true,
            ]
        );
    }

    public function unavailable(): static
    {
        return $this->state(
            fn () => [

                'is_available' => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Selection States
    |--------------------------------------------------------------------------
    */

    public function selected(): static
    {
        return $this->state(
            fn () => [

                'is_selected' => true,
            ]
        );
    }

    public function unselected(): static
    {
        return $this->state(
            fn () => [

                'is_selected' => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Quantity States
    |--------------------------------------------------------------------------
    */

    public function quantity(
        int $quantity
    ): static {

        return $this->state(
            function (array $attributes)
            use ($quantity) {

                return [

                    'quantity' => $quantity,

                    'subtotal' =>
                        (float) $attributes['price']
                        * $quantity,
                ];
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Recent Item State
    |--------------------------------------------------------------------------
    */

    public function recentlyAdded(): static
    {
        return $this->state(
            fn () => [

                'added_at' => now(),
            ]
        );
    }
}