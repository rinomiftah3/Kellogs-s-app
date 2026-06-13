<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Core System Seeders
        |--------------------------------------------------------------------------
        */

        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Master Data Seeders
        |--------------------------------------------------------------------------
        */

        $this->call([
            CategorySeeder::class,
            ShippingSeeder::class,
            VoucherSeeder::class,
            PromotionSeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Development Seeders
        |--------------------------------------------------------------------------
        */

        if (
            App::environment([
                'local',
                'testing',
            ])
        ) {

            $this->call([

                CustomerSeeder::class,

                /*
                |--------------------------------------------------------------------------
                | Pending Audit
                |--------------------------------------------------------------------------
                |
                | ProductSeeder akan diaktifkan setelah
                | Product Catalog Factory dan Model
                | selesai diaudit.
                |
                */

                 ProductSeeder::class,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Logging
        |--------------------------------------------------------------------------
        */

        Log::info(
            'DatabaseSeeder completed successfully.',
            [
                'environment' => app()->environment(),
            ]
        );
    }
}