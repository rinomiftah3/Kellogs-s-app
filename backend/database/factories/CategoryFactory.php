<?php

namespace Database\Factories;

use App\Models\Category;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Category Factory
 *
 * Enterprise Ready
 *
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var class-string<Category>
     */
    protected $model = Category::class;

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        $name = fake()->unique()->words(
            rand(1, 3),
            true
        );

        return [

            'parent_id' => null,

            'name' => ucfirst(
                $name
            ),

            'slug' => Str::slug(
                $name
            ),

            'description' => fake()
                ->optional()
                ->sentence(),

            'image' => null,

            'sort_order' => fake()
                ->numberBetween(
                    0,
                    100
                ),

            'is_active' => true,
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
    | Hierarchy States
    |--------------------------------------------------------------------------
    */

    public function parent(): static
    {
        return $this->state(
            fn () => [

                'parent_id' => null,
            ]
        );
    }

    public function child(
        ?Category $parent = null
    ): static {

        return $this->state(
            fn () => [

                'parent_id'
                    => $parent?->id
                    ?? Category::factory(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Image States
    |--------------------------------------------------------------------------
    */

    public function withImage(
        ?string $path = null
    ): static {

        return $this->state(
            fn () => [

                'image'
                    => $path
                    ?? 'categories/default.jpg',
            ]
        );
    }

    public function withoutImage(): static
    {
        return $this->state(
            fn () => [

                'image' => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ordering States
    |--------------------------------------------------------------------------
    */

    public function ordered(
        int $order
    ): static {

        return $this->state(
            fn () => [

                'sort_order'
                    => $order,
            ]
        );
    }
}