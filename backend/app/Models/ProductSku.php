<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * @property int $id
 * @property int $product_id
 * @property string $sku
 * @property string|null $barcode
 * @property numeric $price
 * @property numeric|null $compare_at_price
 * @property numeric|null $cost_price
 * @property numeric $weight
 * @property numeric|null $length
 * @property numeric|null $width
 * @property numeric|null $height
 * @property int $minimum_order_quantity
 * @property int|null $maximum_order_quantity
 * @property bool $is_default
 * @property string $status
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CheckoutItem> $checkoutItems
 * @property-read int|null $checkout_items_count
 * @property-read int $inventory_stock
 * @property-read float $margin_percentage
 * @property-read float $profit
 * @property-read \App\Models\Inventory|null $inventory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOptionValue> $optionValues
 * @property-read int|null $option_values_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PromoSku> $promoSkus
 * @property-read int|null $promo_skus_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSkuValue> $values
 * @property-read int|null $values_count
 * @method static Builder<static>|ProductSku active()
 * @method static Builder<static>|ProductSku activeStatus()
 * @method static Builder<static>|ProductSku default()
 * @method static Builder<static>|ProductSku draft()
 * @method static \Database\Factories\ProductSkuFactory factory($count = null, $state = [])
 * @method static Builder<static>|ProductSku inStock()
 * @method static Builder<static>|ProductSku latest()
 * @method static Builder<static>|ProductSku newModelQuery()
 * @method static Builder<static>|ProductSku newQuery()
 * @method static Builder<static>|ProductSku onlyTrashed()
 * @method static Builder<static>|ProductSku outOfStock()
 * @method static Builder<static>|ProductSku published()
 * @method static Builder<static>|ProductSku query()
 * @method static Builder<static>|ProductSku search(?string $keyword)
 * @method static Builder<static>|ProductSku status(string $status)
 * @method static Builder<static>|ProductSku whereBarcode($value)
 * @method static Builder<static>|ProductSku whereCompareAtPrice($value)
 * @method static Builder<static>|ProductSku whereCostPrice($value)
 * @method static Builder<static>|ProductSku whereCreatedAt($value)
 * @method static Builder<static>|ProductSku whereDeletedAt($value)
 * @method static Builder<static>|ProductSku whereHeight($value)
 * @method static Builder<static>|ProductSku whereId($value)
 * @method static Builder<static>|ProductSku whereIsActive($value)
 * @method static Builder<static>|ProductSku whereIsDefault($value)
 * @method static Builder<static>|ProductSku whereLength($value)
 * @method static Builder<static>|ProductSku whereMaximumOrderQuantity($value)
 * @method static Builder<static>|ProductSku whereMinimumOrderQuantity($value)
 * @method static Builder<static>|ProductSku wherePrice($value)
 * @method static Builder<static>|ProductSku whereProductId($value)
 * @method static Builder<static>|ProductSku wherePublishedAt($value)
 * @method static Builder<static>|ProductSku whereSku($value)
 * @method static Builder<static>|ProductSku whereStatus($value)
 * @method static Builder<static>|ProductSku whereUpdatedAt($value)
 * @method static Builder<static>|ProductSku whereWeight($value)
 * @method static Builder<static>|ProductSku whereWidth($value)
 * @method static Builder<static>|ProductSku withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ProductSku withoutTrashed()
 * @mixin \Eloquent
 */
class ProductSku extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_ARCHIVED = 'archived';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_id',

        'sku',

        'barcode',

        'price',

        'compare_at_price',

        'cost_price',

        'weight',

        'length',

        'width',

        'height',

        'minimum_order_quantity',

        'maximum_order_quantity',

        'is_default',

        'status',

        'is_active',

        'published_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'price' => 'decimal:2',

            'compare_at_price' => 'decimal:2',

            'cost_price' => 'decimal:2',

            'weight' => 'decimal:2',

            'length' => 'decimal:2',

            'width' => 'decimal:2',

            'height' => 'decimal:2',

            'minimum_order_quantity' => 'integer',

            'maximum_order_quantity' => 'integer',

            'is_default' => 'boolean',

            'is_active' => 'boolean',

            'published_at' => 'datetime',
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

            ->useLogName('product_sku')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Product SKU {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Product::class
        );
    }

    public function values(): HasMany
    {
        return $this->hasMany(
            ProductSkuValue::class
        );
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_sku_values',
            'product_sku_id',
            'product_option_value_id'
        );
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(
            Inventory::class
        );
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(
            CartItem::class
        );
    }

    public function checkoutItems(): HasMany
    {
        return $this->hasMany(
            CheckoutItem::class
        );
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(
            OrderItem::class
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

    public function getProfitAttribute(): float
    {
        return (float) $this->price
            - (float) ($this->cost_price ?? 0);
    }

    public function getMarginPercentageAttribute(): float
    {
        if (
            empty($this->cost_price)
            || $this->cost_price <= 0
        ) {
            return 0;
        }

        return round(
            (
                (
                    $this->price
                    - $this->cost_price
                )
                / $this->cost_price
            ) * 100,
            2
        );
    }

    public function getInventoryStockAttribute(): int
    {
        return (int)
            ($this->inventory?->available_stock ?? 0);
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

    public function scopeActiveStatus(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_ACTIVE
        );
    }

    public function scopePublished(
        Builder $query
    ): Builder {

        return $query
            ->whereNotNull(
                'published_at'
            )
            ->where(
                'published_at',
                '<=',
                now()
            );
    }

    public function scopeDefault(
        Builder $query
    ): Builder {

        return $query->where(
            'is_default',
            true
        );
    }

    public function scopeStatus(
        Builder $query,
        string $status
    ): Builder {

        return $query->where(
            'status',
            $status
        );
    }

    public function scopeDraft(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_DRAFT
        );
    }

    public function scopeInStock(
        Builder $query
    ): Builder {

        return $query->whereHas(
            'inventory',
            fn ($query)

                => $query->where(
                    'available_stock',
                    '>',
                    0
                )
        );
    }

    public function scopeOutOfStock(
        Builder $query
    ): Builder {

        return $query->whereHas(
            'inventory',
            fn ($query)

                => $query->where(
                    'available_stock',
                    '<=',
                    0
                )
        );
    }

    public function scopeLatest(
        Builder $query
    ): Builder {

        return $query->latest();
    }

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(
            filled($keyword),

            fn (Builder $query)

                => $query->where(function ($q) use ($keyword) {

                    $q->where(
                        'sku',
                        'like',
                        "%{$keyword}%"
                    )

                    ->orWhere(
                        'barcode',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhereHas(
    'product',
    fn ($query) =>
        $query->where(
            'name',
            'like',
            "%{$keyword}%"
        )
);
                })
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

    public function isPublished(): bool
    {
        return !is_null(
            $this->published_at
        );
    }

    public function isDefault(): bool
    {
        return (bool) $this->is_default;
    }

    public function hasBarcode(): bool
    {
        return !empty(
            $this->barcode
        );
    }

    public function hasDiscount(): bool
    {
        return !empty(
            $this->compare_at_price
        )
        && $this->compare_at_price > $this->price;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function isInStock(): bool
    {
        return $this->availableStock() > 0;
    }

    public function isLowStock(): bool
    {
        if (!$this->inventory) {
            return false;
        }

        return $this->inventory->available_stock
            <= $this->inventory->minimum_stock;
    }

    public function availableStock(): int
    {
        return (int)
            ($this->inventory?->available_stock ?? 0);
    }

    public function productName(): ?string
    {
        return $this->product?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function publish(): void
    {
        $this->update([
            'published_at' => now(),
            'status' => self::STATUS_ACTIVE,
            'is_active' => true,
        ]);
    }

    public function archive(): void
    {
        $this->update([
            'status' => self::STATUS_ARCHIVED,
            'is_active' => false,
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