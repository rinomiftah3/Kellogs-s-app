<?php

namespace Tests\Feature;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private const API_PREFIX = '/api/v1/profile';

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = $this->actingAsSuperAdmin();

        $response = $this->getJson(
            self::API_PREFIX
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

    public function test_guest_cannot_view_profile(): void
    {
        $response = $this->getJson(
            self::API_PREFIX
        );

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = $this->actingAsSuperAdmin();

        $response = $this->putJson(
            self::API_PREFIX,
            [
                'name' => 'Updated User',
                'email' => 'updated@test.com',
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
                'email' => 'updated@test.com',
            ]
        );
    }

    public function test_profile_update_requires_valid_email(): void
    {
        $this->actingAsSuperAdmin();

        $response = $this->putJson(
            self::API_PREFIX,
            [
                'name' => 'Updated User',
                'email' => 'invalid-email',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_authenticated_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'OldPassword123!'
            ),
        ]);

        $this->actingAs(
            $user,
            'sanctum'
        );

        $response = $this->putJson(
            self::API_PREFIX . '/password',
            [
                'current_password' =>
                    'OldPassword123!',

                'password' =>
                    'NewPassword123!',

                'password_confirmation' =>
                    'NewPassword123!',
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertTrue(
            Hash::check(
                'NewPassword123!',
                $user->fresh()->password
            )
        );
    }

    public function test_password_update_requires_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'OldPassword123!'
            ),
        ]);

        $this->actingAs(
            $user,
            'sanctum'
        );

        $response = $this->putJson(
            self::API_PREFIX . '/password',
            [
                'current_password' =>
                    'WrongPassword',

                'password' =>
                    'NewPassword123!',

                'password_confirmation' =>
                    'NewPassword123!',
            ]
        );

        $response->assertStatus(422);
    }

    public function test_authenticated_user_can_upload_avatar(): void
    {
        Storage::fake(
            'public'
        );

        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX . '/avatar',
            [
                'avatar' =>
                    UploadedFile::fake()
                        ->image(
                            'avatar.jpg'
                        ),
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_avatar_must_be_image(): void
    {
        Storage::fake(
            'public'
        );

        $this->actingAsSuperAdmin();

        $response = $this->postJson(
            self::API_PREFIX . '/avatar',
            [
                'avatar' =>
                    UploadedFile::fake()
                        ->create(
                            'document.pdf',
                            100
                        ),
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'avatar',
            ]);
    }

    public function test_authenticated_user_can_delete_avatar(): void
    {
        Storage::fake(
            'public'
        );

        $user = User::factory()->create([
            'avatar' =>
                'avatars/avatar.jpg',
        ]);

        $this->actingAs(
            $user,
            'sanctum'
        );

        $response = $this->deleteJson(
            self::API_PREFIX . '/avatar'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_profile_endpoint_returns_expected_structure(): void
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
                    'id',
                    'name',
                    'email',
                ],
            ]);
    }

    public function test_profile_update_creates_activity_log(): void
    {
        $this->actingAsSuperAdmin();

        $this->putJson(
            self::API_PREFIX,
            [
                'name' => 'Updated User',
                'email' => 'updated@test.com',
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' => 'profile_updated',
            ]
        );
    }

    public function test_password_update_creates_activity_log(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'OldPassword123!'
            ),
        ]);

        $this->actingAs(
            $user,
            'sanctum'
        );

        $this->putJson(
            self::API_PREFIX . '/password',
            [
                'current_password' =>
                    'OldPassword123!',

                'password' =>
                    'NewPassword123!',

                'password_confirmation' =>
                    'NewPassword123!',
            ]
        );

        $this->assertDatabaseHas(
            'activity_log',
            [
                'event' =>
                    'password_updated',
            ]
        );
    }
}