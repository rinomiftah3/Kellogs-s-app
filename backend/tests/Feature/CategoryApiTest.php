<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    private const API_PREFIX = '/api/v1/categories';

    public function test_super_admin_can_view_categories(): void
    {
        $this->actingAsSuperAdmin();

        Category::factory()->count(3)->create();

        $response = $this->getJson(
            self::API_PREFIX
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_guest_cannot_view_categories(): void
    {
        $response = $this->getJson(
            self::API_PREFIX
        );

        $response->assertUnauthorized();
    }

    public function test_super_admin_can_view_category_detail(): void
    {
        $this->actingAsSuperAdmin();

        $category = Category::factory()->create();

        $response = $this->getJson(
            self::API_PREFIX . "/{$category->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $category->id
            );
    }

    public function test_super_admin_can_create_category(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'Elektronik',
                'description' => 'Kategori elektronik',
                'is_active' => true,
            ]
        );

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'categories',
            [
                'name' => 'Elektronik',
            ]
        );
    }

    public function test_category_name_is_required(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'description' => 'Test',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    public function test_category_name_must_be_unique(): void
    {
        Category::factory()->create([
            'name' => 'Elektronik',
        ]);

        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'Elektronik',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    public function test_category_slug_is_generated(): void
    {
        $this->actingAsSuperAdmin();

        $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'Kategori Baru',
                'description' => 'Testing',
            ]
        );

        $this->assertDatabaseHas(
            'categories',
            [
                'slug' => 'kategori-baru',
            ]
        );
    }

    public function test_super_admin_can_update_category(): void
    {
        $this->actingAsSuperAdmin();

        $category = Category::factory()->create();

        $response = $this->putJson(
            self::API_PREFIX . "/{$category->id}",
            [
                'name' => 'Updated Category',
                'description' => 'Updated Description',
                'is_active' => true,
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'categories',
            [
                'id' => $category->id,
                'name' => 'Updated Category',
            ]
        );
    }

    public function test_super_admin_can_delete_category(): void
    {
        $this->actingAsSuperAdmin();

        $category = Category::factory()->create();

        $response = $this->deleteJson(
            self::API_PREFIX . "/{$category->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing(
            'categories',
            [
                'id' => $category->id,
            ]
        );
    }

    public function test_category_cannot_be_deleted_when_has_products(): void
    {
        $this->actingAsSuperAdmin();

        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson(
            self::API_PREFIX . "/{$category->id}"
        );

        $response->assertStatus(409);
    }

    public function test_category_creation_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'Activity Category',
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'category_created',
            ]
        );
    }

    public function test_category_update_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $category = Category::factory()->create();

        $this->putJson(
            self::API_PREFIX . "/{$category->id}",
            [
                'name' => 'Updated Category',
                'description' => 'Updated',
                'is_active' => true,
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'category_updated',
            ]
        );
    }

    public function test_category_delete_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $category = Category::factory()->create();

        $this->deleteJson(
            self::API_PREFIX . "/{$category->id}"
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'category_deleted',
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

    public function test_user_without_permission_cannot_access_category_module(): void
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