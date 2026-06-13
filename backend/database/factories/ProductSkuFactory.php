<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductSku;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Product SKU Factory
 *
 * Enterprise Ready
 *
 * @extends Factory<ProductSku>
 */
class ProductSkuFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var class-string<ProductSku>
     */
    protected $model = ProductSku::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $price = fake()->numberBetween(
            15000,
            250000
        );

        return [

            'product_id'
                => Product::factory(),

            'sku'
                => strtoupper(
                    fake()->bothify(
                        'SKU-#####'
                    )
                ),

            'barcode'
                => fake()->optional()
                    ->ean13(),

            'price'
                => $price,

            'compare_at_price'
                => null,

            'cost_price'
                => round(
                    $price * 0.7,
                    2
                ),

            'weight'
                => fake()->randomFloat(
                    2,
                    0.10,
                    5.00
                ),

            'length'
                => fake()->randomFloat(
                    2,
                    5,
                    50
                ),

            'width'
                => fake()->randomFloat(
                    2,
                    5,
                    50
                ),

            'height'
                => fake()->randomFloat(
                    2,
                    5,
                    50
                ),

            'minimum_order_quantity'
                => 1,

            'maximum_order_quantity'
                => null,

            'is_default'
                => false,

            'status'
                => ProductSku::STATUS_DRAFT,

            'is_active'
                => true,

            'published_at'
                => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Status States
    |--------------------------------------------------------------------------
    */

    public function draft(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => ProductSku::STATUS_DRAFT,

                'published_at'
                    => null,
            ]
        );
    }

    public function active(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => ProductSku::STATUS_ACTIVE,

                'is_active'
                    => true,

                'published_at'
                    => now(),
            ]
        );
    }

    public function inactive(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => ProductSku::STATUS_INACTIVE,

                'is_active'
                    => false,
            ]
        );
    }

    public function archived(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => ProductSku::STATUS_ARCHIVED,

                'is_active'
                    => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Default SKU States
    |--------------------------------------------------------------------------
    */

    public function default(): static
    {
        return $this->state(
            fn () => [

                'is_default'
                    => true,
            ]
        );
    }

    public function nonDefault(): static
    {
        return $this->state(
            fn () => [

                'is_default'
                    => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Publish States
    |--------------------------------------------------------------------------
    */

    public function published(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => ProductSku::STATUS_ACTIVE,

                'is_active'
                    => true,

                'published_at'
                    => now(),
            ]
        );
    }

    public function unpublished(): static
    {
        return $this->state(
            fn () => [

                'published_at'
                    => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing States
    |--------------------------------------------------------------------------
    */

    public function discounted(): static
    {
        return $this->state(
            function (array $attributes) {

                $price = $attributes['price'];

                return [

                    'compare_at_price'
                        => round(
                            $price * 1.25,
                            2
                        ),
                ];
            }
        );
    }

    public function noDiscount(): static
    {
        return $this->state(
            fn () => [

                'compare_at_price'
                    => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Product State
    |--------------------------------------------------------------------------
    */

    public function product(
        Product $product
    ): static {

        return $this->state(
            fn () => [

                'product_id'
                    => $product->id,
            ]
        );
    }
}