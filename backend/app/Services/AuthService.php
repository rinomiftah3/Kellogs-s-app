<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
            ! $user ||
            ! Hash::check(
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

        if (! $user->isActive()) {
            throw ValidationException::withMessages([
                'email' => [
                    'Akun tidak aktif.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Email Verification Validation
        |--------------------------------------------------------------------------
        */

        if (
            config(
                'auth.require_verified_email',
                false
            )
            && ! $user->isVerified()
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'Email belum diverifikasi.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Token
        |--------------------------------------------------------------------------
        */

        $token = $user
            ->createToken(
                $userAgent
                    ? Str::limit($userAgent, 40)
                    : 'mobile-app'
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
            ->causedBy($user)
            ->performedOn($user)
            ->event(
                Activity::EVENT_LOGIN
            )
            ->withProperties([
                'ip' => $ip,
                'user_agent' => $userAgent,
            ])
            ->log(
                'User login'
            );

        return [
            'user' => $this->userPayload(
                $user->fresh([
                    'roles.permissions',
                ])
            ),

            'token' => $token,
        ];
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
            ->causedBy($user)
            ->performedOn($user)
            ->event(
                Activity::EVENT_LOGOUT
            )
            ->withProperties([
                'ip' => $ip,
                'user_agent' => $userAgent,
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
            ->causedBy($user)
            ->performedOn($user)
            ->event(
                Activity::EVENT_LOGOUT
            )
            ->withProperties([
                'ip' => $ip,
                'user_agent' => $userAgent,
                'all_devices' => true,
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
            'id' => $user->id,

            'name' => $user->name,

            'email' => $user->email,

            'avatar' => $user->avatar,

            'avatar_url' => $user->avatar_url,

            'roles' => $user
                ->getRoleNames()
                ->values(),

            'permissions' => $user
                ->getAllPermissions()
                ->pluck('name')
                ->values(),

            'is_active' => $user->isActive(),

            'is_verified' => $user->isVerified(),

            'email_verified_at' => $user
                ->email_verified_at
                ?->toISOString(),

            'last_login_at' => $user
                ->last_login_at
                ?->toISOString(),
        ];
    }
}