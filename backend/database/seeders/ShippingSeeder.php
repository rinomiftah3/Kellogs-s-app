<?php

namespace Database\Seeders;

use App\Models\Courier;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Couriers
        |--------------------------------------------------------------------------
        */

        $jne = Courier::updateOrCreate(
            ['code' => 'jne'],
            [
                'name'                  => 'JNE',
                'provider'              => 'Jalur Nugraha Ekakurir',
                'description'           => 'JNE shipping courier.',
                'supports_tracking'     => true,
                'supports_cod'          => false,
                'supports_insurance'    => true,
                'sort_order'            => 1,
                'is_active'             => true,
                'published_at'          => now(),
            ]
        );

        $jnt = Courier::updateOrCreate(
            ['code' => 'jnt'],
            [
                'name'                  => 'J&T Express',
                'provider'              => 'J&T Express',
                'description'           => 'J&T Express shipping courier.',
                'supports_tracking'     => true,
                'supports_cod'          => false,
                'supports_insurance'    => true,
                'sort_order'            => 2,
                'is_active'             => true,
                'published_at'          => now(),
            ]
        );

        $sicepat = Courier::updateOrCreate(
            ['code' => 'sicepat'],
            [
                'name'                  => 'SiCepat',
                'provider'              => 'SiCepat Ekspres',
                'description'           => 'SiCepat shipping courier.',
                'supports_tracking'     => true,
                'supports_cod'          => false,
                'supports_insurance'    => true,
                'sort_order'            => 3,
                'is_active'             => true,
                'published_at'          => now(),
            ]
        );

        $anteraja = Courier::updateOrCreate(
            ['code' => 'anteraja'],
            [
                'name'                  => 'AnterAja',
                'provider'              => 'AnterAja',
                'description'           => 'AnterAja shipping courier.',
                'supports_tracking'     => true,
                'supports_cod'          => true,
                'supports_insurance'    => true,
                'sort_order'            => 4,
                'is_active'             => true,
                'published_at'          => now(),
            ]
        );

        $pos = Courier::updateOrCreate(
            ['code' => 'pos'],
            [
                'name'                  => 'POS Indonesia',
                'provider'              => 'POS Indonesia',
                'description'           => 'POS Indonesia shipping courier.',
                'supports_tracking'     => true,
                'supports_cod'          => false,
                'supports_insurance'    => true,
                'sort_order'            => 5,
                'is_active'             => true,
                'published_at'          => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | JNE
        |--------------------------------------------------------------------------
        */

        $this->createShippingMethod(
            $jne,
            'REG',
            'JNE Regular',
            2,
            4,
            1
        );

        $this->createShippingMethod(
            $jne,
            'YES',
            'JNE Yakin Esok Sampai',
            1,
            1,
            2,
            true
        );

        $this->createShippingMethod(
            $jne,
            'OKE',
            'JNE Ongkos Kirim Ekonomis',
            3,
            7,
            3
        );

        /*
        |--------------------------------------------------------------------------
        | J&T
        |--------------------------------------------------------------------------
        */

        $this->createShippingMethod(
            $jnt,
            'EZ',
            'J&T EZ',
            2,
            4,
            1
        );

        /*
        |--------------------------------------------------------------------------
        | SiCepat
        |--------------------------------------------------------------------------
        */

        $this->createShippingMethod(
            $sicepat,
            'REG',
            'SiCepat Regular',
            2,
            4,
            1
        );

        $this->createShippingMethod(
            $sicepat,
            'BEST',
            'SiCepat BEST',
            1,
            1,
            2,
            true
        );

        /*
        |--------------------------------------------------------------------------
        | AnterAja
        |--------------------------------------------------------------------------
        */

        $this->createShippingMethod(
            $anteraja,
            'REG',
            'AnterAja Regular',
            2,
            4,
            1
        );

        $this->createShippingMethod(
            $anteraja,
            'NEXTDAY',
            'AnterAja Next Day',
            1,
            1,
            2,
            true
        );

        /*
        |--------------------------------------------------------------------------
        | POS Indonesia
        |--------------------------------------------------------------------------
        */

        $this->createShippingMethod(
            $pos,
            'KILAT',
            'POS Kilat Khusus',
            2,
            5,
            1
        );
    }

    private function createShippingMethod(
        Courier $courier,
        string $serviceCode,
        string $serviceName,
        int $minDays,
        int $maxDays,
        int $sortOrder,
        bool $featured = false
    ): void {
        ShippingMethod::updateOrCreate(
            [
                'courier_id'   => $courier->id,
                'service_code' => $serviceCode,
            ],
            [
                'service_name'             => $serviceName,
                'description'              => $serviceName,
                'estimated_min_days'       => $minDays,
                'estimated_max_days'       => $maxDays,
                'supports_tracking'        => true,
                'supports_cod'             => false,
                'supports_insurance'       => true,
                'base_cost'                => 0,
                'cost_per_kg'              => 0,
                'minimum_weight'           => 0,
                'maximum_weight'           => null,
                'free_shipping_threshold'  => null,
                'sla_hours'                => $maxDays * 24,
                'sort_order'               => $sortOrder,
                'is_featured'              => $featured,
                'is_active'                => true,
                'published_at'             => now(),
            ]
        );
    }
}