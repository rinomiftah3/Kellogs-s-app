<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * PromoProduct Model
 *
 * Product Specific Promotion Configuration
 *
 * Laravel 13
 * PHP 8.4
 *
 * @property int $id
 * @property int $promotion_id
 * @property int $product_id
 * @property numeric|null $discount_value
 * @property numeric|null $maximum_discount
 * @property bool $is_active
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read float $effective_discount
 * @property-read float|null $effective_maximum_discount
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Promotion|null $promotion
 * @method static Builder<static>|PromoProduct active()
 * @method static Builder<static>|PromoProduct byProduct(?int $productId)
 * @method static Builder<static>|PromoProduct byPromotion(?int $promotionId)
 * @method static Builder<static>|PromoProduct inactive()
 * @method static Builder<static>|PromoProduct newModelQuery()
 * @method static Builder<static>|PromoProduct newQuery()
 * @method static Builder<static>|PromoProduct query()
 * @method static Builder<static>|PromoProduct running()
 * @method static Builder<static>|PromoProduct whereCreatedAt($value)
 * @method static Builder<static>|PromoProduct whereDiscountValue($value)
 * @method static Builder<static>|PromoProduct whereId($value)
 * @method static Builder<static>|PromoProduct whereIsActive($value)
 * @method static Builder<static>|PromoProduct whereMaximumDiscount($value)
 * @method static Builder<static>|PromoProduct whereNotes($value)
 * @method static Builder<static>|PromoProduct whereProductId($value)
 * @method static Builder<static>|PromoProduct wherePromotionId($value)
 * @method static Builder<static>|PromoProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PromoProduct extends Model
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

        'product_id',

        'discount_value',

        'maximum_discount',

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

            ->useLogName('promo_product')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Promo Product {$eventName}"
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Product::class
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

    public function scopeByProduct(
        Builder $query,
        ?int $productId
    ): Builder {

        return $query->when(

            filled($productId),

            fn (Builder $query)

                => $query->where(
                    'product_id',
                    $productId
                )
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

    public function hasCustomDiscount(): bool
    {
        return $this->discount_value !== null;
    }

    public function hasCustomMaximumDiscount(): bool
    {
        return $this->maximum_discount !== null;
    }

    public function canApply(): bool
    {
        return

            $this->is_active

            &&

            $this->promotion !== null

            &&

            $this->promotion->isRunning();
    }

    public function productName(): ?string
    {
        return $this->product?->name;
    }

    public function promotionName(): ?string
    {
        return $this->promotion?->name;
    }

    public function productSkuCount(): int
    {
        return $this->product?->skus()?->count()
            ?? 0;
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
            (float)
            $this->promotion->minimum_purchase
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