<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * Voucher Model
 *
 * Enterprise Promotion System
 *
 * Laravel 13
 * PHP 8.4
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property string $type
 * @property numeric $discount_value
 * @property numeric|null $maximum_discount
 * @property numeric $minimum_purchase
 * @property int|null $usage_limit
 * @property int $usage_per_user
 * @property int $used_count
 * @property bool $is_active
 * @property bool $is_public
 * @property bool $is_stackable
 * @property \Illuminate\Support\Carbon $start_at
 * @property \Illuminate\Support\Carbon $end_at
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read bool $is_expired
 * @property-read bool $is_started
 * @property-read int|null $remaining_usage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VoucherUsage> $usages
 * @property-read int|null $usages_count
 * @method static Builder<static>|Voucher active()
 * @method static Builder<static>|Voucher expired()
 * @method static \Database\Factories\VoucherFactory factory($count = null, $state = [])
 * @method static Builder<static>|Voucher newModelQuery()
 * @method static Builder<static>|Voucher newQuery()
 * @method static Builder<static>|Voucher onlyTrashed()
 * @method static Builder<static>|Voucher public()
 * @method static Builder<static>|Voucher query()
 * @method static Builder<static>|Voucher search(?string $keyword)
 * @method static Builder<static>|Voucher started()
 * @method static Builder<static>|Voucher type(string $type)
 * @method static Builder<static>|Voucher valid()
 * @method static Builder<static>|Voucher whereCode($value)
 * @method static Builder<static>|Voucher whereCreatedAt($value)
 * @method static Builder<static>|Voucher whereDeletedAt($value)
 * @method static Builder<static>|Voucher whereDescription($value)
 * @method static Builder<static>|Voucher whereDiscountValue($value)
 * @method static Builder<static>|Voucher whereEndAt($value)
 * @method static Builder<static>|Voucher whereId($value)
 * @method static Builder<static>|Voucher whereIsActive($value)
 * @method static Builder<static>|Voucher whereIsPublic($value)
 * @method static Builder<static>|Voucher whereIsStackable($value)
 * @method static Builder<static>|Voucher whereMaximumDiscount($value)
 * @method static Builder<static>|Voucher whereMetadata($value)
 * @method static Builder<static>|Voucher whereMinimumPurchase($value)
 * @method static Builder<static>|Voucher whereName($value)
 * @method static Builder<static>|Voucher whereStartAt($value)
 * @method static Builder<static>|Voucher whereType($value)
 * @method static Builder<static>|Voucher whereUpdatedAt($value)
 * @method static Builder<static>|Voucher whereUsageLimit($value)
 * @method static Builder<static>|Voucher whereUsagePerUser($value)
 * @method static Builder<static>|Voucher whereUsedCount($value)
 * @method static Builder<static>|Voucher withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Voucher withoutTrashed()
 * @mixin \Eloquent
 */
class Voucher extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Voucher Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_FIXED = 'fixed';

    public const TYPE_PERCENTAGE = 'percentage';

    public const TYPE_FREE_SHIPPING = 'free_shipping';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'name',

        'code',

        'description',

        'type',

        'discount_value',

        'maximum_discount',

        'minimum_purchase',

        'usage_limit',

        'usage_per_user',

        'used_count',

        'is_active',

        'is_public',

        'is_stackable',

        'start_at',

        'end_at',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'is_expired',

        'is_started',

        'remaining_usage',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'discount_value' => 'decimal:2',

            'maximum_discount' => 'decimal:2',

            'minimum_purchase' => 'decimal:2',

            'usage_limit' => 'integer',

            'usage_per_user' => 'integer',

            'used_count' => 'integer',

            'is_active' => 'boolean',

            'is_public' => 'boolean',

            'is_stackable' => 'boolean',

            'start_at' => 'datetime',

            'end_at' => 'datetime',

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

            ->useLogName('voucher')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Voucher {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function usages(): HasMany
    {
        return $this->hasMany(
            VoucherUsage::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_at !== null
            && now()->gt($this->end_at);
    }

    public function getIsStartedAttribute(): bool
    {
        return $this->start_at !== null
            && now()->gte($this->start_at);
    }

    public function getRemainingUsageAttribute(): ?int
    {
        if ($this->usage_limit === null) {
            return null;
        }

        return max(
            0,
            $this->usage_limit
            - $this->used_count
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

    public function scopePublic(
        Builder $query
    ): Builder {

        return $query->where(
            'is_public',
            true
        );
    }

    public function scopeStarted(
        Builder $query
    ): Builder {

        return $query->where(
            'start_at',
            '<=',
            now()
        );
    }

    public function scopeExpired(
        Builder $query
    ): Builder {

        return $query->where(
            'end_at',
            '<',
            now()
        );
    }

    public function scopeValid(
        Builder $query
    ): Builder {

        return $query

            ->where(
                'is_active',
                true
            )

            ->where(
                'start_at',
                '<=',
                now()
            )

            ->where(
                'end_at',
                '>=',
                now()
            )

            ->where(function (
                Builder $query
            ) {
                $query

                    ->whereNull(
                        'usage_limit'
                    )

                    ->orWhereColumn(
                        'used_count',
                        '<',
                        'usage_limit'
                    );
            });
    }

    public function scopeType(
        Builder $query,
        string $type
    ): Builder {

        return $query->where(
            'type',
            $type
        );
    }

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(
            filled($keyword),

            fn (Builder $query)

                => $query->where(
                    fn ($q)

                        => $q->where(
                            'name',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'code',
                            'like',
                            "%{$keyword}%"
                        )
                )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (
            !$this->start_at ||
            !$this->end_at
        ) {
            return false;
        }

        if (
            !now()->between(
                $this->start_at,
                $this->end_at
            )
        ) {
            return false;
        }

        if (
            $this->usage_limit !== null
            &&
            $this->used_count >= $this->usage_limit
        ) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        return $this->end_at !== null
            && now()->gt($this->end_at);
    }

    public function isStarted(): bool
    {
        return $this->start_at !== null
            && now()->gte($this->start_at);
    }

    public function hasUsageLimit(): bool
    {
        return $this->usage_limit !== null;
    }

    public function isStackable(): bool
    {
        return (bool)
            $this->is_stackable;
    }

    public function remainingUsage(): ?int
    {
        return $this->remaining_usage;
    }

    public function isPercentage(): bool
    {
        return $this->type ===
            self::TYPE_PERCENTAGE;
    }

    public function isFixed(): bool
    {
        return $this->type ===
            self::TYPE_FIXED;
    }

    public function isFreeShipping(): bool
    {
        return $this->type ===
            self::TYPE_FREE_SHIPPING;
    }

    public function meetsMinimumPurchase(
        float $subtotal
    ): bool {

        return $subtotal >=
            (float) $this->minimum_purchase;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function calculateDiscount(
        float $subtotal
    ): float {

        if (
            !$this->isValid()
        ) {
            return 0;
        }

        if (
            !$this->meetsMinimumPurchase(
                $subtotal
            )
        ) {
            return 0;
        }

        if (
            $this->isFixed()
        ) {

            return min(
                $subtotal,
                (float)
                $this->discount_value
            );
        }

        if (
            $this->isPercentage()
        ) {

            $discount =
                $subtotal
                *
                (
                    (float)
                    $this->discount_value
                    / 100
                );

            if (
                $this->maximum_discount !== null
            ) {

                $discount = min(
                    $discount,
                    (float)
                    $this->maximum_discount
                );
            }

            return $discount;
        }

        return 0;
    }

    public function incrementUsage(): void
    {
        $this->increment(
            'used_count'
        );

        $this->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}