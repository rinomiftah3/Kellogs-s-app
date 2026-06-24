<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * PromoSku Model
 *
 * SKU Specific Promotion Configuration
 *
 * Laravel 13
 * PHP 8.4
 *
 * @property int $id
 * @property int $promotion_id
 * @property int $product_sku_id
 * @property numeric|null $discount_value
 * @property numeric|null $maximum_discount
 * @property numeric|null $minimum_purchase
 * @property numeric|null $promo_price
 * @property int|null $max_quantity_per_order
 * @property int|null $usage_limit
 * @property int $used_count
 * @property bool $is_active
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read float $effective_discount
 * @property-read float|null $effective_maximum_discount
 * @property-read float $effective_minimum_purchase
 * @property-read int|null $remaining_usage
 * @property-read \App\Models\ProductSku|null $productSku
 * @property-read \App\Models\Promotion|null $promotion
 * @method static Builder<static>|PromoSku active()
 * @method static Builder<static>|PromoSku byPromotion(?int $promotionId)
 * @method static Builder<static>|PromoSku bySku(?int $skuId)
 * @method static Builder<static>|PromoSku flashSale()
 * @method static Builder<static>|PromoSku inactive()
 * @method static Builder<static>|PromoSku newModelQuery()
 * @method static Builder<static>|PromoSku newQuery()
 * @method static Builder<static>|PromoSku query()
 * @method static Builder<static>|PromoSku running()
 * @method static Builder<static>|PromoSku whereCreatedAt($value)
 * @method static Builder<static>|PromoSku whereDiscountValue($value)
 * @method static Builder<static>|PromoSku whereId($value)
 * @method static Builder<static>|PromoSku whereIsActive($value)
 * @method static Builder<static>|PromoSku whereMaxQuantityPerOrder($value)
 * @method static Builder<static>|PromoSku whereMaximumDiscount($value)
 * @method static Builder<static>|PromoSku whereMinimumPurchase($value)
 * @method static Builder<static>|PromoSku whereNotes($value)
 * @method static Builder<static>|PromoSku whereProductSkuId($value)
 * @method static Builder<static>|PromoSku wherePromoPrice($value)
 * @method static Builder<static>|PromoSku wherePromotionId($value)
 * @method static Builder<static>|PromoSku whereUpdatedAt($value)
 * @method static Builder<static>|PromoSku whereUsageLimit($value)
 * @method static Builder<static>|PromoSku whereUsedCount($value)
 * @mixin \Eloquent
 */
class PromoSku extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'promotion_id',

        'product_sku_id',

        'discount_value',

        'maximum_discount',

        'minimum_purchase',

        'promo_price',

        'max_quantity_per_order',

        'usage_limit',

        'used_count',

        'is_active',

        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'effective_discount',

        'effective_maximum_discount',

        'effective_minimum_purchase',

        'remaining_usage',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'discount_value' => 'decimal:2',

            'maximum_discount' => 'decimal:2',

            'minimum_purchase' => 'decimal:2',

            'promo_price' => 'decimal:2',

            'max_quantity_per_order' => 'integer',

            'usage_limit' => 'integer',

            'used_count' => 'integer',

            'is_active' => 'boolean',
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

            ->useLogName('promo_sku')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Promo SKU {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(
            Promotion::class
        );
    }

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(
            ProductSku::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getEffectiveDiscountAttribute(): float
    {
        if ($this->discount_value !== null) {
            return (float) $this->discount_value;
        }

        return (float)
            ($this->promotion?->discount_value ?? 0);
    }

    public function getEffectiveMaximumDiscountAttribute(): ?float
    {
        if ($this->maximum_discount !== null) {
            return (float) $this->maximum_discount;
        }

        return $this->promotion?->maximum_discount
            ? (float) $this->promotion->maximum_discount
            : null;
    }

    public function getEffectiveMinimumPurchaseAttribute(): float
    {
        if ($this->minimum_purchase !== null) {
            return (float) $this->minimum_purchase;
        }

        return (float)
            ($this->promotion?->minimum_purchase ?? 0);
    }

    public function getRemainingUsageAttribute(): ?int
    {
        if ($this->usage_limit === null) {
            return null;
        }

        return max(
            0,
            $this->usage_limit - $this->used_count
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

    public function scopeRunning(
        Builder $query
    ): Builder {

        return $query->whereHas(
            'promotion',
            fn (Builder $query)
                => $query->running()
        );
    }

    public function scopeByPromotion(
        Builder $query,
        ?int $promotionId
    ): Builder {

        return $query->when(

            filled($promotionId),

            fn (Builder $query)

                => $query->where(
                    'promotion_id',
                    $promotionId
                )
        );
    }

    public function scopeBySku(
        Builder $query,
        ?int $skuId
    ): Builder {

        return $query->when(

            filled($skuId),

            fn (Builder $query)

                => $query->where(
                    'product_sku_id',
                    $skuId
                )
        );
    }

    public function scopeFlashSale(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'promo_price'
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

    public function hasPromotion(): bool
    {
        return $this->promotion !== null;
    }

    public function canApply(): bool
    {
        return

            $this->is_active

            &&

            $this->promotion !== null

            &&

            $this->promotion->isRunning()

            &&

            (
                $this->usage_limit === null
                ||
                $this->used_count < $this->usage_limit
            );
    }

    public function hasPromoPrice(): bool
    {
        return $this->promo_price !== null;
    }

    public function hasUsageLimit(): bool
    {
        return $this->usage_limit !== null;
    }

    public function hasCustomDiscount(): bool
    {
        return $this->discount_value !== null;
    }

    public function hasCustomMaximumDiscount(): bool
    {
        return $this->maximum_discount !== null;
    }

    public function hasCustomMinimumPurchase(): bool
    {
        return $this->minimum_purchase !== null;
    }

    public function skuCode(): ?string
    {
        return $this->productSku?->sku;
    }

    public function productName(): ?string
    {
        return $this->productSku?->product?->name;
    }

    public function promotionName(): ?string
    {
        return $this->promotion?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function calculateDiscount(
        float $subtotal
    ): float {

        if (!$this->canApply()) {
            return 0;
        }

        if (
            $subtotal <
            $this->effective_minimum_purchase
        ) {
            return 0;
        }

        $discountValue =
            $this->effective_discount;

        $maximumDiscount =
            $this->effective_maximum_discount;

        if (
            $this->promotion->isFixedDiscount()
        ) {

            return min(
                $subtotal,
                $discountValue
            );
        }

        if (

            $this->promotion->isPercentageDiscount()

            ||

            $this->promotion->isFlashSale()
        ) {

            $discount =

                $subtotal

                *

                (
                    $discountValue
                    / 100
                );

            if (
                $maximumDiscount !== null
            ) {

                $discount = min(
                    $discount,
                    $maximumDiscount
                );
            }

            return $discount;
        }

        return 0;
    }

    public function getEffectivePrice(
        float $originalPrice
    ): float {

        if (
            $this->hasPromoPrice()
        ) {
            return (float)
                $this->promo_price;
        }

        return max(
            0,
            $originalPrice
            - $this->calculateDiscount(
                $originalPrice
            )
        );
    }

    public function incrementUsage(): void
    {
        $this->increment(
            'used_count'
        );
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