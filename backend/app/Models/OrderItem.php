<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_sku_id
 * @property int $product_id
 * @property int|null $category_id
 * @property string $product_name
 * @property string|null $product_slug
 * @property string $sku
 * @property string|null $barcode
 * @property string|null $variant_name
 * @property string|null $thumbnail
 * @property int $weight
 * @property numeric $unit_price
 * @property numeric $discount_amount
 * @property numeric $final_price
 * @property int $quantity
 * @property numeric $subtotal
 * @property string|null $promotion_name
 * @property string|null $promotion_code
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read string $formatted_discount
 * @property-read string $formatted_final_price
 * @property-read string $formatted_subtotal
 * @property-read string $formatted_unit_price
 * @property-read string|null $thumbnail_url
 * @property-read float $total_discount
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductSku|null $productSku
 * @method static Builder<static>|OrderItem bestSeller()
 * @method static Builder<static>|OrderItem byCategory(?int $categoryId)
 * @method static Builder<static>|OrderItem byOrder(?int $orderId)
 * @method static Builder<static>|OrderItem byProduct(?int $productId)
 * @method static Builder<static>|OrderItem bySku(?string $sku)
 * @method static \Database\Factories\OrderItemFactory factory($count = null, $state = [])
 * @method static Builder<static>|OrderItem latestFirst()
 * @method static Builder<static>|OrderItem newModelQuery()
 * @method static Builder<static>|OrderItem newQuery()
 * @method static Builder<static>|OrderItem query()
 * @method static Builder<static>|OrderItem whereBarcode($value)
 * @method static Builder<static>|OrderItem whereCategoryId($value)
 * @method static Builder<static>|OrderItem whereCreatedAt($value)
 * @method static Builder<static>|OrderItem whereDiscountAmount($value)
 * @method static Builder<static>|OrderItem whereFinalPrice($value)
 * @method static Builder<static>|OrderItem whereId($value)
 * @method static Builder<static>|OrderItem whereMetadata($value)
 * @method static Builder<static>|OrderItem whereOrderId($value)
 * @method static Builder<static>|OrderItem whereProductId($value)
 * @method static Builder<static>|OrderItem whereProductName($value)
 * @method static Builder<static>|OrderItem whereProductSkuId($value)
 * @method static Builder<static>|OrderItem whereProductSlug($value)
 * @method static Builder<static>|OrderItem wherePromotionCode($value)
 * @method static Builder<static>|OrderItem wherePromotionName($value)
 * @method static Builder<static>|OrderItem whereQuantity($value)
 * @method static Builder<static>|OrderItem whereSku($value)
 * @method static Builder<static>|OrderItem whereSubtotal($value)
 * @method static Builder<static>|OrderItem whereThumbnail($value)
 * @method static Builder<static>|OrderItem whereUnitPrice($value)
 * @method static Builder<static>|OrderItem whereUpdatedAt($value)
 * @method static Builder<static>|OrderItem whereVariantName($value)
 * @method static Builder<static>|OrderItem whereWeight($value)
 * @mixin \Eloquent
 */
class OrderItem extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'order_id',

        'product_sku_id',

        'product_id',

        'category_id',

        'product_name',

        'product_slug',

        'sku',

        'barcode',

        'variant_name',

        'thumbnail',

        'weight',

        'unit_price',

        'discount_amount',

        'final_price',

        'quantity',

        'subtotal',

        'promotion_name',

        'promotion_code',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appended Attributes
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'thumbnail_url',

        'total_discount',

        'formatted_unit_price',

        'formatted_discount',

        'formatted_final_price',

        'formatted_subtotal',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'weight' => 'integer',

            'quantity' => 'integer',

            'unit_price' => 'decimal:2',

            'discount_amount' => 'decimal:2',

            'final_price' => 'decimal:2',

            'subtotal' => 'decimal:2',

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

            ->useLogName('order_item')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Order Item {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class
        );
    }

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(
            ProductSku::class
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

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail
            ? asset(
                'storage/' . $this->thumbnail
            )
            : null;
    }

    public function getTotalDiscountAttribute(): float
    {
        return round(
            (float) $this->discount_amount
            * (int) $this->quantity,
            2
        );
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format(
            (float) $this->unit_price,
            0,
            ',',
            '.'
        );
    }

    public function getFormattedDiscountAttribute(): string
    {
        return number_format(
            (float) $this->discount_amount,
            0,
            ',',
            '.'
        );
    }

    public function getFormattedFinalPriceAttribute(): string
    {
        return number_format(
            (float) $this->final_price,
            0,
            ',',
            '.'
        );
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format(
            (float) $this->subtotal,
            0,
            ',',
            '.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByOrder(
        Builder $query,
        ?int $orderId
    ): Builder {

        return $query->when(

            filled($orderId),

            fn (Builder $query)

                => $query->where(
                    'order_id',
                    $orderId
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

    public function scopeByCategory(
        Builder $query,
        ?int $categoryId
    ): Builder {

        return $query->when(

            filled($categoryId),

            fn (Builder $query)

                => $query->where(
                    'category_id',
                    $categoryId
                )
        );
    }

    public function scopeBySku(
        Builder $query,
        ?string $sku
    ): Builder {

        return $query->when(

            filled($sku),

            fn (Builder $query)

                => $query->where(
                    'sku',
                    $sku
                )
        );
    }

    public function scopeBestSeller(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'quantity'
        );
    }

    public function scopeLatestFirst(
        Builder $query
    ): Builder {

        return $query->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasPromotion(): bool
    {
        return !empty(
            $this->promotion_name
        );
    }

    public function hasVariant(): bool
    {
        return !empty(
            $this->variant_name
        );
    }

    public function hasBarcode(): bool
    {
        return !empty(
            $this->barcode
        );
    }

    public function hasThumbnail(): bool
    {
        return !empty(
            $this->thumbnail
        );
    }

    public function productDisplayName(): string
    {
        return trim(
            $this->product_name .
            (
                $this->variant_name
                    ? ' - ' .
                    $this->variant_name
                    : ''
            )
        );
    }

    public function categoryId(): ?int
    {
        return $this->category_id;
    }

    public function productId(): int
    {
        return (int)
            $this->product_id;
    }

    public function skuCode(): string
    {
        return $this->sku;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function revenue(): float
    {
        return (float)
            $this->subtotal;
    }

    public function totalRevenue(): float
    {
        return (float)
            $this->subtotal;
    }

    public function totalDiscountAmount(): float
    {
        return (float)
            $this->total_discount;
    }

    public function totalWeight(): int
    {
        return (int)
            $this->weight
            * (int)
            $this->quantity;
    }

    public function unitDiscount(): float
    {
        return (float)
            $this->discount_amount;
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