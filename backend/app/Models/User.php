<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Role Constants
    |--------------------------------------------------------------------------
    */

    public const ROLE_SUPER_ADMIN = 'Super Admin';

    public const ROLE_ADMIN = 'Admin';

    public const ROLE_STAFF = 'Staff';

    public const ROLE_CUSTOMER = 'Customer';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'name',

        'email',

        'password',

        'avatar',

        'is_active',

        'email_verified_at',

        'last_login_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [

        'password',

        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appended Attributes
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'avatar_url',

        'display_name',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',

            'last_login_at' => 'datetime',

            'is_active' => 'boolean',

            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()

            ->useLogName('user')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
                'remember_token',
                'last_login_at',
            ])

            ->setDescriptionForEvent(
                fn(string $eventName)
                    => "User {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customerProfile(): HasOne
    {
        return $this->hasOne(
            CustomerProfile::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar)) {
            return null;
        }

        if (
            str_starts_with($this->avatar, 'http://') ||
            str_starts_with($this->avatar, 'https://')
        ) {
            return $this->avatar;
        }

        return asset(
            'storage/' . $this->avatar
        );
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(

            filled($keyword),

            function (
                Builder $query
            ) use (
                $keyword
            ) {

                $query->where(

                    function (
                        Builder $query
                    ) use (
                        $keyword
                    ) {

                        $query->where(
                            'name',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'email',
                            'like',
                            "%{$keyword}%"
                        );
                    }
                );
            }
        );
    }

    public function scopeActive(
        Builder $query
    ): Builder {

        return $query->where(
            'is_active',
            true
        );
    }

    public function scopeInactive(
        Builder $query
    ): Builder {

        return $query->where(
            'is_active',
            false
        );
    }

    public function scopeVerified(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'email_verified_at'
        );
    }

    public function scopeLatestLogin(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'last_login_at'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAdmins(
        Builder $query
    ): Builder {

        return $query->role([
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
        ]);
    }

    public function scopeCustomers(
        Builder $query
    ): Builder {

        return $query->role(
            self::ROLE_CUSTOMER
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function isVerified(): bool
    {
        return !is_null(
            $this->email_verified_at
        );
    }

    public function hasVerifiedEmailCustom(): bool
    {
        return $this->isVerified();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(
            self::ROLE_SUPER_ADMIN
        );
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole([
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
        ]);
    }

    public function isStaff(): bool
    {
        return $this->hasRole(
            self::ROLE_STAFF
        );
    }

    public function isCustomer(): bool
    {
        return $this->hasRole(
            self::ROLE_CUSTOMER
        );
    }

    public function hasAvatar(): bool
    {
        return !empty(
            $this->avatar
        );
    }

    public function hasAnyRoleName(
        array $roles
    ): bool {

        return $this->roles()
            ->whereIn(
                'name',
                $roles
            )
            ->exists();
    }

    public function isOnline(): bool
    {
        if (!$this->last_login_at) {
            return false;
        }

        return $this->last_login_at
            ->gt(
                now()->subMinutes(15)
            );
    }

    public function lastLogin(): ?string
    {
        return $this->last_login_at
            ? $this->last_login_at
                ->toDateTimeString()
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Route Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}