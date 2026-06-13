<?php

namespace Tests;

use App\Models\User;

use Database\Seeders\DatabaseSeeder;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use Laravel\Sanctum\Sanctum;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * API Version Prefix
     */
    protected const API_PREFIX =
        '/api/v1';

    /**
     * Auto seed database
     */
    protected bool $seed = true;

    /**
     * Main Seeder
     */
    protected string $seeder =
        DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        app(
            PermissionRegistrar::class
        )->forgetCachedPermissions();
    }

    /*
    |--------------------------------------------------------------------------
    | User Helpers
    |--------------------------------------------------------------------------
    */

    protected function createUser(
        array $attributes = []
    ): User {

        return User::factory()
            ->create(
                $attributes
            );
    }

    protected function actingAsUser(
        User $user
    ): User {

        Sanctum::actingAs(
            $user,
            ['*']
        );

        return $user;
    }

    protected function actingAsRole(
        string $role
    ): User {

        $user =
            User::factory()->create();

        $user->assignRole(
            $role
        );

        Sanctum::actingAs(
            $user,
            ['*']
        );

        return $user;
    }

    protected function actingAsSuperAdmin(): User
    {
        return $this->actingAsRole(
            'Super Admin'
        );
    }

    protected function actingAsAdmin(): User
    {
        return $this->actingAsRole(
            'Admin'
        );
    }

    protected function actingAsStaff(): User
    {
        return $this->actingAsRole(
            'Staff'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    protected function createRole(
        string $name
    ): Role {

        return Role::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]);
    }

    protected function createPermission(
        string $name
    ): Permission {

        return Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]);
    }

    protected function assignPermission(
        User $user,
        string $permission
    ): void {

        $perm =
            $this->createPermission(
                $permission
            );

        $user->givePermissionTo(
            $perm
        );
    }

    /*
    |--------------------------------------------------------------------------
    | API Helpers
    |--------------------------------------------------------------------------
    */

    protected function api(
        string $uri
    ): string {

        return self::API_PREFIX .
            $uri;
    }

    protected function assertApiSuccess(
        mixed $response,
        int $status = 200
    ): void {

        $response

            ->assertStatus(
                $status
            )

            ->assertJson([
                'success' => true,
            ]);
    }

    protected function assertApiError(
        mixed $response,
        int $status
    ): void {

        $response

            ->assertStatus(
                $status
            )

            ->assertJson([
                'success' => false,
            ]);
    }

    protected function assertValidationError(
        mixed $response,
        array $fields
    ): void {

        $response

            ->assertStatus(422)

            ->assertJsonValidationErrors(
                $fields
            );
    }

    protected function assertForbidden(
        mixed $response
    ): void {

        $response
            ->assertStatus(403);
    }

    protected function assertUnauthorized(
        mixed $response
    ): void {

        $response
            ->assertStatus(401);
    }

    protected function assertNotFound(
        mixed $response
    ): void {

        $response
            ->assertStatus(404);
    }

    protected function assertPaginated(
        mixed $response
    ): void {

        $response->assertJsonStructure([

            'success',

            'message',

            'data' => [

                'items',

                'meta',
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Storage Helpers
    |--------------------------------------------------------------------------
    */

    protected function fakeStorage(): void
    {
        Storage::fake(
            'public'
        );
    }

    protected function fakeImage(
        string $name = 'image.jpg'
    ): UploadedFile {

        return UploadedFile::fake()
            ->image($name);
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Helpers
    |--------------------------------------------------------------------------
    */

    protected function clearApplicationCache(): void
    {
        Artisan::call(
            'optimize:clear'
        );
    }
}