<?php

namespace App\Services;

use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Login user.
     */
    public function login(
        array $credentials,
        string $ip,
        ?string $userAgent = null
    ): array {

        return DB::transaction(
            function () use (
                $credentials,
                $ip,
                $userAgent
            ) {

                $user = User::query()

                    ->with([
                        'roles.permissions',
                    ])

                    ->where(
                        'email',
                        $credentials['email']
                    )

                    ->first();

                if (
                    !$user ||
                    !Hash::check(
                        $credentials['password'],
                        $user->password
                    )
                ) {

                    throw ValidationException::withMessages([
                        'email' => [
                            'Email atau password salah.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Active User Validation
                |--------------------------------------------------------------------------
                */

                if (
                    isset($user->is_active) &&
                    !$user->is_active
                ) {

                    throw ValidationException::withMessages([
                        'email' => [
                            'Akun tidak aktif.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Revoke Existing Tokens
                |--------------------------------------------------------------------------
                */

                $user
                    ->tokens()
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | Create Token
                |--------------------------------------------------------------------------
                */

                $token = $user
                    ->createToken(
                        'auth_token'
                    )
                    ->plainTextToken;

                /*
                |--------------------------------------------------------------------------
                | Update Last Login
                |--------------------------------------------------------------------------
                */

                $user->update([
                    'last_login_at' => now(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Activity Log
                |--------------------------------------------------------------------------
                */

                activity()

                    ->causedBy(
                        $user
                    )

                    ->performedOn(
                        $user
                    )

                    ->event(
                        'login'
                    )

                    ->withProperties([
                        'ip' =>
                            $ip,

                        'user_agent' =>
                            $userAgent,
                    ])

                    ->log(
                        'User login'
                    );

                return [

                    'user' =>
                        $this->userPayload(
                            $user->fresh([
                                'roles.permissions',
                            ])
                        ),

                    'token' =>
                        $token,
                ];
            }
        );
    }

    /**
     * Current authenticated user.
     */
    public function me(
        User $user
    ): array {

        $user->loadMissing([
            'roles.permissions',
        ]);

        return $this->userPayload(
            $user
        );
    }

    /**
     * Logout current device.
     */
    public function logout(
        User $user,
        string $ip,
        ?string $userAgent = null
    ): void {

        activity()

            ->causedBy(
                $user
            )

            ->performedOn(
                $user
            )

            ->event(
                'logout'
            )

            ->withProperties([
                'ip' =>
                    $ip,

                'user_agent' =>
                    $userAgent,
            ])

            ->log(
                'User logout'
            );

        $user
            ->currentAccessToken()
            ?->delete();
    }

    /**
     * Logout all devices.
     */
    public function logoutAll(
        User $user,
        string $ip,
        ?string $userAgent = null
    ): void {

        activity()

            ->causedBy(
                $user
            )

            ->performedOn(
                $user
            )

            ->event(
                'logout_all'
            )

            ->withProperties([
                'ip' =>
                    $ip,

                'user_agent' =>
                    $userAgent,
            ])

            ->log(
                'User logout all devices'
            );

        $user
            ->tokens()
            ->delete();
    }

    /**
     * User payload formatter.
     */
    private function userPayload(
        User $user
    ): array {

        return [

            'id' =>
                $user->id,

            'name' =>
                $user->name,

            'email' =>
                $user->email,

            'avatar' =>
                $user->avatar,

            'avatar_url' =>
                $user->avatar_url,

            'roles' =>
                $user
                    ->getRoleNames()
                    ->values(),

            'permissions' =>
                $user
                    ->getAllPermissions()
                    ->pluck('name')
                    ->values(),

            'is_active' =>
                (bool) $user->is_active,

            'is_verified' =>
                !is_null(
                    $user->email_verified_at
                ),

            'email_verified_at' =>
                $user->email_verified_at?->toISOString(),

            'last_login_at' =>
                $user->last_login_at?->toISOString(),
        ];
    }
}