<?php

namespace Database\Factories;

use App\Models\Courier;
use App\Models\ShippingMethod;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingMethod>
 */
class ShippingMethodFactory extends Factory
{
    protected $model = ShippingMethod::class;

    /*
    |--------------------------------------------------------------------------
    | Service Templates
    |--------------------------------------------------------------------------
    */

    private const SERVICES = [

        [
            'code' => 'REG',
            'name' => 'Regular Service',
            'min_days' => 2,
            'max_days' => 4,
        ],

        [
            'code' => 'YES',
            'name' => 'Yakin Esok Sampai',
            'min_days' => 1,
            'max_days' => 1,
        ],

        [
            'code' => 'ECO',
            'name' => 'Economy Service',
            'min_days' => 4,
            'max_days' => 7,
        ],

        [
            'code' => 'ONS',
            'name' => 'Over Night Service',
            'min_days' => 1,
            'max_days' => 1,
        ],

        [
            'code' => 'SDS',
            'name' => 'Same Day Service',
            'min_days' => 0,
            'max_days' => 1,
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $service = fake()->randomElement(
            self::SERVICES
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Relationship
            |--------------------------------------------------------------------------
            */

            'courier_id' => Courier::factory(),

            /*
            |--------------------------------------------------------------------------
            | Service Information
            |--------------------------------------------------------------------------
            */

            'service_code'
                => $service['code'],

            'service_name'
                => $service['name'],

            'description'
                => fake()->sentence(12),

            /*
            |--------------------------------------------------------------------------
            | Delivery Estimation
            |--------------------------------------------------------------------------
            */

            'estimated_min_days'
                => $service['min_days'],

            'estimated_max_days'
                => $service['max_days'],

            /*
            |--------------------------------------------------------------------------
            | Features
            |--------------------------------------------------------------------------
            */

            'supports_tracking'
                => true,

            'supports_cod'
                => fake()->boolean(35),

            'supports_insurance'
                => fake()->boolean(60),

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            'base_cost'
                => fake()->numberBetween(
                    5000,
                    25000
                ),

            'cost_per_kg'
                => fake()->numberBetween(
                    1000,
                    7000
                ),

            /*
            |--------------------------------------------------------------------------
            | Weight Rules
            |--------------------------------------------------------------------------
            */

            'minimum_weight'
                => 0,

            'maximum_weight'
                => fake()->optional(0.7)
                    ->numberBetween(
                        10000,
                        50000
                    ),

            /*
            |--------------------------------------------------------------------------
            | Free Shipping
            |--------------------------------------------------------------------------
            */

            'free_shipping_threshold'
                => fake()->optional(0.5)
                    ->numberBetween(
                        100000,
                        1000000
                    ),

            /*
            |--------------------------------------------------------------------------
            | SLA
            |--------------------------------------------------------------------------
            */

            'sla_hours'
                => fake()->optional(0.8)
                    ->numberBetween(
                        12,
                        72
                    ),

            /*
            |--------------------------------------------------------------------------
            | Display
            |--------------------------------------------------------------------------
            */

            'sort_order'
                => fake()->numberBetween(
                    1,
                    100
                ),

            'is_featured'
                => false,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active'
                => true,

            'published_at'
                => now(),

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'metadata' => [

                'source' => 'factory',

                'region' => 'Indonesia',
            ],
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

    public function featured(): static
    {
        return $this->state(
            fn () => [

                'is_featured' => true,
            ]
        );
    }

    public function published(): static
    {
        return $this->state(
            fn () => [

                'published_at' => now(),
            ]
        );
    }

    public function unpublished(): static
    {
        return $this->state(
            fn () => [

                'published_at' => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Feature States
    |--------------------------------------------------------------------------
    */

    public function supportsCod(): static
    {
        return $this->state(
            fn () => [

                'supports_cod' => true,
            ]
        );
    }

    public function supportsInsurance(): static
    {
        return $this->state(
            fn () => [

                'supports_insurance' => true,
            ]
        );
    }

    public function supportsTracking(): static
    {
        return $this->state(
            fn () => [

                'supports_tracking' => true,
            ]
        );
    }

    public function freeShipping(): static
    {
        return $this->state(
            fn () => [

                'free_shipping_threshold'
                    => 100000,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Shipping Service Presets
    |--------------------------------------------------------------------------
    */

    public function regular(): static
    {
        return $this->state(
            fn () => [

                'service_code' => 'REG',

                'service_name'
                    => 'Regular Service',

                'estimated_min_days' => 2,

                'estimated_max_days' => 4,
            ]
        );
    }

    public function express(): static
    {
        return $this->state(
            fn () => [

                'service_code' => 'YES',

                'service_name'
                    => 'Express Service',

                'estimated_min_days' => 1,

                'estimated_max_days' => 1,
            ]
        );
    }

    public function economy(): static
    {
        return $this->state(
            fn () => [

                'service_code' => 'ECO',

                'service_name'
                    => 'Economy Service',

                'estimated_min_days' => 4,

                'estimated_max_days' => 7,
            ]
        );
    }

    public function sameDay(): static
    {
        return $this->state(
            fn () => [

                'service_code' => 'SDS',

                'service_name'
                    => 'Same Day Service',

                'estimated_min_days' => 0,

                'estimated_max_days' => 1,
            ]
        );
    }
}