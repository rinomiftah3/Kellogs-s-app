<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Product Factory
 *
 * Enterprise Ready
 *
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var class-string<Product>
     */
    protected $model = Product::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $name = fake()->unique()->words(
            rand(2, 4),
            true
        );

        return [

            'category_id'
                => Category::factory(),

            'name'
                => ucwords($name),

            'slug'
                => Str::slug($name),

            'short_description'
                => fake()->sentence(),

            'description'
                => fake()->paragraphs(
                    3,
                    true
                ),

            'thumbnail'
                => null,

            'status'
                => Product::STATUS_DRAFT,

            'is_featured'
                => false,

            'is_active'
                => true,

            'published_at'
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
                    => Product::STATUS_DRAFT,

                'published_at'
                    => null,
            ]
        );
    }

    public function active(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Product::STATUS_ACTIVE,

                'is_active'
                    => true,

                'published_at'
                    => now(),
            ]
        );
    }

    public function inactive(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Product::STATUS_INACTIVE,

                'is_active'
                    => false,
            ]
        );
    }

    public function archived(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Product::STATUS_ARCHIVED,

                'is_active'
                    => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Featured States
    |--------------------------------------------------------------------------
    */

    public function featured(): static
    {
        return $this->state(
            fn () => [

                'is_featured'
                    => true,
            ]
        );
    }

    public function notFeatured(): static
    {
        return $this->state(
            fn () => [

                'is_featured'
                    => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Publish States
    |--------------------------------------------------------------------------
    */

    public function published(): static
    {
        return $this->state(
            fn () => [

                'status'
                    => Product::STATUS_ACTIVE,

                'is_active'
                    => true,

                'published_at'
                    => now(),
            ]
        );
    }

    public function unpublished(): static
    {
        return $this->state(
            fn () => [

                'published_at'
                    => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Media States
    |--------------------------------------------------------------------------
    */

    public function withThumbnail(
        ?string $path = null
    ): static {

        return $this->state(
            fn () => [

                'thumbnail'
                    => $path
                    ?? 'products/default.jpg',
            ]
        );
    }

    public function withoutThumbnail(): static
    {
        return $this->state(
            fn () => [

                'thumbnail'
                    => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Category State
    |--------------------------------------------------------------------------
    */

    public function category(
        Category $category
    ): static {

        return $this->state(
            fn () => [

                'category_id'
                    => $category->id,
            ]
        );
    }
}