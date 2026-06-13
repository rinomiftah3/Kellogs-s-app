<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Tests\TestCase;

class RoleApiTest extends TestCase
{
    use RefreshDatabase;

    private const API_PREFIX = '/api/v1/roles';

    public function test_super_admin_can_view_roles(): void
    {
        $this->actingAsSuperAdmin();

        Role::create([
            'name' => 'manager',
            'guard_name' => 'sanctum',
        ]);

        $response = $this->getJson(
            self::API_PREFIX
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_guest_cannot_view_roles(): void
    {
        $response = $this->getJson(
            self::API_PREFIX
        );

        $response->assertUnauthorized();
    }

    public function test_super_admin_can_create_role(): void
    {
        Permission::create([
            'name' => 'users.view',
            'guard_name' => 'sanctum',
        ]);

        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'manager',

                'permissions' => [
                    'users.view',
                ],
            ]
        );

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'roles',
            [
                'name' => 'manager',
            ]
        );
    }

    public function test_role_name_is_required(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            []
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    public function test_role_name_must_be_unique(): void
    {
        Role::create([
            'name' => 'manager',
            'guard_name' => 'sanctum',
        ]);

        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'manager',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    public function test_super_admin_can_update_role(): void
    {
        $role = Role::create([
            'name' => 'manager',
            'guard_name' => 'sanctum',
        ]);

        $this->actingAsSuperAdmin();

        $response = $this->putJson(
            self::API_PREFIX . "/{$role->id}",
            [
                'name' => 'super-manager',
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'roles',
            [
                'id' => $role->id,
                'name' => 'super-manager',
            ]
        );
    }

    public function test_super_admin_can_delete_role(): void
    {
        $role = Role::create([
            'name' => 'temporary-role',
            'guard_name' => 'sanctum',
        ]);

        $this->actingAsSuperAdmin();

        $response = $this->deleteJson(
            self::API_PREFIX . "/{$role->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing(
            'roles',
            [
                'id' => $role->id,
            ]
        );
    }

    public function test_system_role_cannot_be_deleted(): void
    {
        $role = Role::create([
            'name' => 'super-admin',
            'guard_name' => 'sanctum',
        ]);

        $this->actingAsSuperAdmin();

        $response = $this->deleteJson(
            self::API_PREFIX . "/{$role->id}"
        );

        $response->assertStatus(403);
    }

    public function test_role_index_returns_expected_structure(): void
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

    public function test_role_creation_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'auditor',
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'role_created',
            ]
        );
    }

    public function test_role_update_creates_activity_log(): void
    {
        $role = Role::create([
            'name' => 'manager',
            'guard_name' => 'sanctum',
        ]);

        $this->actingAsSuperAdmin();

        $this->putJson(
            self::API_PREFIX . "/{$role->id}",
            [
                'name' => 'updated-manager',
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'role_updated',
            ]
        );
    }

    public function test_role_delete_creates_activity_log(): void
    {
        $role = Role::create([
            'name' => 'temporary-role',
            'guard_name' => 'sanctum',
        ]);

        $this->actingAsSuperAdmin();

        $this->deleteJson(
            self::API_PREFIX . "/{$role->id}"
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'role_deleted',
            ]
        );
    }

    public function test_user_without_permission_cannot_manage_roles(): void
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