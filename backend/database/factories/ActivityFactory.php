<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $event = fake()->randomElement([

            Activity::EVENT_CREATED,

            Activity::EVENT_UPDATED,

            Activity::EVENT_DELETED,

            Activity::EVENT_RESTORED,

            Activity::EVENT_LOGIN,

            Activity::EVENT_LOGOUT,

            Activity::EVENT_APPROVED,

            Activity::EVENT_REJECTED,

            Activity::EVENT_PUBLISHED,

            Activity::EVENT_CANCELLED,
        ]);

        return [

            /*
            |--------------------------------------------------------------------------
            | Activity Information
            |--------------------------------------------------------------------------
            */

            'log_name' => fake()->randomElement([

                'auth',

                'users',

                'roles',

                'categories',

                'products',

                'orders',

                'payments',

                'dashboard',

                'system',
            ]),

            'description' => fake()->sentence(),

            'event' => $event,

            /*
            |--------------------------------------------------------------------------
            | Subject
            |--------------------------------------------------------------------------
            */

            'subject_type' => null,

            'subject_id' => null,

            /*
            |--------------------------------------------------------------------------
            | Causer
            |--------------------------------------------------------------------------
            */

            'causer_type' => User::class,

            'causer_id' => User::factory(),

            /*
            |--------------------------------------------------------------------------
            | Activity Data
            |--------------------------------------------------------------------------
            */

            'attribute_changes' => [

                'old' => [],

                'attributes' => [],
            ],

            'properties' => [

                'ip' => fake()->ipv4(),

                'user_agent' => fake()->userAgent(),

                'source' => 'factory',
            ],

            /*
            |--------------------------------------------------------------------------
            | Batch
            |--------------------------------------------------------------------------
            */

            'batch_uuid' => null,

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            'created_at' => fake()->dateTimeBetween(
                '-30 days',
                'now'
            ),

            'updated_at' => now(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Event States
    |--------------------------------------------------------------------------
    */

    public function created(): static
    {
        return $this->state(fn () => [

            'event' => Activity::EVENT_CREATED,

            'description' => 'Resource created',
        ]);
    }

    public function updated(): static
    {
        return $this->state(fn () => [

            'event' => Activity::EVENT_UPDATED,

            'description' => 'Resource updated',
        ]);
    }

    public function deleted(): static
    {
        return $this->state(fn () => [

            'event' => Activity::EVENT_DELETED,

            'description' => 'Resource deleted',
        ]);
    }

    public function restored(): static
    {
        return $this->state(fn () => [

            'event' => Activity::EVENT_RESTORED,

            'description' => 'Resource restored',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [

            'event' => Activity::EVENT_APPROVED,

            'description' => 'Resource approved',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [

            'event' => Activity::EVENT_REJECTED,

            'description' => 'Resource rejected',
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [

            'event' => Activity::EVENT_PUBLISHED,

            'description' => 'Resource published',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [

            'event' => Activity::EVENT_CANCELLED,

            'description' => 'Resource cancelled',
        ]);
    }

    public function login(): static
    {
        return $this->state(fn () => [

            'log_name' => 'auth',

            'event' => Activity::EVENT_LOGIN,

            'description' => 'User login',
        ]);
    }

    public function logout(): static
    {
        return $this->state(fn () => [

            'log_name' => 'auth',

            'event' => Activity::EVENT_LOGOUT,

            'description' => 'User logout',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Subject States
    |--------------------------------------------------------------------------
    */

    public function forUser(
        ?User $user = null
    ): static {

        return $this->state(fn () => [

            'subject_type' => User::class,

            'subject_id' => $user?->id
                ?? User::factory(),
        ]);
    }

    public function forProduct(
        ?Product $product = null
    ): static {

        return $this->state(fn () => [

            'subject_type' => Product::class,

            'subject_id' => $product?->id
                ?? Product::factory(),
        ]);
    }

    public function forCategory(
        ?Category $category = null
    ): static {

        return $this->state(fn () => [

            'subject_type' => Category::class,

            'subject_id' => $category?->id
                ?? Category::factory(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard(): static
    {
        return $this->state(fn () => [

            'log_name' => 'dashboard',

            'event' => 'dashboard_viewed',

            'description' => 'Dashboard viewed',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    public function auth(): static
    {
        return $this->state(fn () => [

            'log_name' => 'auth',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | System Activity
    |--------------------------------------------------------------------------
    */

    public function system(): static
    {
        return $this->state(fn () => [

            'causer_type' => null,

            'causer_id' => null,

            'log_name' => 'system',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Batch Activity
    |--------------------------------------------------------------------------
    */

    public function batch(): static
    {
        return $this->state(fn () => [

            'batch_uuid' => (string) Str::uuid(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Recent
    |--------------------------------------------------------------------------
    */

    public function recent(): static
    {
        return $this->state(fn () => [

            'created_at' => now(),

            'updated_at' => now(),
        ]);
    }
}