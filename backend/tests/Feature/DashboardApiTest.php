<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Spatie\Activitylog\Models\Activity;

use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    private const API_ENDPOINT =
        '/api/v1/dashboard';

    public function test_super_admin_can_access_dashboard(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response->assertUnauthorized();
    }

    public function test_dashboard_returns_statistics(): void
    {
        $this->actingAsSuperAdmin();

        User::factory()->count(5)->create();

        Category::factory()->count(3)->create();

        Product::factory()->count(10)->create();

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response
            ->assertOk()

            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'statistics',
                ],
            ]);
    }

    public function test_dashboard_returns_growth_metrics(): void
    {
        $this->actingAsSuperAdmin();

        User::factory()->create([
            'created_at' => now(),
        ]);

        Product::factory()->create([
            'created_at' => now(),
        ]);

        Activity::create([
            'log_name' => 'default',
            'description' => 'Test',
            'event' => 'created',
            'created_at' => now(),
        ]);

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response
            ->assertOk()

            ->assertJsonStructure([
                'data' => [
                    'growth',
                ],
            ]);
    }

    public function test_dashboard_returns_chart_data(): void
    {
        $this->actingAsSuperAdmin();

        Product::factory()->count(5)->create();

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response
            ->assertOk()

            ->assertJsonStructure([
                'data' => [
                    'charts' => [
                        'activity_chart',
                        'user_chart',
                        'product_chart',
                    ],
                ],
            ]);
    }

    public function test_dashboard_returns_low_stock_products(): void
    {
        $this->actingAsSuperAdmin();

        Product::factory()->create([
            'stock' => 5,
        ]);

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response
            ->assertOk()

            ->assertJsonStructure([
                'data' => [
                    'low_stock_products',
                ],
            ]);
    }

    public function test_dashboard_returns_recent_activities(): void
    {
        $this->actingAsSuperAdmin();

        activity()
            ->event('dashboard_test')
            ->log('Dashboard Test');

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response
            ->assertOk()

            ->assertJsonStructure([
                'data' => [
                    'recent_activities',
                ],
            ]);
    }

    public function test_dashboard_statistics_contains_expected_keys(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response
            ->assertOk()

            ->assertJsonStructure([
                'data' => [
                    'statistics' => [
                        'users',
                        'categories',
                        'products',
                        'activity_logs',
                    ],
                ],
            ]);
    }

    public function test_dashboard_growth_contains_expected_keys(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response
            ->assertOk()

            ->assertJsonStructure([
                'data' => [
                    'growth' => [
                        'new_users_today',
                        'new_products_today',
                        'new_activities_today',
                    ],
                ],
            ]);
    }

    public function test_dashboard_response_structure_is_valid(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response
            ->assertOk()

            ->assertJsonStructure([

                'success',

                'message',

                'data' => [

                    'statistics',

                    'growth',

                    'charts' => [

                        'activity_chart',

                        'user_chart',

                        'product_chart',
                    ],

                    'low_stock_products',

                    'recent_activities',
                ],
            ]);
    }

    public function test_dashboard_can_be_loaded_multiple_times(): void
    {
        $this->actingAsSuperAdmin();

        $first = $this->getJson(
            self::API_ENDPOINT
        );

        $second = $this->getJson(
            self::API_ENDPOINT
        );

        $first->assertOk();
        $second->assertOk();
    }

    public function test_user_without_dashboard_permission_cannot_access_dashboard(): void
    {
        $user = $this->createUser();

        $this->actingAs(
            $user,
            'sanctum'
        );

        $response = $this->getJson(
            self::API_ENDPOINT
        );

        $response->assertForbidden();
    }
}