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
 * CheckoutItem Model
 *
 * Checkout Product Snapshot
 *
 * Laravel 13
 * PHP 8.4
 */
class CheckoutItem extends Model
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

        'checkout_session_id',

        'product_sku_id',

        'product_name',

        'sku',

        'thumbnail',

        'price',

        'quantity',

        'subtotal',

        'discount_amount',

        'final_price',

        'is_available',

        'is_valid_price',

        'is_valid_stock',

        'notes',

        'added_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appended Attributes
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'thumbnail_url',

        'formatted_price',

        'formatted_subtotal',

        'formatted_final_price',

        'formatted_discount',
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

            'discount_amount' => 'decimal:2',

            'final_price' => 'decimal:2',

            'quantity' => 'integer',

            'is_available' => 'boolean',

            'is_valid_price' => 'boolean',

            'is_valid_stock' => 'boolean',

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

            ->useLogName('checkout_item')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Checkout Item {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function checkoutSession(): BelongsTo
    {
        return $this->belongsTo(
            CheckoutSession::class
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

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail
            ? asset('storage/' . $this->thumbnail)
            : null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format(
            (float) $this->price,
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

    public function getFormattedFinalPriceAttribute(): string
    {
        return number_format(
            (float) $this->final_price,
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

    public function scopeValid(
        Builder $query
    ): Builder {

        return $query
            ->where(
                'is_valid_price',
                true
            )
            ->where(
                'is_valid_stock',
                true
            );
    }

    public function scopeValidPrice(
        Builder $query
    ): Builder {

        return $query->where(
            'is_valid_price',
            true
        );
    }

    public function scopeInvalidPrice(
        Builder $query
    ): Builder {

        return $query->where(
            'is_valid_price',
            false
        );
    }

    public function scopeValidStock(
        Builder $query
    ): Builder {

        return $query->where(
            'is_valid_stock',
            true
        );
    }

    public function scopeInvalidStock(
        Builder $query
    ): Builder {

        return $query->where(
            'is_valid_stock',
            false
        );
    }

    public function scopeBySession(
        Builder $query,
        ?int $sessionId
    ): Builder {

        return $query->when(
            filled($sessionId),

            fn (Builder $query)
                => $query->where(
                    'checkout_session_id',
                    $sessionId
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

    public function hasValidPrice(): bool
    {
        return (bool)
            $this->is_valid_price;
    }

    public function hasValidStock(): bool
    {
        return (bool)
            $this->is_valid_stock;
    }

    public function hasDiscount(): bool
    {
        return (float)
            $this->discount_amount > 0;
    }

    public function hasThumbnail(): bool
    {
        return !empty(
            $this->thumbnail
        );
    }

    public function hasNotes(): bool
    {
        return !empty(
            $this->notes
        );
    }

    public function productId(): ?int
    {
        return $this->productSku?->product_id;
    }

    public function productName(): string
    {
        return $this->product_name;
    }

    public function skuCode(): ?string
    {
        return $this->productSku?->sku;
    }

    public function finalUnitPrice(): float
    {
        if ($this->quantity <= 0) {
            return 0;
        }

        return round(
            (float) $this->final_price
            / $this->quantity,
            2
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function recalculateTotals(): void
    {
        $subtotal =
            (float) $this->price
            *
            $this->quantity;

        $finalPrice =
            $subtotal
            -
            (float) $this->discount_amount;

        $this->update([

            'subtotal' => $subtotal,

            'final_price' => max(
                0,
                $finalPrice
            ),
        ]);

        $this->checkoutSession?->recalculateTotals();
    }

    public function validatePrice(): void
    {
        $this->update([
            'is_valid_price' => true,
        ]);
    }

    public function invalidatePrice(): void
    {
        $this->update([
            'is_valid_price' => false,
        ]);
    }

    public function validateStock(): void
    {
        $this->update([
            'is_valid_stock' => true,
        ]);
    }

    public function invalidateStock(): void
    {
        $this->update([
            'is_valid_stock' => false,
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