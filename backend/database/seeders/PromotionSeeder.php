<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $promotions = [

            [
                'name' => 'Diskon 10 Persen Semua Produk',
                'code' => 'PROMO10',
                'description' => 'Diskon 10% untuk seluruh produk.',
                'type' => 'percentage_discount',
                'discount_value' => 10,
                'maximum_discount' => 50000,
                'minimum_purchase' => 100000,
                'is_featured' => true,
                'is_stackable' => false,
                'start_at' => $now->copy()->subDays(7),
                'end_at' => $now->copy()->addMonths(3),
                'sort_order' => 1,
            ],

            [
                'name' => 'Diskon Tetap 25 Ribu',
                'code' => 'FIX25K',
                'description' => 'Potongan langsung Rp25.000.',
                'type' => 'fixed_discount',
                'discount_value' => 25000,
                'maximum_discount' => null,
                'minimum_purchase' => 150000,
                'is_featured' => true,
                'is_stackable' => false,
                'start_at' => $now->copy()->subDays(7),
                'end_at' => $now->copy()->addMonths(2),
                'sort_order' => 2,
            ],

            [
                'name' => 'Gratis Ongkir Nasional',
                'code' => 'FREESHIP',
                'description' => 'Gratis ongkir seluruh Indonesia.',
                'type' => 'free_shipping',
                'discount_value' => 0,
                'maximum_discount' => null,
                'minimum_purchase' => 100000,
                'is_featured' => true,
                'is_stackable' => true,
                'start_at' => $now->copy()->subDays(3),
                'end_at' => $now->copy()->addMonths(6),
                'sort_order' => 3,
            ],

            [
                'name' => 'Flash Sale Mingguan',
                'code' => 'FLASHSALE',
                'description' => 'Flash sale untuk produk tertentu.',
                'type' => 'flash_sale',
                'discount_value' => 0,
                'maximum_discount' => null,
                'minimum_purchase' => 0,
                'is_featured' => true,
                'is_stackable' => false,
                'start_at' => $now->copy()->subDay(),
                'end_at' => $now->copy()->addDays(7),
                'sort_order' => 4,
            ],

            [
                'name' => 'Beli 2 Gratis 1',
                'code' => 'BUY2GET1',
                'description' => 'Promo Buy 2 Get 1.',
                'type' => 'buy_x_get_y',
                'discount_value' => 0,
                'maximum_discount' => null,
                'minimum_purchase' => 0,
                'buy_quantity' => 2,
                'free_quantity' => 1,
                'is_featured' => true,
                'is_stackable' => false,
                'start_at' => $now->copy()->subDays(2),
                'end_at' => $now->copy()->addMonths(1),
                'sort_order' => 5,
            ],

            [
                'name' => 'Promo Kadaluarsa',
                'code' => 'EXPIREDPROMO',
                'description' => 'Digunakan untuk testing promotion expiration.',
                'type' => 'percentage_discount',
                'discount_value' => 15,
                'maximum_discount' => 50000,
                'minimum_purchase' => 100000,
                'is_featured' => false,
                'is_stackable' => false,
                'start_at' => $now->copy()->subMonths(2),
                'end_at' => $now->copy()->subMonth(),
                'sort_order' => 999,
            ],
        ];

        foreach ($promotions as $promotion) {

            Promotion::updateOrCreate(
                [
                    'code' => $promotion['code'],
                ],
                [
                    'name' => $promotion['name'],
                    'description' => $promotion['description'],
                    'type' => $promotion['type'],
                    'discount_value' => $promotion['discount_value'],
                    'maximum_discount' => $promotion['maximum_discount'],
                    'minimum_purchase' => $promotion['minimum_purchase'],
                    'buy_quantity' => $promotion['buy_quantity'] ?? null,
                    'free_quantity' => $promotion['free_quantity'] ?? null,
                    'usage_limit' => null,
                    'used_count' => 0,
                    'is_active' => true,
                    'is_featured' => $promotion['is_featured'],
                    'is_stackable' => $promotion['is_stackable'],
                    'start_at' => $promotion['start_at'],
                    'end_at' => $promotion['end_at'],
                    'banner_image' => null,
                    'sort_order' => $promotion['sort_order'],
                    'metadata' => null,
                ]
            );
        }
    }
}