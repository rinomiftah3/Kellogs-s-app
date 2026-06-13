<?php

namespace Database\Factories;

use App\Models\CustomerProfile;
use App\Models\LoyaltyPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoyaltyPoint>
 */
class LoyaltyPointFactory extends Factory
{
    protected $model = LoyaltyPoint::class;

    public function definition(): array
    {
        $earned = fake()->numberBetween(
            100,
            5000
        );

        $redeemed = fake()->numberBetween(
            0,
            (int) ($earned * 0.5)
        );

        $expired = fake()->numberBetween(
            0,
            (int) ($earned * 0.2)
        );

        $available = max(
            0,
            $earned - $redeemed - $expired
        );

        $tier = fake()->randomElement([
            LoyaltyPoint::TIER_BRONZE,
            LoyaltyPoint::TIER_SILVER,
            LoyaltyPoint::TIER_GOLD,
            LoyaltyPoint::TIER_PLATINUM,
        ]);

        return [

            /*
            |--------------------------------------------------------------------------
            | Relationship
            |--------------------------------------------------------------------------
            */

            'customer_profile_id' =>
                CustomerProfile::factory(),

            /*
            |--------------------------------------------------------------------------
            | Point Balance
            |--------------------------------------------------------------------------
            */

            'current_points' =>
                $available,

            'available_points' =>
                $available,

            'pending_points' =>
                fake()->numberBetween(
                    0,
                    500
                ),

            'earned_points' =>
                $earned,

            'redeemed_points' =>
                $redeemed,

            'expired_points' =>
                $expired,

            /*
            |--------------------------------------------------------------------------
            | Lifetime Statistics
            |--------------------------------------------------------------------------
            */

            'lifetime_points' =>
                $earned,

            'lifetime_orders' =>
                fake()->numberBetween(
                    1,
                    100
                ),

            'lifetime_spending' =>
                fake()->randomFloat(
                    2,
                    100000,
                    50000000
                ),

            /*
            |--------------------------------------------------------------------------
            | Membership Tier
            |--------------------------------------------------------------------------
            */

            'tier' => $tier,

            /*
            |--------------------------------------------------------------------------
            | Tier Information
            |--------------------------------------------------------------------------
            */

            'tier_upgraded_at' =>
                now()->subMonths(
                    fake()->numberBetween(
                        1,
                        12
                    )
                ),

            'tier_expires_at' =>
                now()->addMonths(
                    fake()->numberBetween(
                        1,
                        24
                    )
                ),

            /*
            |--------------------------------------------------------------------------
            | Activity
            |--------------------------------------------------------------------------
            */

            'last_earned_at' =>
                now()->subDays(
                    fake()->numberBetween(
                        1,
                        60
                    )
                ),

            'last_redeemed_at' =>
                fake()->boolean(70)
                    ? now()->subDays(
                        fake()->numberBetween(
                            1,
                            30
                        )
                    )
                    : null,

            'last_activity_at' =>
                now()->subDays(
                    fake()->numberBetween(
                        0,
                        30
                    )
                ),

            /*
            |--------------------------------------------------------------------------
            | Expiration
            |--------------------------------------------------------------------------
            */

            'last_expired_at' =>
                $expired > 0
                    ? now()->subMonths(
                        fake()->numberBetween(
                            1,
                            12
                        )
                    )
                    : null,

            'total_expiration_events' =>
                $expired > 0
                    ? fake()->numberBetween(
                        1,
                        10
                    )
                    : 0,

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' => true,

            'published_at' => now(),

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'metadata' => [

                'source' => 'factory',

                'generated_by' =>
                    'LoyaltyPointFactory',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Tier States
    |--------------------------------------------------------------------------
    */

    public function bronze(): static
    {
        return $this->state(fn () => [
            'tier' =>
                LoyaltyPoint::TIER_BRONZE,
        ]);
    }

    public function silver(): static
    {
        return $this->state(fn () => [
            'tier' =>
                LoyaltyPoint::TIER_SILVER,
        ]);
    }

    public function gold(): static
    {
        return $this->state(fn () => [
            'tier' =>
                LoyaltyPoint::TIER_GOLD,
        ]);
    }

    public function platinum(): static
    {
        return $this->state(fn () => [
            'tier' =>
                LoyaltyPoint::TIER_PLATINUM,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Status States
    |--------------------------------------------------------------------------
    */

    public function inactive(): static
    {
        return $this->state(fn () => [

            'is_active' => false,

            'published_at' => null,
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn () => [

            'published_at' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Activity States
    |--------------------------------------------------------------------------
    */

    public function highPoints(): static
    {
        return $this->state(fn () => [

            'current_points' => 10000,

            'available_points' => 10000,

            'earned_points' => 12000,

            'lifetime_points' => 12000,
        ]);
    }

    public function expiredTier(): static
    {
        return $this->state(fn () => [

            'tier_expires_at' =>
                now()->subDays(
                    fake()->numberBetween(
                        1,
                        90
                    )
                ),
        ]);
    }

    public function noPoints(): static
    {
        return $this->state(fn () => [

            'current_points' => 0,

            'available_points' => 0,

            'pending_points' => 0,
        ]);
    }
}