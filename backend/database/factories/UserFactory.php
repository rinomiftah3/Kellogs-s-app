<?php

namespace Database\Factories;

use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Eloquent\Factories\Factory;

use Spatie\Permission\Models\Role;

/**
 * User Factory
 *
 * Enterprise Ready
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Cached password hash.
     */
    protected static ?string $password = null;

    /*
    |--------------------------------------------------------------------------
    | Role Constants
    |--------------------------------------------------------------------------
    */

    private const ROLE_SUPER_ADMIN = 'Super Admin';

    private const ROLE_ADMIN = 'Admin';

    private const ROLE_STAFF = 'Staff';

    /*
    |--------------------------------------------------------------------------
    | Default State
    |--------------------------------------------------------------------------
    */

    public function definition(): array
    {
        return [

            'name' => fake()->name(),

            'email' => fake()
                ->unique()
                ->safeEmail(),

            'email_verified_at' => now(),

            'last_login_at' => fake()
                ->optional()
                ->dateTimeBetween(
                    '-30 days',
                    'now'
                ),

            'is_active' => true,

            'password' => static::$password
                ??= Hash::make('password'),

            'remember_token'
                => Str::random(10),

            'avatar' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Verification States
    |--------------------------------------------------------------------------
    */

    public function verified(): static
    {
        return $this->state(
            fn () => [

                'email_verified_at' => now(),
            ]
        );
    }

    public function unverified(): static
    {
        return $this->state(
            fn () => [

                'email_verified_at' => null,
            ]
        );
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
    | Password State
    |--------------------------------------------------------------------------
    */

    public function withPassword(
        string $password
    ): static {

        return $this->state(
            fn () => [

                'password'
                    => Hash::make(
                        $password
                    ),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Avatar State
    |--------------------------------------------------------------------------
    */

    public function withAvatar(
        ?string $path = null
    ): static {

        return $this->state(
            fn () => [

                'avatar'
                    => $path
                    ?? 'avatars/default.png',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Login State
    |--------------------------------------------------------------------------
    */

    public function recentlyLoggedIn(): static
    {
        return $this->state(
            fn () => [

                'last_login_at'
                    => now(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role States
    |--------------------------------------------------------------------------
    */

    public function superAdmin(): static
    {
        return $this->assignRole(
            self::ROLE_SUPER_ADMIN
        );
    }

    public function admin(): static
    {
        return $this->assignRole(
            self::ROLE_ADMIN
        );
    }

    public function staff(): static
    {
        return $this->assignRole(
            self::ROLE_STAFF
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function assignRole(
        string $role
    ): static {

        return $this->afterCreating(
            function (
                User $user
            ) use (
                $role
            ) {

                if (
                    Role::where(
                        'name',
                        $role
                    )->exists()
                ) {

                    $user->syncRoles([
                        $role,
                    ]);
                }
            }
        );
    }
}