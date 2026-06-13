<?php

namespace Database\Factories;

use App\Models\CustomerAddress;
use App\Models\CustomerProfile;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerAddress>
 */
class CustomerAddressFactory extends Factory
{
    /**
     * Factory Model
     */
    protected $model = CustomerAddress::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [

            'customer_profile_id'
                => CustomerProfile::factory(),

            'label'
                => fake()->randomElement([
                    'Rumah',
                    'Kantor',
                    'Kos',
                    'Gudang',
                ]),

            'recipient_name'
                => fake()->name(),

            'recipient_phone'
                => fake()->numerify(
                    '08##########'
                ),

            'address'
                => fake()->streetAddress(),

            'province'
                => fake()->randomElement([
                    'DKI Jakarta',
                    'Jawa Barat',
                    'Jawa Tengah',
                    'Jawa Timur',
                    'Banten',
                    'DI Yogyakarta',
                ]),

            'city'
                => fake()->city(),

            'district'
                => fake()->citySuffix(),

            'subdistrict'
                => fake()->streetName(),

            'postal_code'
                => fake()->postcode(),

            'latitude'
                => fake()->optional(70)
                    ->latitude(
                        -11,
                        6
                    ),

            'longitude'
                => fake()->optional(70)
                    ->longitude(
                        95,
                        141
                    ),

            'is_default'
                => false,

            'is_active'
                => true,

            'notes'
                => fake()->optional(40)
                    ->sentence(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | State : Default Address
    |--------------------------------------------------------------------------
    */

    public function default(): static
    {
        return $this->state(fn () => [

            'is_default' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | State : Active
    |--------------------------------------------------------------------------
    */

    public function active(): static
    {
        return $this->state(fn () => [

            'is_active' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | State : Inactive
    |--------------------------------------------------------------------------
    */

    public function inactive(): static
    {
        return $this->state(fn () => [

            'is_active' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | State : Without Coordinates
    |--------------------------------------------------------------------------
    */

    public function withoutCoordinates(): static
    {
        return $this->state(fn () => [

            'latitude' => null,

            'longitude' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | State : With Coordinates
    |--------------------------------------------------------------------------
    */

    public function withCoordinates(): static
    {
        return $this->state(fn () => [

            'latitude'
                => fake()->latitude(
                    -11,
                    6
                ),

            'longitude'
                => fake()->longitude(
                    95,
                    141
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | State : Home Address
    |--------------------------------------------------------------------------
    */

    public function home(): static
    {
        return $this->state(fn () => [

            'label' => 'Rumah',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | State : Office Address
    |--------------------------------------------------------------------------
    */

    public function office(): static
    {
        return $this->state(fn () => [

            'label' => 'Kantor',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | State : Warehouse Address
    |--------------------------------------------------------------------------
    */

    public function warehouse(): static
    {
        return $this->state(fn () => [

            'label' => 'Gudang',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | State : For Specific Customer
    |--------------------------------------------------------------------------
    */

    public function forCustomer(
        ?CustomerProfile $customer = null
    ): static {

        return $this->state(fn () => [

            'customer_profile_id'
                => $customer?->id
                ?? CustomerProfile::factory(),
        ]);
    }
}