<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * CartItem Model
 *
 * Shopping Cart Line Item
 *
 * Laravel 13
 * PHP 8.4
 *
 * @property int $id
 * @property int $cart_id
 * @property int $product_sku_id
 * @property string $product_name
 * @property string $sku
 * @property string|null $thumbnail
 * @property numeric $price
 * @property int $quantity
 * @property numeric $subtotal
 * @property bool $is_available
 * @property bool $is_selected
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $added_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\Cart|null $cart
 * @property-read \App\Models\ProductSku|null $productSku
 * @method static Builder<static>|CartItem available()
 * @method static Builder<static>|CartItem byCart(?int $cartId)
 * @method static Builder<static>|CartItem bySku(?int $skuId)
 * @method static \Database\Factories\CartItemFactory factory($count = null, $state = [])
 * @method static Builder<static>|CartItem latestFirst()
 * @method static Builder<static>|CartItem newModelQuery()
 * @method static Builder<static>|CartItem newQuery()
 * @method static Builder<static>|CartItem onlyTrashed()
 * @method static Builder<static>|CartItem query()
 * @method static Builder<static>|CartItem selected()
 * @method static Builder<static>|CartItem selectedAvailable()
 * @method static Builder<static>|CartItem unavailable()
 * @method static Builder<static>|CartItem unselected()
 * @method static Builder<static>|CartItem whereAddedAt($value)
 * @method static Builder<static>|CartItem whereCartId($value)
 * @method static Builder<static>|CartItem whereCreatedAt($value)
 * @method static Builder<static>|CartItem whereDeletedAt($value)
 * @method static Builder<static>|CartItem whereId($value)
 * @method static Builder<static>|CartItem whereIsAvailable($value)
 * @method static Builder<static>|CartItem whereIsSelected($value)
 * @method static Builder<static>|CartItem whereNotes($value)
 * @method static Builder<static>|CartItem wherePrice($value)
 * @method static Builder<static>|CartItem whereProductName($value)
 * @method static Builder<static>|CartItem whereProductSkuId($value)
 * @method static Builder<static>|CartItem whereQuantity($value)
 * @method static Builder<static>|CartItem whereSku($value)
 * @method static Builder<static>|CartItem whereSubtotal($value)
 * @method static Builder<static>|CartItem whereThumbnail($value)
 * @method static Builder<static>|CartItem whereUpdatedAt($value)
 * @method static Builder<static>|CartItem withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|CartItem withoutTrashed()
 * @mixin \Eloquent
 */
class CartItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'cart_id',

        'product_sku_id',

        'product_name',

        'sku',

        'thumbnail',

        'price',

        'quantity',

        'subtotal',

        'is_available',

        'is_selected',

        'notes',

        'added_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'price' => 'decimal:2',

            'subtotal' => 'decimal:2',

            'quantity' => 'integer',

            'is_available' => 'boolean',

            'is_selected' => 'boolean',

            'added_at' => 'datetime',
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

            ->useLogName('cart_item')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Cart Item {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function cart(): BelongsTo
    {
        return $this->belongsTo(
            Cart::class
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

    public function thumbnailUrl(): ?string
    {
        if (!$this->thumbnail) {
            return null;
        }

        if (
            str_starts_with(
                $this->thumbnail,
                'http'
            )
        ) {
            return $this->thumbnail;
        }

        return asset(
            'storage/' .
            ltrim(
                $this->thumbnail,
                '/'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable(
        Builder $query
    ): Builder {

        return $query->where(
            'is_available',
            true
        );
    }

    public function scopeUnavailable(
        Builder $query
    ): Builder {

        return $query->where(
            'is_available',
            false
        );
    }

    public function scopeSelected(
        Builder $query
    ): Builder {

        return $query->where(
            'is_selected',
            true
        );
    }

    public function scopeUnselected(
        Builder $query
    ): Builder {

        return $query->where(
            'is_selected',
            false
        );
    }

    public function scopeSelectedAvailable(
        Builder $query
    ): Builder {

        return $query

            ->where(
                'is_selected',
                true
            )

            ->where(
                'is_available',
                true
            );
    }

    public function scopeByCart(
        Builder $query,
        ?int $cartId
    ): Builder {

        return $query->when(
            filled($cartId),

            fn (Builder $query)

                => $query->where(
                    'cart_id',
                    $cartId
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

    public function scopeLatestFirst(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'added_at'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isAvailable(): bool
    {
        return (bool)
            $this->is_available;
    }

    public function isSelected(): bool
    {
        return (bool)
            $this->is_selected;
    }

    public function hasThumbnail(): bool
    {
        return !empty(
            $this->thumbnail
        );
    }

    public function skuCode(): ?string
    {
        return $this->productSku?->sku;
    }

    public function productId(): ?int
    {
        return $this->productSku?->product_id;
    }

    public function product(): ?Product
    {
        return $this->productSku?->product;
    }

    public function inventory(): ?Inventory
    {
        return $this->productSku?->inventory;
    }

    public function lineTotal(): float
    {
        return (float)
            $this->subtotal;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function recalculateSubtotal(): void
    {
        $this->update([
            'subtotal' =>
                (float) $this->price
                * (int) $this->quantity,
        ]);

        $this->cart?->refreshSummary();
    }

    public function setQuantity(
        int $quantity
    ): void {

        $quantity = max(
            1,
            $quantity
        );

        $this->update([
            'quantity' => $quantity,
        ]);

        $this->recalculateSubtotal();
    }

    public function increaseQuantity(
        int $quantity = 1
    ): void {

        $this->setQuantity(
            $this->quantity + $quantity
        );
    }

    public function decreaseQuantity(
        int $quantity = 1
    ): void {

        $this->setQuantity(
            $this->quantity - $quantity
        );
    }

    public function select(): void
    {
        $this->update([
            'is_selected' => true,
        ]);
    }

    public function unselect(): void
    {
        $this->update([
            'is_selected' => false,
        ]);
    }

    public function markAvailable(): void
    {
        $this->update([
            'is_available' => true,
        ]);
    }

    public function markUnavailable(): void
    {
        $this->update([
            'is_available' => false,
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