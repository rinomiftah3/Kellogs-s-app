<?php

namespace Tests\Feature;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Spatie\Activitylog\Models\Activity;

use Tests\TestCase;

class ActivityLogApiTest extends TestCase
{
    use RefreshDatabase;

    protected const API_PREFIX =
        '/api/v1/activity-logs';

    public function test_super_admin_can_view_activity_logs(): void
    {
        $this->actingAsSuperAdmin();

        Activity::factory()
            ->count(5)
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

    public function test_guest_cannot_access_activity_logs(): void
    {
        $response = $this->getJson(
            self::API_PREFIX
        );

        $response->assertUnauthorized();
    }

    public function test_activity_logs_are_paginated(): void
    {
        $this->actingAsSuperAdmin();

        Activity::factory()
            ->count(30)
            ->create();

        $response = $this->getJson(
            self::API_PREFIX .
            '?per_page=10'
        );

        $response
            ->assertOk()

            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'items',
                    'meta',
                ],
            ]);
    }

    public function test_can_filter_activity_logs_by_event(): void
    {
        $this->actingAsSuperAdmin();

        Activity::factory()->create([
            'event' => 'user_created',
        ]);

        Activity::factory()->create([
            'event' => 'user_deleted',
        ]);

        $response = $this->getJson(
            self::API_PREFIX .
            '?event=user_created'
        );

        $response->assertOk();
    }

    public function test_can_filter_activity_logs_by_log_name(): void
    {
        $this->actingAsSuperAdmin();

        Activity::factory()->create([
            'log_name' => 'auth',
        ]);

        Activity::factory()->create([
            'log_name' => 'products',
        ]);

        $response = $this->getJson(
            self::API_PREFIX .
            '?log_name=auth'
        );

        $response->assertOk();
    }

    public function test_can_search_activity_logs(): void
    {
        $this->actingAsSuperAdmin();

        Activity::factory()->create([
            'description' =>
                'Product created',
        ]);

        $response = $this->getJson(
            self::API_PREFIX .
            '?search=Product'
        );

        $response->assertOk();
    }

    public function test_can_filter_by_causer(): void
    {
        $this->actingAsSuperAdmin();

        $user = User::factory()->create();

        Activity::factory()->create([
            'causer_type' => User::class,
            'causer_id' => $user->id,
        ]);

        $response = $this->getJson(
            self::API_PREFIX .
            '?causer_id=' .
            $user->id
        );

        $response->assertOk();
    }

    public function test_can_filter_by_subject(): void
    {
        $this->actingAsSuperAdmin();

        $activity = Activity::factory()
            ->create();

        $response = $this->getJson(
            self::API_PREFIX .
            '?subject_id=' .
            $activity->subject_id
        );

        $response->assertOk();
    }

    public function test_can_filter_by_subject_type(): void
    {
        $this->actingAsSuperAdmin();

        $activity = Activity::factory()
            ->create();

        $response = $this->getJson(
            self::API_PREFIX .
            '?subject_type=' .
            urlencode(
                $activity->subject_type
            )
        );

        $response->assertOk();
    }

    public function test_can_filter_by_date_range(): void
    {
        $this->actingAsSuperAdmin();

        Activity::factory()->create([
            'created_at' =>
                now()->subDays(2),
        ]);

        Activity::factory()->create([
            'created_at' =>
                now(),
        ]);

        $response = $this->getJson(
            self::API_PREFIX .
            '?date_from=' .
            now()
                ->subDay()
                ->toDateString()
        );

        $response->assertOk();
    }

    public function test_super_admin_can_view_activity_log_detail(): void
    {
        $this->actingAsSuperAdmin();

        $activity = Activity::factory()
            ->create();

        $response = $this->getJson(
            self::API_PREFIX .
            "/{$activity->id}"
        );

        $response
            ->assertOk()

            ->assertJsonPath(
                'data.id',
                $activity->id
            );
    }

    public function test_activity_log_detail_returns_expected_structure(): void
    {
        $this->actingAsSuperAdmin();

        $activity = Activity::factory()
            ->create();

        $response = $this->getJson(
            self::API_PREFIX .
            "/{$activity->id}"
        );

        $response
            ->assertOk()

            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    public function test_activity_log_index_returns_expected_structure(): void
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

                'data' => [

                    'items',

                    'meta' => [

                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                    ],
                ],
            ]);
    }

    public function test_invalid_activity_log_returns_404(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->getJson(
            self::API_PREFIX .
            '/999999'
        );

        $response->assertNotFound();
    }

    public function test_user_without_permission_cannot_access_activity_logs(): void
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