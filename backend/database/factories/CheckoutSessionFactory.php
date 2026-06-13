<?php

namespace Database\Factories;

use App\Models\CheckoutSession;
use App\Models\CustomerProfile;
use App\Models\CustomerAddress;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CheckoutSession>
 */
class CheckoutSessionFactory extends Factory
{
    protected $model = CheckoutSession::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(
            2,
            50000,
            1500000
        );

        $voucherDiscount = fake()->randomFloat(
            2,
            0,
            50000
        );

        $promotionDiscount = fake()->randomFloat(
            2,
            0,
            100000
        );

        $shippingCost = fake()->randomFloat(
            2,
            10000,
            50000
        );

        $totalDiscount =
            $voucherDiscount +
            $promotionDiscount;

        return [

            'customer_profile_id'
                => CustomerProfile::factory(),

            'shipping_address_id'
                => CustomerAddress::factory(),

            'session_code'
                => 'CHK-' .
                strtoupper(
                    Str::random(12)
                ),

            'status'
                => CheckoutSession::STATUS_DRAFT,

            /*
            |--------------------------------------------------------------------------
            | Voucher & Promotion
            |--------------------------------------------------------------------------
            */

            'voucher_code'
                => null,

            'voucher_discount'
                => $voucherDiscount,

            'promotion_discount'
                => $promotionDiscount,

            /*
            |--------------------------------------------------------------------------
            | Pricing Summary
            |--------------------------------------------------------------------------
            */

            'subtotal'
                => $subtotal,

            'shipping_cost'
                => $shippingCost,

            'total_discount'
                => $totalDiscount,

            'grand_total'
                => max(
                    0,
                    $subtotal
                    + $shippingCost
                    - $totalDiscount
                ),

            /*
            |--------------------------------------------------------------------------
            | Shipping Snapshot
            |--------------------------------------------------------------------------
            */

            'courier_code'
                => fake()->randomElement([
                    'jne',
                    'jnt',
                    'sicepat',
                    'pos',
                ]),

            'courier_service'
                => fake()->randomElement([
                    'REG',
                    'YES',
                    'BEST',
                    'EXPRESS',
                ]),

            'total_weight'
                => fake()->numberBetween(
                    100,
                    10000
                ),

            /*
            |--------------------------------------------------------------------------
            | Validation Flags
            |--------------------------------------------------------------------------
            */

            'is_price_valid' => true,

            'is_stock_valid' => true,

            'is_voucher_valid' => true,

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'notes'
                => fake()->optional()
                    ->sentence(),

            /*
            |--------------------------------------------------------------------------
            | Lifecycle
            |--------------------------------------------------------------------------
            */

            'expired_at'
                => now()->addHours(
                    fake()->numberBetween(
                        1,
                        24
                    )
                ),

            'checked_out_at'
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
                    => CheckoutSession::STATUS_DRAFT,
            ]
        );
    }

    public function ready(): static
    {
        return $this->state(
            fn () => [
                'status'
                    => CheckoutSession::STATUS_READY,
            ]
        );
    }

    public function checkedOut(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => CheckoutSession::STATUS_CHECKED_OUT,

                'checked_out_at'
                    => now(),
            ]
        );
    }

    public function expired(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => CheckoutSession::STATUS_EXPIRED,

                'expired_at'
                    => now()->subHour(),
            ]
        );
    }

    public function cancelled(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => CheckoutSession::STATUS_CANCELLED,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation States
    |--------------------------------------------------------------------------
    */

    public function invalidStock(): static
    {
        return $this->state(
            fn () => [

                'is_stock_valid' => false,
            ]
        );
    }

    public function invalidPrice(): static
    {
        return $this->state(
            fn () => [

                'is_price_valid' => false,
            ]
        );
    }

    public function invalidVoucher(): static
    {
        return $this->state(
            fn () => [

                'is_voucher_valid' => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Voucher State
    |--------------------------------------------------------------------------
    */

    public function withVoucher(
        string $code = 'WELCOME10'
    ): static {

        return $this->state(
            fn () => [

                'voucher_code'
                    => $code,
            ]
        );
    }
}