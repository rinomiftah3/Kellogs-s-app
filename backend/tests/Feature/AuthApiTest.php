<?php

namespace Tests\Feature;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private const API_PREFIX = '/api/v1';

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson(
            self::API_PREFIX . '/login',
            [
                'email' => 'admin@test.com',
                'password' => 'Password123!',
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas(
            'personal_access_tokens',
            [
                'tokenable_id' => $user->id,
            ]
        );
    }

    public function test_login_fails_with_invalid_email(): void
    {
        User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson(
            self::API_PREFIX . '/login',
            [
                'email' => 'wrong@test.com',
                'password' => 'Password123!',
            ]
        );

        $response
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson(
            self::API_PREFIX . '/login',
            [
                'email' => 'admin@test.com',
                'password' => 'WrongPassword123!',
            ]
        );

        $response
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_login_requires_email(): void
    {
        $response = $this->postJson(
            self::API_PREFIX . '/login',
            [
                'password' => 'Password123!',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_login_requires_password(): void
    {
        $response = $this->postJson(
            self::API_PREFIX . '/login',
            [
                'email' => 'admin@test.com',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = $this->actingAsSuperAdmin();

        $response = $this->getJson(
            self::API_PREFIX . '/me'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath(
                'data.id',
                $user->id
            );
    }

    public function test_guest_cannot_access_me_endpoint(): void
    {
        $response = $this->getJson(
            self::API_PREFIX . '/me'
        );

        $response
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX . '/logout'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_authenticated_user_can_logout_all_devices(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX . '/logout-all'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_login_creates_activity_log(): void
    {
        User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->postJson(
            self::API_PREFIX . '/login',
            [
                'email' => 'admin@test.com',
                'password' => 'Password123!',
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'login',
            ]
        );
    }

    public function test_logout_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $this->postJson(
            self::API_PREFIX . '/logout'
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'logout',
            ]
        );
    }

    public function test_me_endpoint_returns_expected_structure(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson(
            self::API_PREFIX . '/me'
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
            ]);
    }
}