<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Parent Categories
        |--------------------------------------------------------------------------
        */

        $foods = Category::updateOrCreate(
            [
                'slug' => 'foods',
            ],
            [
                'parent_id'   => null,
                'name'        => 'Foods',
                'description' => 'Food products category.',
                'image'       => null,
                'sort_order'  => 1,
                'is_active'   => true,
            ]
        );

        $beverages = Category::updateOrCreate(
            [
                'slug' => 'beverages',
            ],
            [
                'parent_id'   => null,
                'name'        => 'Beverages',
                'description' => 'Beverage products category.',
                'image'       => null,
                'sort_order'  => 2,
                'is_active'   => true,
            ]
        );

        $healthyLiving = Category::updateOrCreate(
            [
                'slug' => 'healthy-living',
            ],
            [
                'parent_id'   => null,
                'name'        => 'Healthy Living',
                'description' => 'Healthy lifestyle and nutrition products.',
                'image'       => null,
                'sort_order'  => 3,
                'is_active'   => true,
            ]
        );

        $bundles = Category::updateOrCreate(
            [
                'slug' => 'bundles',
            ],
            [
                'parent_id'   => null,
                'name'        => 'Bundles',
                'description' => 'Bundle and package products.',
                'image'       => null,
                'sort_order'  => 4,
                'is_active'   => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Foods
        |--------------------------------------------------------------------------
        */

        Category::updateOrCreate(
            ['slug' => 'cereal'],
            [
                'parent_id'   => $foods->id,
                'name'        => 'Cereal',
                'description' => 'Breakfast cereal products.',
                'image'       => null,
                'sort_order'  => 1,
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'snack'],
            [
                'parent_id'   => $foods->id,
                'name'        => 'Snack',
                'description' => 'Snack products.',
                'image'       => null,
                'sort_order'  => 2,
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'cookies'],
            [
                'parent_id'   => $foods->id,
                'name'        => 'Cookies',
                'description' => 'Cookies and biscuits.',
                'image'       => null,
                'sort_order'  => 3,
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'granola'],
            [
                'parent_id'   => $foods->id,
                'name'        => 'Granola',
                'description' => 'Granola products.',
                'image'       => null,
                'sort_order'  => 4,
                'is_active'   => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Beverages
        |--------------------------------------------------------------------------
        */

        Category::updateOrCreate(
            ['slug' => 'coffee'],
            [
                'parent_id'   => $beverages->id,
                'name'        => 'Coffee',
                'description' => 'Coffee products.',
                'image'       => null,
                'sort_order'  => 1,
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'tea'],
            [
                'parent_id'   => $beverages->id,
                'name'        => 'Tea',
                'description' => 'Tea products.',
                'image'       => null,
                'sort_order'  => 2,
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'chocolate-drink'],
            [
                'parent_id'   => $beverages->id,
                'name'        => 'Chocolate Drink',
                'description' => 'Chocolate beverage products.',
                'image'       => null,
                'sort_order'  => 3,
                'is_active'   => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Healthy Living
        |--------------------------------------------------------------------------
        */

        Category::updateOrCreate(
            ['slug' => 'protein'],
            [
                'parent_id'   => $healthyLiving->id,
                'name'        => 'Protein',
                'description' => 'Protein nutrition products.',
                'image'       => null,
                'sort_order'  => 1,
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'diet-food'],
            [
                'parent_id'   => $healthyLiving->id,
                'name'        => 'Diet Food',
                'description' => 'Diet and low-calorie products.',
                'image'       => null,
                'sort_order'  => 2,
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'organic-food'],
            [
                'parent_id'   => $healthyLiving->id,
                'name'        => 'Organic Food',
                'description' => 'Organic food products.',
                'image'       => null,
                'sort_order'  => 3,
                'is_active'   => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Bundles
        |--------------------------------------------------------------------------
        */

        Category::updateOrCreate(
            ['slug' => 'family-pack'],
            [
                'parent_id'   => $bundles->id,
                'name'        => 'Family Pack',
                'description' => 'Family package products.',
                'image'       => null,
                'sort_order'  => 1,
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'gift-pack'],
            [
                'parent_id'   => $bundles->id,
                'name'        => 'Gift Pack',
                'description' => 'Gift package products.',
                'image'       => null,
                'sort_order'  => 2,
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'monthly-package'],
            [
                'parent_id'   => $bundles->id,
                'name'        => 'Monthly Package',
                'description' => 'Monthly subscription packages.',
                'image'       => null,
                'sort_order'  => 3,
                'is_active'   => true,
            ]
        );
    }
}