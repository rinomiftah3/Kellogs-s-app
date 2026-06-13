<?php

namespace Database\Factories;

use App\Models\Courier;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Courier>
 */
class CourierFactory extends Factory
{
    protected $model = Courier::class;

    /*
    |--------------------------------------------------------------------------
    | Courier Templates
    |--------------------------------------------------------------------------
    */

    private const COURIERS = [

        [
            'name' => 'JNE',
            'code' => 'jne',
            'provider' => 'Jalur Nugraha Ekakurir',
        ],

        [
            'name' => 'J&T Express',
            'code' => 'jnt',
            'provider' => 'Global Jet Express',
        ],

        [
            'name' => 'SiCepat',
            'code' => 'sicepat',
            'provider' => 'SiCepat Ekspres Indonesia',
        ],

        [
            'name' => 'AnterAja',
            'code' => 'anteraja',
            'provider' => 'Tripatra Engineers',
        ],

        [
            'name' => 'Ninja Xpress',
            'code' => 'ninja',
            'provider' => 'Ninja Van',
        ],

        [
            'name' => 'POS Indonesia',
            'code' => 'pos',
            'provider' => 'PT Pos Indonesia',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $courier = fake()->randomElement(
            self::COURIERS
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Courier Information
            |--------------------------------------------------------------------------
            */

            'name'
                => $courier['name'],

            'code'
                => $courier['code']
                . '-'
                . fake()->unique()->numberBetween(
                    100,
                    999
                ),

            'provider'
                => $courier['provider'],

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            'description'
                => fake()->sentence(12),

            'logo'
                => 'couriers/'
                . Str::slug(
                    $courier['name']
                )
                . '.png',

            /*
            |--------------------------------------------------------------------------
            | Contact Information
            |--------------------------------------------------------------------------
            */

            'website'
                => fake()->url(),

            'contact_email'
                => fake()->companyEmail(),

            'contact_phone'
                => fake()->phoneNumber(),

            /*
            |--------------------------------------------------------------------------
            | Tracking
            |--------------------------------------------------------------------------
            */

            'tracking_url_template'
                => 'https://tracking.example.com/{tracking_number}',

            'supports_tracking'
                => true,

            'supports_cod'
                => fake()->boolean(40),

            'supports_insurance'
                => fake()->boolean(60),

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

                'country' => 'Indonesia',
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

    public function published(): static
    {
        return $this->state(
            fn () => [

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
    | Feature States
    |--------------------------------------------------------------------------
    */

    public function supportsTracking(): static
    {
        return $this->state(
            fn () => [

                'supports_tracking'
                    => true,
            ]
        );
    }

    public function noTracking(): static
    {
        return $this->state(
            fn () => [

                'supports_tracking'
                    => false,

                'tracking_url_template'
                    => null,
            ]
        );
    }

    public function supportsCod(): static
    {
        return $this->state(
            fn () => [

                'supports_cod'
                    => true,
            ]
        );
    }

    public function supportsInsurance(): static
    {
        return $this->state(
            fn () => [

                'supports_insurance'
                    => true,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Courier Presets
    |--------------------------------------------------------------------------
    */

    public function jne(): static
    {
        return $this->state(
            fn () => [

                'name' => 'JNE',

                'code' => 'jne',

                'provider'
                    => 'Jalur Nugraha Ekakurir',
            ]
        );
    }

    public function jnt(): static
    {
        return $this->state(
            fn () => [

                'name' => 'J&T Express',

                'code' => 'jnt',

                'provider'
                    => 'Global Jet Express',
            ]
        );
    }

    public function sicepat(): static
    {
        return $this->state(
            fn () => [

                'name' => 'SiCepat',

                'code' => 'sicepat',

                'provider'
                    => 'SiCepat Ekspres Indonesia',
            ]
        );
    }

    public function anterAja(): static
    {
        return $this->state(
            fn () => [

                'name' => 'AnterAja',

                'code' => 'anteraja',

                'provider'
                    => 'Tripatra Engineers',
            ]
        );
    }

    public function ninja(): static
    {
        return $this->state(
            fn () => [

                'name' => 'Ninja Xpress',

                'code' => 'ninja',

                'provider'
                    => 'Ninja Van',
            ]
        );
    }

    public function posIndonesia(): static
    {
        return $this->state(
            fn () => [

                'name' => 'POS Indonesia',

                'code' => 'pos',

                'provider'
                    => 'PT Pos Indonesia',
            ]
        );
    }
}