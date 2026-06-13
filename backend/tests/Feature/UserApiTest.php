<?php

namespace Tests\Feature;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Spatie\Permission\Models\Role;

use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    private const API_PREFIX = '/api/v1/users';

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'sanctum',
        ]);
    }

    public function test_super_admin_can_view_users(): void
    {
        $this->actingAsSuperAdmin();

        User::factory()->count(3)->create();

        $response = $this->getJson(
            self::API_PREFIX
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_guest_cannot_view_users(): void
    {
        $response = $this->getJson(
            self::API_PREFIX
        );

        $response->assertUnauthorized();
    }

    public function test_super_admin_can_view_user_detail(): void
    {
        $this->actingAsSuperAdmin();

        $user = User::factory()->create();

        $response = $this->getJson(
            self::API_PREFIX . "/{$user->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $user->id
            );
    }

    public function test_super_admin_can_create_user(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'John Doe',

                'email' => 'john@example.com',

                'password' => 'Password123!',

                'password_confirmation' => 'Password123!',

                'role' => 'staff',

                'is_active' => true,
            ]
        );

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'users',
            [
                'email' => 'john@example.com',
            ]
        );
    }

    public function test_create_user_requires_name(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'email' => 'john@example.com',
                'password' => 'Password123!',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    public function test_create_user_requires_email(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'John Doe',
                'password' => 'Password123!',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_create_user_requires_password(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_email_must_be_unique(): void
    {
        $this->actingAsSuperAdmin();

        User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'John Doe',

                'email' => 'john@example.com',

                'password' => 'Password123!',

                'password_confirmation' => 'Password123!',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_super_admin_can_update_user(): void
    {
        $this->actingAsSuperAdmin();

        $user = User::factory()->create();

        $response = $this->putJson(
            self::API_PREFIX . "/{$user->id}",
            [
                'name' => 'Updated User',

                'email' => $user->email,

                'role' => 'staff',

                'is_active' => true,
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'users',
            [
                'id' => $user->id,
                'name' => 'Updated User',
            ]
        );
    }

    public function test_super_admin_can_delete_user(): void
    {
        $this->actingAsSuperAdmin();

        $user = User::factory()->create();

        $response = $this->deleteJson(
            self::API_PREFIX . "/{$user->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing(
            'users',
            [
                'id' => $user->id,
            ]
        );
    }

    public function test_super_admin_cannot_delete_himself(): void
    {
        $user = $this->actingAsSuperAdmin();

        $response = $this->deleteJson(
            self::API_PREFIX . "/{$user->id}"
        );

        $response->assertForbidden();
    }

    public function test_super_admin_cannot_delete_another_super_admin(): void
    {
        $this->actingAsSuperAdmin();

        $target = User::factory()->create();

        $target->assignRole(
            'super-admin'
        );

        $response = $this->deleteJson(
            self::API_PREFIX . "/{$target->id}"
        );

        $response->assertForbidden();
    }

    public function test_user_creation_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'Audit User',

                'email' => 'audit@example.com',

                'password' => 'Password123!',

                'password_confirmation' => 'Password123!',

                'role' => 'staff',
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'user_created',
            ]
        );
    }

    public function test_user_update_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $user = User::factory()->create();

        $this->putJson(
            self::API_PREFIX . "/{$user->id}",
            [
                'name' => 'Updated',

                'email' => $user->email,

                'role' => 'staff',

                'is_active' => true,
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'user_updated',
            ]
        );
    }

    public function test_user_delete_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $user = User::factory()->create();

        $this->deleteJson(
            self::API_PREFIX . "/{$user->id}"
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'user_deleted',
            ]
        );
    }

    public function test_response_structure_is_valid(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson(
            self::API_PREFIX
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    public function test_user_without_permission_cannot_access_user_module(): void
    {
        $user = $this->createUser();

        $this->actingAs(
            $user,
            'sanctum'
        );

        $response = $this->getJson(
            self::API_PREFIX
        );

        $response->assertForbidden();
    }
}