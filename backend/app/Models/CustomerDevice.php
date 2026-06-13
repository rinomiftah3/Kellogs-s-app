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