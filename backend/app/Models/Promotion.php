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
 * Promotion Model
 *
 * Enterprise Promotion Engine
 *
 * Laravel 13
 * PHP 8.4
 */
class Promotion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Promotion Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_FIXED_DISCOUNT = 'fixed_discount';

    public const TYPE_PERCENTAGE_DISCOUNT = 'percentage_discount';

    public const TYPE_FLASH_SALE = 'flash_sale';

    public const TYPE_BUY_X_GET_Y = 'buy_x_get_y';

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

        'buy_quantity',
        'free_quantity',

        'usage_limit',
        'used_count',

        'is_active',
        'is_featured',
        'is_stackable',

        'start_at',
        'end_at',

        'banner_image',

        'sort_order',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'banner_url',

        'is_running',

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

            'buy_quantity' => 'integer',

            'free_quantity' => 'integer',

            'usage_limit' => 'integer',

            'used_count' => 'integer',

            'is_active' => 'boolean',

            'is_featured' => 'boolean',

            'is_stackable' => 'boolean',

            'sort_order' => 'integer',

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

            ->useLogName('promotion')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Promotion {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function promoProducts(): HasMany
    {
        return $this->hasMany(
            PromoProduct::class
        );
    }

    public function promoCategories(): HasMany
    {
        return $this->hasMany(
            PromoCategory::class
        );
    }

    public function promoSkus(): HasMany
    {
        return $this->hasMany(
            PromoSku::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getBannerUrlAttribute(): ?string
    {
        if (empty($this->banner_image)) {
            return null;
        }

        if (
            str_starts_with(
                $this->banner_image,
                'http'
            )
        ) {
            return $this->banner_image;
        }

        return asset(
            'storage/' .
            ltrim(
                $this->banner_image,
                '/'
            )
        );
    }

    public function getIsRunningAttribute(): bool
    {
        return $this->isRunning();
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

    public function scopeFeatured(
        Builder $query
    ): Builder {

        return $query->where(
            'is_featured',
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

    public function scopeRunning(
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

    public function scopeOrdered(
        Builder $query
    ): Builder {

        return $query

            ->orderBy('sort_order')

            ->orderByDesc('created_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isRunning(): bool
    {
        if (
            !$this->is_active ||
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

    public function remainingUsage(): ?int
    {
        return $this->remaining_usage;
    }

    public function isStackable(): bool
    {
        return (bool)
            $this->is_stackable;
    }

    public function isPercentageDiscount(): bool
    {
        return $this->type ===
            self::TYPE_PERCENTAGE_DISCOUNT;
    }

    public function isFixedDiscount(): bool
    {
        return $this->type ===
            self::TYPE_FIXED_DISCOUNT;
    }

    public function isFlashSale(): bool
    {
        return $this->type ===
            self::TYPE_FLASH_SALE;
    }

    public function isBuyXGetY(): bool
    {
        return $this->type ===
            self::TYPE_BUY_X_GET_Y;
    }

    public function isFreeShipping(): bool
    {
        return $this->type ===
            self::TYPE_FREE_SHIPPING;
    }

    public function hasBanner(): bool
    {
        return !empty(
            $this->banner_image
        );
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
            !$this->isRunning()
        ) {
            return 0;
        }

        if (
            $subtotal <
            (float) $this->minimum_purchase
        ) {
            return 0;
        }

        if (
            $this->isFixedDiscount()
        ) {

            return min(
                $subtotal,
                (float)
                $this->discount_value
            );
        }

        if (
            $this->isPercentageDiscount()
            ||
            $this->isFlashSale()
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