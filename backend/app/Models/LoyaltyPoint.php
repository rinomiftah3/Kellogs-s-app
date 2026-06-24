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

/**
 * @property int $id
 * @property int $customer_profile_id
 * @property int $current_points
 * @property int $available_points
 * @property int $pending_points
 * @property int $earned_points
 * @property int $redeemed_points
 * @property int $expired_points
 * @property int $lifetime_points
 * @property int $lifetime_orders
 * @property numeric $lifetime_spending
 * @property string $tier
 * @property \Illuminate\Support\Carbon|null $tier_upgraded_at
 * @property \Illuminate\Support\Carbon|null $tier_expires_at
 * @property \Illuminate\Support\Carbon|null $last_earned_at
 * @property \Illuminate\Support\Carbon|null $last_redeemed_at
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property \Illuminate\Support\Carbon|null $last_expired_at
 * @property int $total_expiration_events
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\CustomerProfile|null $customerProfile
 * @property-read bool $is_expired
 * @property-read int $net_points
 * @property-read string $tier_badge
 * @property-read string $tier_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PointTransaction> $transactions
 * @property-read int|null $transactions_count
 * @method static Builder<static>|LoyaltyPoint active()
 * @method static Builder<static>|LoyaltyPoint bronze()
 * @method static Builder<static>|LoyaltyPoint byCustomer(?int $customerProfileId)
 * @method static Builder<static>|LoyaltyPoint expiredTier()
 * @method static \Database\Factories\LoyaltyPointFactory factory($count = null, $state = [])
 * @method static Builder<static>|LoyaltyPoint gold()
 * @method static Builder<static>|LoyaltyPoint highestPoints()
 * @method static Builder<static>|LoyaltyPoint newModelQuery()
 * @method static Builder<static>|LoyaltyPoint newQuery()
 * @method static Builder<static>|LoyaltyPoint onlyTrashed()
 * @method static Builder<static>|LoyaltyPoint platinum()
 * @method static Builder<static>|LoyaltyPoint published()
 * @method static Builder<static>|LoyaltyPoint query()
 * @method static Builder<static>|LoyaltyPoint silver()
 * @method static Builder<static>|LoyaltyPoint tier(string $tier)
 * @method static Builder<static>|LoyaltyPoint whereAvailablePoints($value)
 * @method static Builder<static>|LoyaltyPoint whereCreatedAt($value)
 * @method static Builder<static>|LoyaltyPoint whereCurrentPoints($value)
 * @method static Builder<static>|LoyaltyPoint whereCustomerProfileId($value)
 * @method static Builder<static>|LoyaltyPoint whereDeletedAt($value)
 * @method static Builder<static>|LoyaltyPoint whereEarnedPoints($value)
 * @method static Builder<static>|LoyaltyPoint whereExpiredPoints($value)
 * @method static Builder<static>|LoyaltyPoint whereId($value)
 * @method static Builder<static>|LoyaltyPoint whereIsActive($value)
 * @method static Builder<static>|LoyaltyPoint whereLastActivityAt($value)
 * @method static Builder<static>|LoyaltyPoint whereLastEarnedAt($value)
 * @method static Builder<static>|LoyaltyPoint whereLastExpiredAt($value)
 * @method static Builder<static>|LoyaltyPoint whereLastRedeemedAt($value)
 * @method static Builder<static>|LoyaltyPoint whereLifetimeOrders($value)
 * @method static Builder<static>|LoyaltyPoint whereLifetimePoints($value)
 * @method static Builder<static>|LoyaltyPoint whereLifetimeSpending($value)
 * @method static Builder<static>|LoyaltyPoint whereMetadata($value)
 * @method static Builder<static>|LoyaltyPoint wherePendingPoints($value)
 * @method static Builder<static>|LoyaltyPoint wherePublishedAt($value)
 * @method static Builder<static>|LoyaltyPoint whereRedeemedPoints($value)
 * @method static Builder<static>|LoyaltyPoint whereTier($value)
 * @method static Builder<static>|LoyaltyPoint whereTierExpiresAt($value)
 * @method static Builder<static>|LoyaltyPoint whereTierUpgradedAt($value)
 * @method static Builder<static>|LoyaltyPoint whereTotalExpirationEvents($value)
 * @method static Builder<static>|LoyaltyPoint whereUpdatedAt($value)
 * @method static Builder<static>|LoyaltyPoint withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|LoyaltyPoint withoutTrashed()
 * @mixin \Eloquent
 */
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