<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;

class UserService
{
    /**
     * Paginated users.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ) {
        return User::query()
            ->with('roles')
            ->search(
                $filters['search'] ?? null
            )
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find user.
     */
    public function find(
        User $user
    ): User {
        return $user->load([
            'roles.permissions',
        ]);
    }

    /**
     * Create user.
     */
    public function create(
        array $data,
        User $actor,
        Request $request
    ): User {
        return DB::transaction(
            function () use (
                $data,
                $actor,
                $request
            ) {
                $payload = [
                    'name' => $data['name'],

                    'email' => $data['email'],

                    'password' => Hash::make(
                        $data['password']
                    ),

                    'is_active' => $data['is_active']
                        ?? true,
                ];

                if (
                    isset($data['avatar'])
                ) {
                    $payload['avatar'] =
                        $disk = env(
                        'AVATAR_DISK',
                        'public'
                    );
                }

                $user = User::create(
                    $payload
                );

                $user->syncRoles([
                    $data['role'],
                ]);

                activity()
                    ->causedBy($actor)
                    ->performedOn($user)
                    ->event(
                        'user_created'
                    )
                    ->withProperties([
                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'new' => [
                            'name' =>
                                $user->name,

                            'email' =>
                                $user->email,

                            'roles' =>
                                $user
                                    ->getRoleNames()
                                    ->toArray(),
                        ],
                    ])
                    ->log(
                        'User created'
                    );

                $this->clearCaches();

                return $user->load([
                    'roles.permissions',
                ]);
            }
        );
    }

    /**
     * Update user.
     */
    public function update(
        User $user,
        array $data,
        User $actor,
        Request $request
    ): User {
        return DB::transaction(
            function () use (
                $user,
                $data,
                $actor,
                $request
            ) {
                if (
                    $user->hasRole(
                        User::ROLE_SUPER_ADMIN
                    )
                    &&
                    $data['role']
                    !== User::ROLE_SUPER_ADMIN
                ) {
                    $count = User::role(
                        User::ROLE_SUPER_ADMIN
                    )->count();

                    if ($count <= 1) {
                        throw ValidationException::withMessages([
                            'role' => [
                                'Minimal harus ada satu Super Admin.',
                            ],
                        ]);
                    }
                }

                $oldData = [
                    'name' =>
                        $user->name,

                    'email' =>
                        $user->email,

                    'roles' =>
                        $user
                            ->getRoleNames()
                            ->toArray(),
                ];

                $payload = [
                    'name' =>
                        $data['name'],

                    'email' =>
                        $data['email'],
                ];

                if (
                    ! empty(
                        $data['password']
                    )
                ) {
                    $payload['password'] =
                        Hash::make(
                            $data['password']
                        );
                }

                if (
                    array_key_exists(
                        'is_active',
                        $data
                    )
                ) {
                    $payload['is_active'] =
                        $data['is_active'];
                }

                if (
                    isset($data['avatar'])
                ) {
                    if (
                        $user->avatar
                    ) {
                        Storage::disk(
                            'public'
                        )->delete(
                            $user->avatar
                        );
                    }

                    $payload['avatar'] =
                        $disk = env(
                        'AVATAR_DISK',
                        'public'
                    );
                }

                $user->update(
                    $payload
                );

                $user->syncRoles([
                    $data['role'],
                ]);

                activity()
                    ->causedBy($actor)
                    ->performedOn($user)
                    ->event(
                        'user_updated'
                    )
                    ->withProperties([
                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,

                        'new' => [
                            'name' =>
                                $user->name,

                            'email' =>
                                $user->email,

                            'roles' =>
                                $user
                                    ->getRoleNames()
                                    ->toArray(),
                        ],
                    ])
                    ->log(
                        'User updated'
                    );

                $this->clearCaches();

                return $user->fresh([
                    'roles.permissions',
                ]);
            }
        );
    }

    /**
     * Delete user.
     */
    public function delete(
        User $user,
        User $actor,
        Request $request
    ): void {
        DB::transaction(
            function () use (
                $user,
                $actor,
                $request
            ) {
                if (
                    $user->id ===
                    $actor->id
                ) {
                    throw ValidationException::withMessages([
                        'user' => [
                            'Tidak dapat menghapus akun sendiri.',
                        ],
                    ]);
                }

                if (
                    $user->hasRole(
                        User::ROLE_SUPER_ADMIN
                    )
                ) {
                    $count = User::role(
                        User::ROLE_SUPER_ADMIN
                    )->count();

                    if ($count <= 1) {
                        throw ValidationException::withMessages([
                            'user' => [
                                'Minimal harus ada satu Super Admin.',
                            ],
                        ]);
                    }
                }

                $oldData = [
                    'id' =>
                        $user->id,

                    'name' =>
                        $user->name,

                    'email' =>
                        $user->email,

                    'roles' =>
                        $user
                            ->getRoleNames()
                            ->toArray(),
                ];

                activity()
                    ->causedBy($actor)
                    ->performedOn($user)
                    ->event(
                        'user_deleted'
                    )
                    ->withProperties([
                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,
                    ])
                    ->log(
                        'User deleted'
                    );

                if (
                    $user->avatar
                ) {
                    Storage::disk(
                        'public'
                    )->delete(
                        $user->avatar
                    );
                }

                $user
                    ->tokens()
                    ->delete();

                $user->delete();

                $this->clearCaches();
            }
        );
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(
        User $user,
        User $actor
    ): User {
        return DB::transaction(
            function () use (
                $user,
                $actor
            ) {
                if (
                    $user->id ===
                    $actor->id
                    &&
                    $user->isActive()
                ) {
                    throw ValidationException::withMessages([
                        'user' => [
                            'Tidak dapat menonaktifkan akun sendiri.',
                        ],
                    ]);
                }

                if (
                    $user->hasRole(
                        User::ROLE_SUPER_ADMIN
                    )
                    &&
                    $user->isActive()
                ) {
                    $count = User::role(
                        User::ROLE_SUPER_ADMIN
                    )
                    ->active()
                    ->count();

                    if ($count <= 1) {
                        throw ValidationException::withMessages([
                            'user' => [
                                'Minimal harus ada satu Super Admin aktif.',
                            ],
                        ]);
                    }
                }

                $user->update([
                    'is_active' =>
                        ! $user->is_active,
                ]);

                activity()
                    ->causedBy($actor)
                    ->performedOn($user)
                    ->event(
                        'user_updated'
                    )
                    ->withProperties([
                        'status' =>
                            $user->is_active,
                    ])
                    ->log(
                        'User status changed'
                    );

                $this->clearCaches();

                return $user->fresh([
                    'roles.permissions',
                ]);
            }
        );
    }

    /**
     * Clear application caches.
     */
    private function clearCaches(): void
    {
        app(
            PermissionRegistrar::class
        )->forgetCachedPermissions();

        Cache::forget(
            'dashboard.overview'
        );
    }
}