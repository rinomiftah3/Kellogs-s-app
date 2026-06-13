<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\CustomerProfile;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Customer Profile Factory
 *
 * Enterprise Ready
 *
 * @extends Factory<CustomerProfile>
 */
class CustomerProfileFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var class-string<CustomerProfile>
     */
    protected $model = CustomerProfile::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        return [

            'user_id'
                => User::factory(),

            'customer_code'
                => 'CUS-' .
                    fake()->unique()->numerify(
                        '#####'
                    ),

            'full_name'
                => fake()->name(),

            'phone'
                => fake()->numerify(
                    '08##########'
                ),

            'gender'
                => fake()->randomElement([
                    CustomerProfile::GENDER_MALE,
                    CustomerProfile::GENDER_FEMALE,
                ]),

            'birth_date'
                => fake()->dateTimeBetween(
                    '-50 years',
                    '-18 years'
                ),

            'avatar'
                => null,

            'bio'
                => fake()->optional()
                    ->sentence(),

            'membership_level'
                => CustomerProfile::LEVEL_REGULAR,

            'total_points'
                => 0,

            'total_spent'
                => 0,

            'total_orders'
                => 0,

            'is_active'
                => true,

            'last_order_at'
                => null,

            'email_subscribed'
                => true,

            'sms_subscribed'
                => false,

            'push_subscribed'
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
    | Gender States
    |--------------------------------------------------------------------------
    */

    public function male(): static
    {
        return $this->state(
            fn () => [

                'gender'
                    => CustomerProfile::GENDER_MALE,
            ]
        );
    }

    public function female(): static
    {
        return $this->state(
            fn () => [

                'gender'
                    => CustomerProfile::GENDER_FEMALE,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Membership States
    |--------------------------------------------------------------------------
    */

    public function regular(): static
    {
        return $this->state(
            fn () => [

                'membership_level'
                    => CustomerProfile::LEVEL_REGULAR,
            ]
        );
    }

    public function silver(): static
    {
        return $this->state(
            fn () => [

                'membership_level'
                    => CustomerProfile::LEVEL_SILVER,

                'total_points'
                    => fake()->numberBetween(
                        100,
                        999
                    ),
            ]
        );
    }

    public function gold(): static
    {
        return $this->state(
            fn () => [

                'membership_level'
                    => CustomerProfile::LEVEL_GOLD,

                'total_points'
                    => fake()->numberBetween(
                        1000,
                        4999
                    ),
            ]
        );
    }

    public function platinum(): static
    {
        return $this->state(
            fn () => [

                'membership_level'
                    => CustomerProfile::LEVEL_PLATINUM,

                'total_points'
                    => fake()->numberBetween(
                        5000,
                        20000
                    ),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Avatar States
    |--------------------------------------------------------------------------
    */

    public function withAvatar(
        ?string $path = null
    ): static {

        return $this->state(
            fn () => [

                'avatar'
                    => $path
                    ?? 'avatars/default.jpg',
            ]
        );
    }

    public function withoutAvatar(): static
    {
        return $this->state(
            fn () => [

                'avatar' => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Subscription States
    |--------------------------------------------------------------------------
    */

    public function subscribed(): static
    {
        return $this->state(
            fn () => [

                'email_subscribed' => true,

                'sms_subscribed' => true,

                'push_subscribed' => true,
            ]
        );
    }

    public function unsubscribed(): static
    {
        return $this->state(
            fn () => [

                'email_subscribed' => false,

                'sms_subscribed' => false,

                'push_subscribed' => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Activity States
    |--------------------------------------------------------------------------
    */

    public function withOrders(): static
    {
        return $this->state(
            fn () => [

                'total_orders'
                    => fake()->numberBetween(
                        1,
                        50
                    ),

                'total_spent'
                    => fake()->numberBetween(
                        100000,
                        10000000
                    ),

                'last_order_at'
                    => now()->subDays(
                        rand(1, 30)
                    ),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | User State
    |--------------------------------------------------------------------------
    */

    public function user(
        User $user
    ): static {

        return $this->state(
            fn () => [

                'user_id'
                    => $user->id,
            ]
        );
    }
}