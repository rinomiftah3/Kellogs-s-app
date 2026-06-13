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
 * Wishlist Model
 *
 * Customer Wishlist
 * Enterprise Ready
 */
class Wishlist extends Model
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

        'customer_profile_id',

        'product_id',

        'added_at',

        'notify_price_drop',

        'notify_back_in_stock',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'added_at' => 'datetime',

            'notify_price_drop' => 'boolean',

            'notify_back_in_stock' => 'boolean',
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

            ->useLogName('wishlist')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Wishlist {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            CustomerProfile::class,
            'customer_profile_id'
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
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByCustomer(
        Builder $query,
        ?int $customerId
    ): Builder {

        return $query->when(
            filled($customerId),

            fn (Builder $query)

                => $query->where(
                    'customer_profile_id',
                    $customerId
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

    public function scopeNotifyPriceDrop(
        Builder $query
    ): Builder {

        return $query->where(
            'notify_price_drop',
            true
        );
    }

    public function scopeNotifyBackInStock(
        Builder $query
    ): Builder {

        return $query->where(
            'notify_back_in_stock',
            true
        );
    }

    public function scopeWithNotifications(
        Builder $query
    ): Builder {

        return $query->where(function (
            Builder $query
        ) {

            $query->where(
                'notify_price_drop',
                true
            )

            ->orWhere(
                'notify_back_in_stock',
                true
            );
        });
    }

    public function scopeLatestAdded(
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

    public function customerName(): ?string
    {
        return $this->customer?->full_name;
    }

    public function productName(): ?string
    {
        return $this->product?->name;
    }

    public function isPriceDropNotificationEnabled(): bool
    {
        return (bool)
            $this->notify_price_drop;
    }

    public function isBackInStockNotificationEnabled(): bool
    {
        return (bool)
            $this->notify_back_in_stock;
    }

    public function productPrice(): ?float
    {
        $sku = $this->product?->skus()
            ->where('is_default', true)
            ->first();

        return $sku
            ? (float) $sku->price
            : null;
    }

    public function productThumbnail(): ?string
    {
        return $this->product?->thumbnail;
    }

    public function addedDate(): ?string
    {
        return $this->added_at
            ? $this->added_at
                ->format('Y-m-d H:i:s')
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function enablePriceDropNotification(): bool
    {
        return $this->update([
            'notify_price_drop' => true,
        ]);
    }

    public function disablePriceDropNotification(): bool
    {
        return $this->update([
            'notify_price_drop' => false,
        ]);
    }

    public function enableBackInStockNotification(): bool
    {
        return $this->update([
            'notify_back_in_stock' => true,
        ]);
    }

    public function disableBackInStockNotification(): bool
    {
        return $this->update([
            'notify_back_in_stock' => false,
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