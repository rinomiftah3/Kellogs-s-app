<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * CustomerDevice Model
 *
 * Device Registration
 * Login Tracking
 * Push Notification
 *
 * Enterprise Ready
 *
 * @property int $id
 * @property int $customer_profile_id
 * @property string $device_id
 * @property string|null $device_name
 * @property string $device_type
 * @property string|null $platform
 * @property string|null $platform_version
 * @property string|null $app_version
 * @property string|null $fcm_token
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property bool $is_active
 * @property bool $is_trusted
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $last_active_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\CustomerProfile|null $customer
 * @method static Builder<static>|CustomerDevice active()
 * @method static Builder<static>|CustomerDevice byCustomer(?int $customerId)
 * @method static Builder<static>|CustomerDevice byType(?string $type)
 * @method static Builder<static>|CustomerDevice inactive()
 * @method static Builder<static>|CustomerDevice latestActive()
 * @method static Builder<static>|CustomerDevice latestLogin()
 * @method static Builder<static>|CustomerDevice newModelQuery()
 * @method static Builder<static>|CustomerDevice newQuery()
 * @method static Builder<static>|CustomerDevice online()
 * @method static Builder<static>|CustomerDevice onlyTrashed()
 * @method static Builder<static>|CustomerDevice query()
 * @method static Builder<static>|CustomerDevice trusted()
 * @method static Builder<static>|CustomerDevice untrusted()
 * @method static Builder<static>|CustomerDevice whereAppVersion($value)
 * @method static Builder<static>|CustomerDevice whereCreatedAt($value)
 * @method static Builder<static>|CustomerDevice whereCustomerProfileId($value)
 * @method static Builder<static>|CustomerDevice whereDeletedAt($value)
 * @method static Builder<static>|CustomerDevice whereDeviceId($value)
 * @method static Builder<static>|CustomerDevice whereDeviceName($value)
 * @method static Builder<static>|CustomerDevice whereDeviceType($value)
 * @method static Builder<static>|CustomerDevice whereFcmToken($value)
 * @method static Builder<static>|CustomerDevice whereId($value)
 * @method static Builder<static>|CustomerDevice whereIpAddress($value)
 * @method static Builder<static>|CustomerDevice whereIsActive($value)
 * @method static Builder<static>|CustomerDevice whereIsTrusted($value)
 * @method static Builder<static>|CustomerDevice whereLastActiveAt($value)
 * @method static Builder<static>|CustomerDevice whereLastLoginAt($value)
 * @method static Builder<static>|CustomerDevice wherePlatform($value)
 * @method static Builder<static>|CustomerDevice wherePlatformVersion($value)
 * @method static Builder<static>|CustomerDevice whereUpdatedAt($value)
 * @method static Builder<static>|CustomerDevice whereUserAgent($value)
 * @method static Builder<static>|CustomerDevice withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|CustomerDevice withoutTrashed()
 * @mixin \Eloquent
 */
class CustomerDevice extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Device Constants
    |--------------------------------------------------------------------------
    */

    public const TYPE_ANDROID = 'android';

    public const TYPE_IOS = 'ios';

    public const TYPE_WEB = 'web';

    public const TYPE_DESKTOP = 'desktop';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'customer_profile_id',

        'device_id',

        'device_name',

        'device_type',

        'platform',

        'platform_version',

        'app_version',

        'fcm_token',

        'ip_address',

        'user_agent',

        'is_active',

        'is_trusted',

        'last_login_at',

        'last_active_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'is_active' => 'boolean',

            'is_trusted' => 'boolean',

            'last_login_at' => 'datetime',

            'last_active_at' => 'datetime',
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

            ->useLogName('customer_device')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn(string $eventName)
                    => "Customer Device {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            CustomerProfile::class,
            'customer_profile_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

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

    public function scopeTrusted(
        Builder $query
    ): Builder {

        return $query->where(
            'is_trusted',
            true
        );
    }

    public function scopeUntrusted(
        Builder $query
    ): Builder {

        return $query->where(
            'is_trusted',
            false
        );
    }

    public function scopeOnline(
        Builder $query
    ): Builder {

        return $query->where(
            'last_active_at',
            '>=',
            now()->subMinutes(15)
        );
    }

    public function scopeByCustomer(
        Builder $query,
        ?int $customerId
    ): Builder {

        return $query->when(
            filled($customerId),

            fn(Builder $query)

                => $query->where(
                    'customer_profile_id',
                    $customerId
                )
        );
    }

    public function scopeByType(
        Builder $query,
        ?string $type
    ): Builder {

        return $query->when(
            filled($type),

            fn(Builder $query)

                => $query->where(
                    'device_type',
                    $type
                )
        );
    }

    public function scopeLatestActive(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'last_active_at'
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
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return (bool)
            $this->is_active;
    }

    public function isTrusted(): bool
    {
        return (bool)
            $this->is_trusted;
    }

    public function isOnline(): bool
    {
        if (!$this->last_active_at) {
            return false;
        }

        return $this->last_active_at
            ->gt(now()->subMinutes(15));
    }

    public function hasFcmToken(): bool
    {
        return !empty(
            $this->fcm_token
        );
    }

    public function hasAppVersion(): bool
    {
        return !empty(
            $this->app_version
        );
    }

    public function hasPlatform(): bool
    {
        return !empty(
            $this->platform
        );
    }

    public function hasLoginHistory(): bool
    {
        return !is_null(
            $this->last_login_at
        );
    }

    public function customerName(): ?string
    {
        return $this->customer?->full_name;
    }

    public function deviceDisplayName(): string
    {
        return $this->device_name
            ?: strtoupper(
                $this->device_type
            );
    }

    public function platformDisplay(): string
    {
        return trim(
            implode(' ', array_filter([
                $this->platform,
                $this->platform_version,
            ]))
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function markTrusted(): bool
    {
        return $this->update([
            'is_trusted' => true,
        ]);
    }

    public function markUntrusted(): bool
    {
        return $this->update([
            'is_trusted' => false,
        ]);
    }

    public function activate(): bool
    {
        return $this->update([
            'is_active' => true,
        ]);
    }

    public function deactivate(): bool
    {
        return $this->update([
            'is_active' => false,
        ]);
    }

    public function updateActivity(): bool
    {
        return $this->update([
            'last_active_at' => now(),
        ]);
    }

    public function updateLogin(): bool
    {
        return $this->update([
            'last_login_at' => now(),
            'last_active_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'device_id';
    }
}