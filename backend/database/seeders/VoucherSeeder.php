<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vouchers = [

            /*
            |--------------------------------------------------------------------------
            | Fixed Discount
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Voucher Member Baru',
                'code' => 'WELCOME10K',
                'description' => 'Potongan Rp10.000 untuk member baru.',
                'type' => 'fixed',

                'discount_value' => 10000,
                'maximum_discount' => null,

                'minimum_purchase' => 50000,

                'usage_limit' => 1000,
                'usage_per_user' => 1,
                'used_count' => 0,

                'is_active' => true,
                'is_public' => true,
                'is_stackable' => false,

                'start_at' => now()->subDays(30),
                'end_at' => now()->addYear(),

                'metadata' => json_encode([
                    'category' => 'welcome',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Percentage Discount
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Diskon 10 Persen',
                'code' => 'HEMAT10',
                'description' => 'Diskon 10% maksimal Rp50.000.',
                'type' => 'percentage',

                'discount_value' => 10,
                'maximum_discount' => 50000,

                'minimum_purchase' => 100000,

                'usage_limit' => 5000,
                'usage_per_user' => 3,
                'used_count' => 0,

                'is_active' => true,
                'is_public' => true,
                'is_stackable' => false,

                'start_at' => now()->subDays(7),
                'end_at' => now()->addMonths(6),

                'metadata' => json_encode([
                    'category' => 'percentage',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Free Shipping
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Gratis Ongkir Nasional',
                'code' => 'FREESHIP',
                'description' => 'Gratis ongkir seluruh Indonesia.',
                'type' => 'free_shipping',

                'discount_value' => 0,
                'maximum_discount' => null,

                'minimum_purchase' => 75000,

                'usage_limit' => null,
                'usage_per_user' => 5,
                'used_count' => 0,

                'is_active' => true,
                'is_public' => true,
                'is_stackable' => true,

                'start_at' => now()->subDays(30),
                'end_at' => now()->addYear(),

                'metadata' => json_encode([
                    'category' => 'shipping',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | VIP Voucher
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Voucher Platinum',
                'code' => 'VIP25',
                'description' => 'Diskon 25% untuk pelanggan VIP.',
                'type' => 'percentage',

                'discount_value' => 25,
                'maximum_discount' => 100000,

                'minimum_purchase' => 250000,

                'usage_limit' => 500,
                'usage_per_user' => 1,
                'used_count' => 0,

                'is_active' => true,
                'is_public' => false,
                'is_stackable' => false,

                'start_at' => now()->subDays(1),
                'end_at' => now()->addMonths(3),

                'metadata' => json_encode([
                    'membership' => 'platinum',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Expired Voucher
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Voucher Lama',
                'code' => 'EXPIRED2025',
                'description' => 'Voucher expired untuk testing.',
                'type' => 'fixed',

                'discount_value' => 20000,
                'maximum_discount' => null,

                'minimum_purchase' => 50000,

                'usage_limit' => 100,
                'usage_per_user' => 1,
                'used_count' => 0,

                'is_active' => false,
                'is_public' => true,
                'is_stackable' => false,

                'start_at' => now()->subYear(),
                'end_at' => now()->subMonth(),

                'metadata' => json_encode([
                    'status' => 'expired',
                ]),
            ],
        ];

        foreach ($vouchers as $voucher) {

            Voucher::updateOrCreate(
                [
                    'code' => $voucher['code'],
                ],
                $voucher
            );
        }
    }
}