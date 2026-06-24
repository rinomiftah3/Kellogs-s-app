<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * CustomerProfile Model
 *
 * Customer Domain Root
 *
 * Enterprise Ready
 *
 * @property int $id
 * @property int $user_id
 * @property string $customer_code
 * @property string $full_name
 * @property string|null $phone
 * @property string|null $gender
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $avatar
 * @property string|null $bio
 * @property string $membership_level
 * @property int $total_points
 * @property numeric $total_spent
 * @property int $total_orders
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_order_at
 * @property bool $email_subscribed
 * @property bool $sms_subscribed
 * @property bool $push_subscribed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerAddress> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerDevice> $devices
 * @property-read int|null $devices_count
 * @property-read \App\Models\LoyaltyPoint|null $loyaltyPoint
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PointTransaction> $pointTransactions
 * @property-read int|null $point_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductReview> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wishlist> $wishlists
 * @property-read int|null $wishlists_count
 * @method static Builder<static>|CustomerProfile active()
 * @method static Builder<static>|CustomerProfile emailSubscribed()
 * @method static \Database\Factories\CustomerProfileFactory factory($count = null, $state = [])
 * @method static Builder<static>|CustomerProfile gold()
 * @method static Builder<static>|CustomerProfile hasOrders()
 * @method static Builder<static>|CustomerProfile inactive()
 * @method static Builder<static>|CustomerProfile latestOrder()
 * @method static Builder<static>|CustomerProfile membership(string $level)
 * @method static Builder<static>|CustomerProfile newModelQuery()
 * @method static Builder<static>|CustomerProfile newQuery()
 * @method static Builder<static>|CustomerProfile onlyTrashed()
 * @method static Builder<static>|CustomerProfile platinum()
 * @method static Builder<static>|CustomerProfile pushSubscribed()
 * @method static Builder<static>|CustomerProfile query()
 * @method static Builder<static>|CustomerProfile regular()
 * @method static Builder<static>|CustomerProfile search(?string $keyword)
 * @method static Builder<static>|CustomerProfile silver()
 * @method static Builder<static>|CustomerProfile smsSubscribed()
 * @method static Builder<static>|CustomerProfile whereAvatar($value)
 * @method static Builder<static>|CustomerProfile whereBio($value)
 * @method static Builder<static>|CustomerProfile whereBirthDate($value)
 * @method static Builder<static>|CustomerProfile whereCreatedAt($value)
 * @method static Builder<static>|CustomerProfile whereCustomerCode($value)
 * @method static Builder<static>|CustomerProfile whereDeletedAt($value)
 * @method static Builder<static>|CustomerProfile whereEmailSubscribed($value)
 * @method static Builder<static>|CustomerProfile whereFullName($value)
 * @method static Builder<static>|CustomerProfile whereGender($value)
 * @method static Builder<static>|CustomerProfile whereId($value)
 * @method static Builder<static>|CustomerProfile whereIsActive($value)
 * @method static Builder<static>|CustomerProfile whereLastOrderAt($value)
 * @method static Builder<static>|CustomerProfile whereMembershipLevel($value)
 * @method static Builder<static>|CustomerProfile wherePhone($value)
 * @method static Builder<static>|CustomerProfile wherePushSubscribed($value)
 * @method static Builder<static>|CustomerProfile whereSmsSubscribed($value)
 * @method static Builder<static>|CustomerProfile whereTotalOrders($value)
 * @method static Builder<static>|CustomerProfile whereTotalPoints($value)
 * @method static Builder<static>|CustomerProfile whereTotalSpent($value)
 * @method static Builder<static>|CustomerProfile whereUpdatedAt($value)
 * @method static Builder<static>|CustomerProfile whereUserId($value)
 * @method static Builder<static>|CustomerProfile withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|CustomerProfile withoutTrashed()
 * @mixin \Eloquent
 */
class CustomerProfile extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Gender Constants
    |--------------------------------------------------------------------------
    */

    public const GENDER_MALE = 'male';

    public const GENDER_FEMALE = 'female';

    /*
    |--------------------------------------------------------------------------
    | Membership Constants
    |--------------------------------------------------------------------------
    */

    public const LEVEL_REGULAR = 'regular';

    public const LEVEL_SILVER = 'silver';

    public const LEVEL_GOLD = 'gold';

    public const LEVEL_PLATINUM = 'platinum';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'user_id',

        'customer_code',

        'full_name',

        'phone',

        'gender',

        'birth_date',

        'avatar',

        'bio',

        'membership_level',

        'total_points',

        'total_spent',

        'total_orders',

        'is_active',

        'last_order_at',

        'email_subscribed',

        'sms_subscribed',

        'push_subscribed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'birth_date' => 'date',

            'last_order_at' => 'datetime',

            'total_points' => 'integer',

            'total_orders' => 'integer',

            'total_spent' => 'decimal:2',

            'is_active' => 'boolean',

            'email_subscribed' => 'boolean',

            'sms_subscribed' => 'boolean',

            'push_subscribed' => 'boolean',
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

            ->useLogName('customer_profile')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn(string $eventName)
                    => "Customer Profile {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(
            CustomerAddress::class
        );
    }

    public function devices(): HasMany
    {
        return $this->hasMany(
            CustomerDevice::class
        );
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(
            CustomerNotification::class
        );
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(
            Wishlist::class
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(
            ProductReview::class
        );
    }

    public function loyaltyPoint(): HasOne
    {
        return $this->hasOne(
            LoyaltyPoint::class
        );
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(
            PointTransaction::class
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

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(
            filled($keyword),

            fn(Builder $query)

                => $query->where(
                    fn($q)

                        => $q->where(
                            'full_name',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'customer_code',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'phone',
                            'like',
                            "%{$keyword}%"
                        )
                )
        );
    }

    public function scopeMembership(
        Builder $query,
        string $level
    ): Builder {

        return $query->where(
            'membership_level',
            $level
        );
    }

    public function scopeRegular(
        Builder $query
    ): Builder {

        return $query->where(
            'membership_level',
            self::LEVEL_REGULAR
        );
    }

    public function scopeSilver(
        Builder $query
    ): Builder {

        return $query->where(
            'membership_level',
            self::LEVEL_SILVER
        );
    }

    public function scopeGold(
        Builder $query
    ): Builder {

        return $query->where(
            'membership_level',
            self::LEVEL_GOLD
        );
    }

    public function scopePlatinum(
        Builder $query
    ): Builder {

        return $query->where(
            'membership_level',
            self::LEVEL_PLATINUM
        );
    }

    public function scopeHasOrders(
        Builder $query
    ): Builder {

        return $query->where(
            'total_orders',
            '>',
            0
        );
    }

    public function scopeLatestOrder(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'last_order_at'
        );
    }

    public function scopeEmailSubscribed(
        Builder $query
    ): Builder {

        return $query->where(
            'email_subscribed',
            true
        );
    }

    public function scopeSmsSubscribed(
        Builder $query
    ): Builder {

        return $query->where(
            'sms_subscribed',
            true
        );
    }

    public function scopePushSubscribed(
        Builder $query
    ): Builder {

        return $query->where(
            'push_subscribed',
            true
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

    public function isMale(): bool
    {
        return $this->gender === self::GENDER_MALE;
    }

    public function isFemale(): bool
    {
        return $this->gender === self::GENDER_FEMALE;
    }

    public function isRegular(): bool
    {
        return $this->membership_level === self::LEVEL_REGULAR;
    }

    public function isSilver(): bool
    {
        return $this->membership_level === self::LEVEL_SILVER;
    }

    public function isGold(): bool
    {
        return $this->membership_level === self::LEVEL_GOLD;
    }

    public function isPlatinum(): bool
    {
        return $this->membership_level === self::LEVEL_PLATINUM;
    }

    public function hasPhone(): bool
    {
        return !empty(
            $this->phone
        );
    }

    public function hasAvatar(): bool
    {
        return !empty(
            $this->avatar
        );
    }

    public function hasOrders(): bool
    {
        return $this->total_orders > 0;
    }

    public function hasPoints(): bool
    {
        return $this->total_points > 0;
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar
            ? asset(
                'storage/' .
                $this->avatar
            )
            : null;
    }

    public function age(): ?int
    {
        return $this->birth_date
            ? $this->birth_date->age
            : null;
    }

    public function membershipBadge(): string
    {
        return ucfirst(
            $this->membership_level
        );
    }

    public function userEmail(): ?string
    {
        return $this->user?->email;
    }

    public function formattedTotalSpent(): string
    {
        return number_format(
            (float) $this->total_spent,
            0,
            ',',
            '.'
        );
    }

    public function fullIdentity(): string
    {
        return "{$this->customer_code} - {$this->full_name}";
    }

    public function isSubscribedEmail(): bool
    {
        return (bool)
            $this->email_subscribed;
    }

    public function isSubscribedSms(): bool
    {
        return (bool)
            $this->sms_subscribed;
    }

    public function isSubscribedPush(): bool
    {
        return (bool)
            $this->push_subscribed;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function increasePoints(
        int $points
    ): void {

        $this->increment(
            'total_points',
            $points
        );

        $this->refresh();
    }

    public function increaseOrderCount(): void
    {
        $this->increment(
            'total_orders'
        );

        $this->refresh();
    }

    public function updateLastOrder(): void
    {
        $this->update([
            'last_order_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'customer_code';
    }
}