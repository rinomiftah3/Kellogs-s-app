<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\ProductSku;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Inventory Factory
 *
 * Enterprise Ready
 *
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var class-string<Inventory>
     */
    protected $model = Inventory::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $currentStock = fake()->numberBetween(
            50,
            500
        );

        $reservedStock = fake()->numberBetween(
            0,
            min(50, $currentStock)
        );

        $minimumStock = fake()->numberBetween(
            5,
            20
        );

        return [

            'product_sku_id'
                => ProductSku::factory(),

            'current_stock'
                => $currentStock,

            'reserved_stock'
                => $reservedStock,

            'available_stock'
                => $currentStock - $reservedStock,

            'minimum_stock'
                => $minimumStock,

            'maximum_stock'
                => fake()
                    ->optional()
                    ->numberBetween(
                        500,
                        1000
                    ),

            'reorder_point'
                => fake()->numberBetween(
                    $minimumStock,
                    $minimumStock + 20
                ),

            'allow_backorder'
                => false,

            'is_active'
                => true,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Active States
    |--------------------------------------------------------------------------
    */

    public function active(): static
    {
        return $this->state(
            fn () => [

                'is_active'
                    => true,
            ]
        );
    }

    public function inactive(): static
    {
        return $this->state(
            fn () => [

                'is_active'
                    => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Stock States
    |--------------------------------------------------------------------------
    */

    public function inStock(): static
    {
        return $this->state(
            function () {

                $currentStock = fake()->numberBetween(
                    100,
                    500
                );

                $reservedStock = fake()->numberBetween(
                    0,
                    20
                );

                return [

                    'current_stock'
                        => $currentStock,

                    'reserved_stock'
                        => $reservedStock,

                    'available_stock'
                        => $currentStock - $reservedStock,

                    'minimum_stock'
                        => 10,

                    'maximum_stock'
                        => 500,

                    'reorder_point'
                        => 20,

                    'allow_backorder'
                        => false,

                    'is_active'
                        => true,
                ];
            }
        );
    }

    public function lowStock(): static
    {
        return $this->state(
            fn () => [

                'current_stock'
                    => 15,

                'reserved_stock'
                    => 5,

                'available_stock'
                    => 10,

                'minimum_stock'
                    => 10,

                'maximum_stock'
                    => 500,

                'reorder_point'
                    => 15,

                'allow_backorder'
                    => false,

                'is_active'
                    => true,
            ]
        );
    }

    public function outOfStock(): static
    {
        return $this->state(
            fn () => [

                'current_stock'
                    => 0,

                'reserved_stock'
                    => 0,

                'available_stock'
                    => 0,

                'minimum_stock'
                    => 10,

                'maximum_stock'
                    => 500,

                'reorder_point'
                    => 15,

                'allow_backorder'
                    => false,

                'is_active'
                    => true,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Backorder States
    |--------------------------------------------------------------------------
    */

    public function allowBackorder(): static
    {
        return $this->state(
            fn () => [

                'allow_backorder'
                    => true,
            ]
        );
    }

    public function noBackorder(): static
    {
        return $this->state(
            fn () => [

                'allow_backorder'
                    => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SKU State
    |--------------------------------------------------------------------------
    */

    public function sku(
        ProductSku $sku
    ): static {

        return $this->state(
            fn () => [

                'product_sku_id'
                    => $sku->id,
            ]
        );
    }
}