<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

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