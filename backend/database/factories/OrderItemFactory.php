<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\ProductSku;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(
            2,
            10000,
            1000000
        );

        $discountAmount = fake()->randomFloat(
            2,
            0,
            100000
        );

        $quantity = fake()->numberBetween(
            1,
            10
        );

        $finalPrice = max(
            0,
            $unitPrice - $discountAmount
        );

        $subtotal =
            $finalPrice
            *
            $quantity;

        return [

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            'order_id'
                => Order::factory(),

            'product_sku_id'
                => ProductSku::factory(),

            /*
            |--------------------------------------------------------------------------
            | Product Snapshot
            |--------------------------------------------------------------------------
            */

            'product_id'
                => fake()->numberBetween(
                    1,
                    1000
                ),

            'category_id'
                => fake()->optional()
                    ->numberBetween(
                        1,
                        100
                    ),

            'product_name'
                => fake()->words(
                    3,
                    true
                ),

            'product_slug'
                => fake()->slug(),

            'sku'
                => strtoupper(
                    fake()->bothify(
                        'SKU-#####'
                    )
                ),

            'barcode'
                => fake()->optional()
                    ->ean13(),

            /*
            |--------------------------------------------------------------------------
            | Variant Snapshot
            |--------------------------------------------------------------------------
            */

            'variant_name'
                => fake()->optional()
                    ->randomElement([
                        'Red',
                        'Blue',
                        'Black',
                        'White',
                        'Large',
                        'Medium',
                        'Small',
                    ]),

            /*
            |--------------------------------------------------------------------------
            | Product Information Snapshot
            |--------------------------------------------------------------------------
            */

            'thumbnail'
                => 'products/default.jpg',

            'weight'
                => fake()->numberBetween(
                    100,
                    5000
                ),

            /*
            |--------------------------------------------------------------------------
            | Pricing Snapshot
            |--------------------------------------------------------------------------
            */

            'unit_price'
                => $unitPrice,

            'discount_amount'
                => $discountAmount,

            'final_price'
                => $finalPrice,

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            'quantity'
                => $quantity,

            /*
            |--------------------------------------------------------------------------
            | Totals
            |--------------------------------------------------------------------------
            */

            'subtotal'
                => $subtotal,

            /*
            |--------------------------------------------------------------------------
            | Promotion Snapshot
            |--------------------------------------------------------------------------
            */

            'promotion_name'
                => fake()->optional()
                    ->words(
                        2,
                        true
                    ),

            'promotion_code'
                => fake()->optional()
                    ->bothify(
                        'PROMO-###'
                    ),

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'metadata' => [
                'source' => 'factory',
                'generated_at'
                    => now()->toDateTimeString(),
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing States
    |--------------------------------------------------------------------------
    */

    public function withoutDiscount(): static
    {
        return $this->state(
            function (array $attributes) {

                return [

                    'discount_amount' => 0,

                    'final_price'
                        => $attributes['unit_price'],

                    'subtotal'
                        => $attributes['unit_price']
                        *
                        $attributes['quantity'],
                ];
            }
        );
    }

    public function discounted(): static
    {
        return $this->state(
            function (array $attributes) {

                $discount = fake()->randomFloat(
                    2,
                    1000,
                    50000
                );

                $finalPrice = max(
                    0,
                    $attributes['unit_price']
                    - $discount
                );

                return [

                    'discount_amount'
                        => $discount,

                    'final_price'
                        => $finalPrice,

                    'subtotal'
                        => $finalPrice
                        *
                        $attributes['quantity'],
                ];
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Promotion States
    |--------------------------------------------------------------------------
    */

    public function promoted(): static
    {
        return $this->state(
            fn () => [

                'promotion_name'
                    => fake()->randomElement([
                        'Flash Sale',
                        'Mega Sale',
                        'Payday Promo',
                        'Harbolnas',
                    ]),

                'promotion_code'
                    => strtoupper(
                        fake()->bothify(
                            'PROMO-####'
                        )
                    ),
            ]
        );
    }

    public function withoutPromotion(): static
    {
        return $this->state(
            fn () => [

                'promotion_name' => null,

                'promotion_code' => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Variant States
    |--------------------------------------------------------------------------
    */

    public function withVariant(): static
    {
        return $this->state(
            fn () => [

                'variant_name'
                    => fake()->randomElement([
                        'Red XL',
                        'Blue M',
                        'Black L',
                        'White S',
                    ]),
            ]
        );
    }

    public function withoutVariant(): static
    {
        return $this->state(
            fn () => [

                'variant_name' => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Analytics States
    |--------------------------------------------------------------------------
    */

    public function bestseller(): static
    {
        return $this->state(
            fn () => [

                'quantity'
                    => fake()->numberBetween(
                        20,
                        100
                    ),
            ]
        );
    }

    public function highValue(): static
    {
        return $this->state(
            function (array $attributes) {

                $price = fake()->randomFloat(
                    2,
                    1000000,
                    10000000
                );

                return [

                    'unit_price'
                        => $price,

                    'final_price'
                        => $price,

                    'discount_amount'
                        => 0,

                    'subtotal'
                        => $price
                        *
                        $attributes['quantity'],
                ];
            }
        );
    }
}