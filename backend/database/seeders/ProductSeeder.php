<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductSku;
use App\Models\ProductSkuValue;
use App\Models\ProductReview;
use App\Models\ProductReviewImage;
use App\Models\Inventory;
use App\Models\CustomerProfile;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Required Data
        |--------------------------------------------------------------------------
        */

        $customers = CustomerProfile::all();

        if ($customers->isEmpty()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $cerealCategory = Category::where(
            'slug',
            'cereal'
        )->first();

        $granolaCategory = Category::where(
            'slug',
            'granola'
        )->first();

        $cookiesCategory = Category::where(
            'slug',
            'cookies'
        )->first();

        $proteinCategory = Category::where(
            'slug',
            'protein'
        )->first();

        $coffeeCategory = Category::where(
            'slug',
            'coffee'
        )->first();

        $teaCategory = Category::where(
            'slug',
            'tea'
        )->first();

        /*
        |--------------------------------------------------------------------------
        | Product Catalog
        |--------------------------------------------------------------------------
        */

        $products = [

            [
                'category_id' => $cerealCategory?->id,

                'name' => 'Kellogg\'s Corn Flakes',

                'slug' => 'kelloggs-corn-flakes',

                'short_description'
                    => 'Classic crispy corn cereal.',

                'description'
                    => 'Original Kellogg\'s Corn Flakes made from selected corn grains and enriched with vitamins and minerals.',

                'thumbnail'
                    => 'products/corn-flakes/main.jpg',

                'status'
                    => Product::STATUS_ACTIVE,

                'is_featured'
                    => true,

                'price_small'
                    => 28000,

                'price_medium'
                    => 45000,

                'price_large'
                    => 69000,

                'sizes' => [
                    '250g',
                    '500g',
                    '1kg',
                ],

                'flavors' => [
                    'Original',
                ],
            ],

            [
                'category_id' => $cerealCategory?->id,

                'name' => 'Kellogg\'s Frosties',

                'slug' => 'kelloggs-frosties',

                'short_description'
                    => 'Sweet crunchy breakfast cereal.',

                'description'
                    => 'Crunchy frosted cereal with delicious sweet taste suitable for breakfast and snacks.',

                'thumbnail'
                    => 'products/frosties/main.jpg',

                'status'
                    => Product::STATUS_ACTIVE,

                'is_featured'
                    => true,

                'price_small'
                    => 32000,

                'price_medium'
                    => 52000,

                'price_large'
                    => 79000,

                'sizes' => [
                    '250g',
                    '500g',
                    '1kg',
                ],

                'flavors' => [
                    'Original',
                ],
            ],

            [
                'category_id' => $granolaCategory?->id,

                'name' => 'Kellogg\'s Granola Honey Almond',

                'slug' => 'kelloggs-granola-honey-almond',

                'short_description'
                    => 'Premium granola with honey and almond.',

                'description'
                    => 'Healthy granola made from whole grain oats combined with honey and roasted almonds.',

                'thumbnail'
                    => 'products/granola-honey-almond/main.jpg',

                'status'
                    => Product::STATUS_ACTIVE,

                'is_featured'
                    => true,

                'price_small'
                    => 42000,

                'price_medium'
                    => 68000,

                'price_large'
                    => 98000,

                'sizes' => [
                    '300g',
                    '600g',
                    '1kg',
                ],

                'flavors' => [
                    'Honey Almond',
                ],
            ],

            [
                'category_id' => $cookiesCategory?->id,

                'name' => 'Kellogg\'s Choco Cookies',

                'slug' => 'kelloggs-choco-cookies',

                'short_description'
                    => 'Chocolate cookies snack.',

                'description'
                    => 'Crunchy cookies with premium chocolate chips suitable for all ages.',

                'thumbnail'
                    => 'products/choco-cookies/main.jpg',

                'status'
                    => Product::STATUS_ACTIVE,

                'is_featured'
                    => false,

                'price_small'
                    => 22000,

                'price_medium'
                    => 38000,

                'price_large'
                    => 56000,

                'sizes' => [
                    '150g',
                    '300g',
                    '600g',
                ],

                'flavors' => [
                    'Chocolate',
                ],
            ],

            [
                'category_id' => $proteinCategory?->id,

                'name' => 'Kellogg\'s Protein Bar',

                'slug' => 'kelloggs-protein-bar',

                'short_description'
                    => 'High protein healthy snack.',

                'description'
                    => 'Protein snack bar enriched with whey protein and fiber.',

                'thumbnail'
                    => 'products/protein-bar/main.jpg',

                'status'
                    => Product::STATUS_ACTIVE,

                'is_featured'
                    => true,

                'price_small'
                    => 18000,

                'price_medium'
                    => 34000,

                'price_large'
                    => 62000,

                'sizes' => [
                    '3pcs',
                    '6pcs',
                    '12pcs',
                ],

                'flavors' => [
                    'Chocolate',
                    'Vanilla',
                ],
            ],

        ];
        /*
        |--------------------------------------------------------------------------
        | Create Products
        |--------------------------------------------------------------------------
        */

        foreach ($products as $productData) {

            $product = Product::updateOrCreate(

                [
                    'slug' => $productData['slug'],
                ],

                [
                    'category_id'
                        => $productData['category_id'],

                    'name'
                        => $productData['name'],

                    'short_description'
                        => $productData['short_description'],

                    'description'
                        => $productData['description'],

                    'thumbnail'
                        => $productData['thumbnail'],

                    'status'
                        => $productData['status'],

                    'is_featured'
                        => $productData['is_featured'],

                    'is_active'
                        => true,

                    'published_at'
                        => now(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Product Images
            |--------------------------------------------------------------------------
            */

            ProductImage::updateOrCreate(

                [
                    'product_id' => $product->id,
                    'sort_order' => 1,
                ],

                [
                    'image_url'
                        => $productData['thumbnail'],

                    'alt_text'
                        => $product->name,

                    'is_primary'
                        => true,

                    'is_active'
                        => true,
                ]
            );

            ProductImage::updateOrCreate(

                [
                    'product_id' => $product->id,
                    'sort_order' => 2,
                ],

                [
                    'image_url'
                        => str_replace(
                            'main.jpg',
                            'gallery-1.jpg',
                            $productData['thumbnail']
                        ),

                    'alt_text'
                        => $product->name . ' Gallery 1',

                    'is_primary'
                        => false,

                    'is_active'
                        => true,
                ]
            );

            ProductImage::updateOrCreate(

                [
                    'product_id' => $product->id,
                    'sort_order' => 3,
                ],

                [
                    'image_url'
                        => str_replace(
                            'main.jpg',
                            'gallery-2.jpg',
                            $productData['thumbnail']
                        ),

                    'alt_text'
                        => $product->name . ' Gallery 2',

                    'is_primary'
                        => false,

                    'is_active'
                        => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Product Options
            |--------------------------------------------------------------------------
            */

            $sizeOption = ProductOption::updateOrCreate(

                [
                    'product_id' => $product->id,
                    'code' => 'SIZE',
                ],

                [
                    'name' => 'Size',

                    'sort_order' => 1,

                    'is_required' => true,

                    'is_active' => true,
                ]
            );

            $flavorOption = ProductOption::updateOrCreate(

                [
                    'product_id' => $product->id,
                    'code' => 'FLAVOR',
                ],

                [
                    'name' => 'Flavor',

                    'sort_order' => 2,

                    'is_required' => true,

                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Size Values
            |--------------------------------------------------------------------------
            */

            $sizeValues = [];

            foreach (
                $productData['sizes']
                as $index => $size
            ) {

                $sizeValues[] =
                    ProductOptionValue::updateOrCreate(

                        [
                            'product_option_id'
                                => $sizeOption->id,

                            'value'
                                => $size,
                        ],

                        [
                            'code'
                                => strtoupper(
                                    str_replace(
                                        [' ', '.'],
                                        '',
                                        $size
                                    )
                                ),

                            'sort_order'
                                => $index + 1,

                            'is_active'
                                => true,
                        ]
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Flavor Values
            |--------------------------------------------------------------------------
            */

            $flavorValues = [];

            foreach (
                $productData['flavors']
                as $index => $flavor
            ) {

                $flavorValues[] =
                    ProductOptionValue::updateOrCreate(

                        [
                            'product_option_id'
                                => $flavorOption->id,

                            'value'
                                => $flavor,
                        ],

                        [
                            'code'
                                => strtoupper(
                                    Str::slug(
                                        $flavor,
                                        ''
                                    )
                                ),

                            'sort_order'
                                => $index + 1,

                            'is_active'
                                => true,
                        ]
                    );
            }
                /*
            |--------------------------------------------------------------------------
            | SKU + Inventory
            |--------------------------------------------------------------------------
            */

            foreach (
                $sizeValues as $sizeIndex => $sizeValue
            ) {

                $basePrice = match ($sizeIndex) {

                    0 => $productData['price_small'],

                    1 => $productData['price_medium'],

                    default => $productData['price_large'],
                };

                foreach (
                    $flavorValues as $flavorValue
                ) {

                    $skuCode = strtoupper(

                        substr(
                            Str::slug(
                                $product->slug,
                                ''
                            ),
                            0,
                            6
                        )

                    )

                    . '-'

                    . strtoupper(
                        Str::slug(
                            $flavorValue->value,
                            ''
                        )
                    )

                    . '-'

                    . strtoupper(
                        Str::slug(
                            $sizeValue->value,
                            ''
                        )
                    );

                    $sku = ProductSku::updateOrCreate(

                        [
                            'sku' => $skuCode,
                        ],

                        [
                            'product_id'
                                => $product->id,

                            'barcode'
                                => fake()->unique()
                                    ->ean13(),

                            'price'
                                => $basePrice,

                            'compare_at_price'
                                => $basePrice + 10000,

                            'cost_price'
                                => round(
                                    $basePrice * 0.6
                                ),

                            'weight'
                                => match ($sizeIndex) {

                                    0 => 0.25,

                                    1 => 0.50,

                                    default => 1.00,
                                },

                            'length' => 10,
                            'width' => 10,
                            'height' => 10,

                            'minimum_order_quantity'
                                => 1,

                            'maximum_order_quantity'
                                => 20,

                            'is_default'
                                => (
                                    $sizeIndex === 0
                                    &&
                                    $flavorValue->id
                                    === $flavorValues[0]->id
                                ),

                            'status'
                                => ProductSku::STATUS_ACTIVE,

                            'is_active'
                                => true,

                            'published_at'
                                => now(),
                        ]
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SKU Values
                    |--------------------------------------------------------------------------
                    */

                    ProductSkuValue::updateOrCreate(

                        [
                            'product_sku_id'
                                => $sku->id,

                            'product_option_value_id'
                                => $sizeValue->id,
                        ]
                    );

                    ProductSkuValue::updateOrCreate(

                        [
                            'product_sku_id'
                                => $sku->id,

                            'product_option_value_id'
                                => $flavorValue->id,
                        ]
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Inventory
                    |--------------------------------------------------------------------------
                    */

                    $stock = rand(
                        50,
                        200
                    );

                    Inventory::updateOrCreate(

                        [
                            'product_sku_id'
                                => $sku->id,
                        ],

                        [
                            'current_stock'
                                => $stock,

                            'reserved_stock'
                                => 0,

                            'available_stock'
                                => $stock,

                            'minimum_stock'
                                => 10,

                            'maximum_stock'
                                => 500,

                            'reorder_point'
                                => 20,

                            'allow_backorder'
                                => false,

                            'is_active'
                                => true,
                        ]
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Product Reviews
            |--------------------------------------------------------------------------
            */

            $reviewCustomers = $customers
                ->random(
                    min(
                        3,
                        $customers->count()
                    )
                );

                    foreach (
                $reviewCustomers
                as $customer
            ) {

                $review = ProductReview::updateOrCreate(

                    [
                        'product_id'
                            => $product->id,

                        'customer_profile_id'
                            => $customer->id,
                    ],

                    [
                        'rating'
                            => rand(4, 5),

                        'title'
                            => fake()->sentence(4),

                        'review'
                            => fake()->paragraph(),

                        'is_verified_purchase'
                            => true,

                        'status'
                            => ProductReview::STATUS_APPROVED,

                        'helpful_count'
                            => rand(0, 25),
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Review Images
                |--------------------------------------------------------------------------
                */

                if (
                    rand(1, 100) <= 60
                ) {

                    ProductReviewImage::updateOrCreate(

                        [
                            'product_review_id'
                                => $review->id,

                            'sort_order'
                                => 1,
                        ],

                        [
                            'image_url'
                                => 'reviews/product-review-1.jpg',

                            'alt_text'
                                => $product->name
                                . ' Review Image',

                            'is_active'
                                => true,
                        ]
                    );
                }

                if (
                    rand(1, 100) <= 30
                ) {

                    ProductReviewImage::updateOrCreate(

                        [
                            'product_review_id'
                                => $review->id,

                            'sort_order'
                                => 2,
                        ],

                        [
                            'image_url'
                                => 'reviews/product-review-2.jpg',

                            'alt_text'
                                => $product->name
                                . ' Review Image 2',

                            'is_active'
                                => true,
                        ]
                    );
                }
            }
        }
    }
}