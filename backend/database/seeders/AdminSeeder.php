<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminSeeder extends Seeder
{
    private const ROLE = 'Super Admin';

    private const DEFAULT_NAME =
        'Super Admin';

    private const DEFAULT_EMAIL =
        'admin@kelloggs.com';

    private const DEFAULT_PASSWORD =
        'password123';

    /**
     * Seed Super Admin.
     */
    public function run(): void
    {
        DB::transaction(function () {

            $email = env(
                'SUPER_ADMIN_EMAIL',
                self::DEFAULT_EMAIL
            );

            $password = env(
                'SUPER_ADMIN_PASSWORD',
                self::DEFAULT_PASSWORD
            );

            /*
            |--------------------------------------------------------------------------
            | Find Existing User (Including Soft Deleted)
            |--------------------------------------------------------------------------
            */

            $admin = User::withTrashed()
                ->where(
                    'email',
                    $email
                )
                ->first();

            if ($admin) {

                if ($admin->trashed()) {
                    $admin->restore();
                }

                $admin->update([

                    'name' => self::DEFAULT_NAME,

                    'password' => Hash::make(
                        $password
                    ),

                    'is_active' => true,

                    'email_verified_at' => now(),
                ]);
            } else {

                $admin = User::create([

                    'name' => self::DEFAULT_NAME,

                    'email' => $email,

                    'password' => Hash::make(
                        $password
                    ),

                    'is_active' => true,

                    'email_verified_at' => now(),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Assign Role
            |--------------------------------------------------------------------------
            */

            $admin->syncRoles([
                self::ROLE,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Logging
            |--------------------------------------------------------------------------
            */

            Log::info(
                'Super Admin seeded successfully.',
                [
                    'user_id' => $admin->id,
                    'email'   => $admin->email,
                    'role'    => self::ROLE,
                ]
            );
        });
    }
}