<?php

namespace Database\Factories;

use App\Models\CheckoutItem;
use App\Models\CheckoutSession;
use App\Models\ProductSku;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CheckoutItem>
 */
class CheckoutItemFactory extends Factory
{
    protected $model = CheckoutItem::class;

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
            10
        );

        $subtotal =
            $price * $quantity;

        $discount =
            fake()->randomFloat(
                2,
                0,
                $subtotal * 0.20
            );

        return [

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            'checkout_session_id'
                => CheckoutSession::factory(),

            'product_sku_id'
                => ProductSku::factory(),

            /*
            |--------------------------------------------------------------------------
            | Product Snapshot
            |--------------------------------------------------------------------------
            */

            'product_name'
                => fake()->words(
                    3,
                    true
                ),

            'sku'
                => strtoupper(
                    fake()->bothify(
                        'SKU-####-???'
                    )
                ),

            'thumbnail'
                => fake()->optional()
                    ->randomElement([
                        'products/product-1.jpg',
                        'products/product-2.jpg',
                        'products/product-3.jpg',
                        null,
                    ]),

            /*
            |--------------------------------------------------------------------------
            | Pricing Snapshot
            |--------------------------------------------------------------------------
            */

            'price'
                => $price,

            'quantity'
                => $quantity,

            'subtotal'
                => $subtotal,

            /*
            |--------------------------------------------------------------------------
            | Promotion Snapshot
            |--------------------------------------------------------------------------
            */

            'discount_amount'
                => $discount,

            'final_price'
                => max(
                    0,
                    $subtotal - $discount
                ),

            /*
            |--------------------------------------------------------------------------
            | Validation Status
            |--------------------------------------------------------------------------
            */

            'is_available'
                => true,

            'is_valid_price'
                => true,

            'is_valid_stock'
                => true,

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'notes'
                => fake()
                    ->optional()
                    ->sentence(),

            /*
            |--------------------------------------------------------------------------
            | Activity
            |--------------------------------------------------------------------------
            */

            'added_at'
                => fake()
                    ->dateTimeBetween(
                        '-7 days',
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
    | Validation States
    |--------------------------------------------------------------------------
    */

    public function validPrice(): static
    {
        return $this->state(
            fn () => [

                'is_valid_price' => true,
            ]
        );
    }

    public function invalidPrice(): static
    {
        return $this->state(
            fn () => [

                'is_valid_price' => false,
            ]
        );
    }

    public function validStock(): static
    {
        return $this->state(
            fn () => [

                'is_valid_stock' => true,
            ]
        );
    }

    public function invalidStock(): static
    {
        return $this->state(
            fn () => [

                'is_valid_stock' => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Promotion States
    |--------------------------------------------------------------------------
    */

    public function withDiscount(
        float $discount = 10000
    ): static {

        return $this->state(
            function (array $attributes)
            use ($discount) {

                return [

                    'discount_amount'
                        => $discount,

                    'final_price'
                        => max(
                            0,
                            $attributes['subtotal']
                            - $discount
                        ),
                ];
            }
        );
    }

    public function withoutDiscount(): static
    {
        return $this->state(
            function (
                array $attributes
            ) {

                return [

                    'discount_amount' => 0,

                    'final_price'
                        => $attributes['subtotal'],
                ];
            }
        );
    }
}