<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class LoyaltyPoint extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Tier Constants
    |--------------------------------------------------------------------------
    */

    public const TIER_BRONZE = 'bronze';

    public const TIER_SILVER = 'silver';

    public const TIER_GOLD = 'gold';

    public const TIER_PLATINUM = 'platinum';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'customer_profile_id',

        'current_points',
        'available_points',
        'pending_points',

        'earned_points',
        'redeemed_points',
        'expired_points',

        'lifetime_points',
        'lifetime_orders',
        'lifetime_spending',

        'tier',

        'tier_upgraded_at',
        'tier_expires_at',

        'last_earned_at',
        'last_redeemed_at',
        'last_activity_at',

        'last_expired_at',
        'total_expiration_events',

        'is_active',

        'published_at',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'tier_label',

        'tier_badge',

        'is_expired',

        'net_points',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'current_points' => 'integer',
            'available_points' => 'integer',
            'pending_points' => 'integer',

            'earned_points' => 'integer',
            'redeemed_points' => 'integer',
            'expired_points' => 'integer',

            'lifetime_points' => 'integer',
            'lifetime_orders' => 'integer',

            'lifetime_spending' => 'decimal:2',

            'tier_upgraded_at' => 'datetime',
            'tier_expires_at' => 'datetime',

            'last_earned_at' => 'datetime',
            'last_redeemed_at' => 'datetime',
            'last_activity_at' => 'datetime',

            'last_expired_at' => 'datetime',

            'is_active' => 'boolean',

            'published_at' => 'datetime',

            'metadata' => 'array',
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

            ->useLogName(
                'loyalty_point'
            )

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Loyalty Point {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(
            CustomerProfile::class
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(
            PointTransaction::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTierLabelAttribute(): string
    {
        return ucfirst(
            $this->tier
        );
    }

    public function getTierBadgeAttribute(): string
    {
        return match ($this->tier) {

            self::TIER_BRONZE =>
                'Bronze Member',

            self::TIER_SILVER =>
                'Silver Member',

            self::TIER_GOLD =>
                'Gold Member',

            self::TIER_PLATINUM =>
                'Platinum Member',

            default =>
                'Member',
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return ! is_null(
            $this->tier_expires_at
        )
        &&
        now()->greaterThan(
            $this->tier_expires_at
        );
    }

    public function getNetPointsAttribute(): int
    {
        return max(
            0,
            (int) $this->available_points
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByCustomer(
        Builder $query,
        ?int $customerProfileId
    ): Builder {

        return $query->when(

            filled($customerProfileId),

            fn (Builder $query)

                => $query->where(
                    'customer_profile_id',
                    $customerProfileId
                )
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

    public function scopePublished(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'published_at'
        );
    }

    public function scopeTier(
        Builder $query,
        string $tier
    ): Builder {

        return $query->where(
            'tier',
            $tier
        );
    }

    public function scopeBronze(
        Builder $query
    ): Builder {

        return $query->where(
            'tier',
            self::TIER_BRONZE
        );
    }

    public function scopeSilver(
        Builder $query
    ): Builder {

        return $query->where(
            'tier',
            self::TIER_SILVER
        );
    }

    public function scopeGold(
        Builder $query
    ): Builder {

        return $query->where(
            'tier',
            self::TIER_GOLD
        );
    }

    public function scopePlatinum(
        Builder $query
    ): Builder {

        return $query->where(
            'tier',
            self::TIER_PLATINUM
        );
    }

    public function scopeExpiredTier(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'tier_expires_at'
        )->where(
            'tier_expires_at',
            '<',
            now()
        );
    }

    public function scopeHighestPoints(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'available_points'
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

    public function isPublished(): bool
    {
        return ! is_null(
            $this->published_at
        );
    }

    public function isBronze(): bool
    {
        return $this->tier ===
            self::TIER_BRONZE;
    }

    public function isSilver(): bool
    {
        return $this->tier ===
            self::TIER_SILVER;
    }

    public function isGold(): bool
    {
        return $this->tier ===
            self::TIER_GOLD;
    }

    public function isPlatinum(): bool
    {
        return $this->tier ===
            self::TIER_PLATINUM;
    }

    public function isTierExpired(): bool
    {
        return $this->is_expired;
    }

    public function hasAvailablePoints(
        int $points
    ): bool {

        return $this->available_points
            >= $points;
    }

    public function totalPointsEarned(): int
    {
        return (int)
            $this->earned_points;
    }

    public function totalPointsRedeemed(): int
    {
        return (int)
            $this->redeemed_points;
    }

    public function totalPointsExpired(): int
    {
        return (int)
            $this->expired_points;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Methods
    |--------------------------------------------------------------------------
    */

    public function addPoints(
        int $points
    ): void {

        $this->increment(
            'current_points',
            $points
        );

        $this->increment(
            'available_points',
            $points
        );

        $this->increment(
            'earned_points',
            $points
        );

        $this->increment(
            'lifetime_points',
            $points
        );

        $this->update([
            'last_earned_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    public function redeemPoints(
        int $points
    ): void {

        if (
            ! $this->hasAvailablePoints(
                $points
            )
        ) {
            return;
        }

        $this->decrement(
            'current_points',
            $points
        );

        $this->decrement(
            'available_points',
            $points
        );

        $this->increment(
            'redeemed_points',
            $points
        );

        $this->update([
            'last_redeemed_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    public function expirePoints(
        int $points
    ): void {

        $points = min(
            $points,
            $this->available_points
        );

        if ($points <= 0) {
            return;
        }

        $this->decrement(
            'current_points',
            $points
        );

        $this->decrement(
            'available_points',
            $points
        );

        $this->increment(
            'expired_points',
            $points
        );

        $this->increment(
            'total_expiration_events'
        );

        $this->update([
            'last_expired_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    public function upgradeTier(
        string $tier
    ): void {

        $this->update([

            'tier' => $tier,

            'tier_upgraded_at' => now(),
        ]);
    }

    public function activate(): void
    {
        $this->update([
            'is_active' => true,
        ]);
    }

    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
        ]);
    }

    public function publish(): void
    {
        $this->update([
            'published_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}