<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private const API_PREFIX = '/api/v1/products';

    public function test_super_admin_can_view_products(): void
    {
        $this->actingAsSuperAdmin();

        Product::factory()
            ->count(3)
            ->create();

        $response = $this->getJson(
            self::API_PREFIX
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_guest_cannot_view_products(): void
    {
        $response = $this->getJson(
            self::API_PREFIX
        );

        $response->assertUnauthorized();
    }

    public function test_super_admin_can_view_product_detail(): void
    {
        $this->actingAsSuperAdmin();

        $product = Product::factory()
            ->create();

        $response = $this->getJson(
            self::API_PREFIX . "/{$product->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $product->id
            );
    }

    public function test_super_admin_can_create_product(): void
    {
        Storage::fake('public');

        $this->actingAsSuperAdmin();

        $category = Category::factory()
            ->create();

        $image = UploadedFile::fake()
            ->image('product.jpg');

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'category_id' => $category->id,
                'name' => 'Gaming Laptop',
                'description' => 'High End Laptop',
                'price' => 15000000,
                'stock' => 10,
                'is_active' => true,
                'image' => $image,
            ]
        );

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'products',
            [
                'name' => 'Gaming Laptop',
            ]
        );
    }

    public function test_product_name_is_required(): void
    {
        $this->actingAsSuperAdmin();

        $category = Category::factory()
            ->create();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'category_id' => $category->id,
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    public function test_product_requires_category(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX,
            [
                'name' => 'Test Product',
                'price' => 10000,
                'stock' => 5,
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'category_id',
            ]);
    }

    public function test_product_slug_is_generated(): void
    {
        $this->actingAsSuperAdmin();

        $category = Category::factory()
            ->create();

        $this->postJson(
            self::API_PREFIX,
            [
                'category_id' => $category->id,
                'name' => 'Gaming Laptop Pro',
                'price' => 10000,
                'stock' => 5,
            ]
        );

        $this->assertDatabaseHas(
            'products',
            [
                'slug' => 'gaming-laptop-pro',
            ]
        );
    }

    public function test_super_admin_can_update_product(): void
    {
        $this->actingAsSuperAdmin();

        $product = Product::factory()
            ->create();

        $response = $this->putJson(
            self::API_PREFIX . "/{$product->id}",
            [
                'category_id' => $product->category_id,
                'name' => 'Updated Product',
                'description' => 'Updated Description',
                'price' => 25000,
                'stock' => 20,
                'is_active' => true,
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'products',
            [
                'id' => $product->id,
                'name' => 'Updated Product',
            ]
        );
    }

    public function test_super_admin_can_delete_product(): void
    {
        $this->actingAsSuperAdmin();

        $product = Product::factory()
            ->create();

        $response = $this->deleteJson(
            self::API_PREFIX . "/{$product->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing(
            'products',
            [
                'id' => $product->id,
            ]
        );
    }

    public function test_products_can_be_filtered_by_category(): void
    {
        $this->actingAsSuperAdmin();

        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $categoryA->id,
        ]);

        Product::factory()->create([
            'category_id' => $categoryB->id,
        ]);

        $response = $this->getJson(
            self::API_PREFIX .
            '?category_id=' .
            $categoryA->id
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_products_can_be_filtered_by_active_status(): void
    {
        $this->actingAsSuperAdmin();

        Product::factory()->create([
            'is_active' => true,
        ]);

        Product::factory()->create([
            'is_active' => false,
        ]);

        $response = $this->getJson(
            self::API_PREFIX .
            '?is_active=1'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_product_creation_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $category = Category::factory()
            ->create();

        $this->postJson(
            self::API_PREFIX,
            [
                'category_id' => $category->id,
                'name' => 'Activity Product',
                'price' => 10000,
                'stock' => 10,
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'product_created',
            ]
        );
    }

    public function test_product_update_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $product = Product::factory()
            ->create();

        $this->putJson(
            self::API_PREFIX . "/{$product->id}",
            [
                'category_id' => $product->category_id,
                'name' => 'Updated Product',
                'price' => 20000,
                'stock' => 20,
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'product_updated',
            ]
        );
    }

    public function test_product_delete_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $product = Product::factory()
            ->create();

        $this->deleteJson(
            self::API_PREFIX . "/{$product->id}"
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'product_deleted',
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

    public function test_user_without_permission_cannot_access_product_module(): void
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