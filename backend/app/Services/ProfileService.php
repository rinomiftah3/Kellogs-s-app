<?php

namespace App\Services;

use App\Models\User;

use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\ValidationException;

class ProfileService
{
    /**
     * Show profile.
     */
    public function show(
        User $user
    ): array {

        return $this->profilePayload(
            $user->fresh()->loadMissing([
                'roles.permissions',
            ])
        );
    }

    /**
     * Update profile.
     */
    public function update(
        User $user,
        array $data
    ): array {

        return DB::transaction(
            function () use (
                $user,
                $data
            ) {

                $oldData = [

                    'name' =>
                        $user->name,

                    'email' =>
                        $user->email,
                ];

                $user->update([
                    'name' =>
                        $data['name'],

                    'email' =>
                        $data['email'],
                ]);

                activity()

                    ->causedBy($user)

                    ->performedOn($user)

                    ->event(
                        'profile_updated'
                    )

                    ->withProperties([
                        'old' =>
                            $oldData,

                        'attributes' => [

                            'name' =>
                                $user->name,

                            'email' =>
                                $user->email,
                        ],
                    ])

                    ->log(
                        'User updated profile'
                    );

                $this->clearCaches();

                return $this->profilePayload(
                    $user->fresh()->loadMissing([
                        'roles.permissions',
                    ])
                );
            }
        );
    }

    /**
     * Update password.
     */
    public function updatePassword(
        User $user,
        array $data
    ): void {

        if (
            !Hash::check(
                $data['current_password'],
                $user->password
            )
        ) {

            throw ValidationException::withMessages([
                'current_password' => [
                    'Password lama tidak sesuai.',
                ],
            ]);
        }

        DB::transaction(
            function () use (
                $user,
                $data
            ) {

                $user->update([
                    'password' =>
                        $data['new_password'],
                ]);

                activity()

                    ->causedBy($user)

                    ->performedOn($user)

                    ->event(
                        'password_updated'
                    )

                    ->log(
                        'User changed password'
                    );

                /*
                |--------------------------------------------------------------------------
                | Revoke All Tokens
                |--------------------------------------------------------------------------
                */

                $user
                    ->tokens()
                    ->delete();

                $this->clearCaches();
            }
        );
    }

    /**
     * Upload avatar.
     */
    public function uploadAvatar(
        User $user,
        UploadedFile $avatar
    ): array {

        return DB::transaction(
            function () use (
                $user,
                $avatar
            ) {

                $oldAvatar =
                    $user->avatar;

                $path = $avatar->store(
                    'avatars',
                    'public'
                );

                $user->update([
                    'avatar' => $path,
                ]);

                if (
                    $oldAvatar &&
                    Storage::disk(
                        'public'
                    )->exists(
                        $oldAvatar
                    )
                ) {

                    Storage::disk(
                        'public'
                    )->delete(
                        $oldAvatar
                    );
                }

                activity()

                    ->causedBy($user)

                    ->performedOn($user)

                    ->event(
                        'avatar_uploaded'
                    )

                    ->withProperties([
                        'old_avatar' =>
                            $oldAvatar,

                        'new_avatar' =>
                            $path,
                    ])

                    ->log(
                        'User uploaded avatar'
                    );

                $this->clearCaches();

                return $this->profilePayload(
                    $user->fresh()->loadMissing([
                        'roles.permissions',
                    ])
                );
            }
        );
    }

    /**
     * Delete avatar.
     */
    public function deleteAvatar(
        User $user
    ): array {

        return DB::transaction(
            function () use (
                $user
            ) {

                $oldAvatar =
                    $user->avatar;

                if (
                    $oldAvatar &&
                    Storage::disk(
                        'public'
                    )->exists(
                        $oldAvatar
                    )
                ) {

                    Storage::disk(
                        'public'
                    )->delete(
                        $oldAvatar
                    );
                }

                $user->update([
                    'avatar' => null,
                ]);

                activity()

                    ->causedBy($user)

                    ->performedOn($user)

                    ->event(
                        'avatar_deleted'
                    )

                    ->withProperties([
                        'old_avatar' =>
                            $oldAvatar,
                    ])

                    ->log(
                        'User deleted avatar'
                    );

                $this->clearCaches();

                return $this->profilePayload(
                    $user->fresh()->loadMissing([
                        'roles.permissions',
                    ])
                );
            }
        );
    }

    /**
     * Profile statistics.
     */
    public function statistics(
        User $user
    ): array {

        return [

            'roles_count' =>
                $user->roles()->count(),

            'permissions_count' =>
                $user
                    ->getAllPermissions()
                    ->count(),

            'is_verified' =>
                !is_null(
                    $user->email_verified_at
                ),

            'last_login_at' =>
                $user->last_login_at,
        ];
    }

    /**
     * Profile payload.
     */
    private function profilePayload(
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
                (bool) (
                    $user->is_active
                    ?? true
                ),

            'email_verified_at' =>
                $user->email_verified_at,

            'last_login_at' =>
                $user->last_login_at,
        ];
    }

    /**
     * Clear caches.
     */
    private function clearCaches(): void
    {
        Cache::forget(
            'dashboard.overview'
        );
    }
}