<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * CheckoutSession Model
 *
 * Checkout Pipeline
 *
 * Laravel 13
 * PHP 8.4
 */
class CheckoutSession extends Model
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

    public const STATUS_READY = 'ready';

    public const STATUS_CHECKED_OUT = 'checked_out';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'customer_profile_id',

        'shipping_address_id',

        'session_code',

        'status',

        'voucher_code',

        'voucher_discount',

        'promotion_discount',

        'subtotal',

        'shipping_cost',

        'total_discount',

        'grand_total',

        'courier_code',

        'courier_service',

        'total_weight',

        'is_price_valid',

        'is_stock_valid',

        'is_voucher_valid',

        'notes',

        'expired_at',

        'checked_out_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'voucher_discount' => 'decimal:2',

            'promotion_discount' => 'decimal:2',

            'subtotal' => 'decimal:2',

            'shipping_cost' => 'decimal:2',

            'total_discount' => 'decimal:2',

            'grand_total' => 'decimal:2',

            'total_weight' => 'integer',

            'is_price_valid' => 'boolean',

            'is_stock_valid' => 'boolean',

            'is_voucher_valid' => 'boolean',

            'expired_at' => 'datetime',

            'checked_out_at' => 'datetime',
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

            ->useLogName('checkout_session')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Checkout Session {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(
            CustomerProfile::class
        );
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(
            CustomerAddress::class,
            'shipping_address_id'
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            CheckoutItem::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsExpiredAttribute(): bool
    {
        return $this->expired_at?->isPast()
            ?? false;
    }

    public function getIsReadyAttribute(): bool
    {
        return $this->status ===
            self::STATUS_READY;
    }

    public function getIsCheckedOutAttribute(): bool
    {
        return $this->status ===
            self::STATUS_CHECKED_OUT;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDraft(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_DRAFT
        );
    }

    public function scopeReady(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_READY
        );
    }

    public function scopeCheckedOut(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_CHECKED_OUT
        );
    }

    public function scopeExpired(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_EXPIRED
        );
    }

    public function scopeActive(
        Builder $query
    ): Builder {

        return $query->whereIn(
            'status',
            [
                self::STATUS_DRAFT,
                self::STATUS_READY,
            ]
        );
    }

    public function scopeValid(
        Builder $query
    ): Builder {

        return $query

            ->where(
                'is_price_valid',
                true
            )

            ->where(
                'is_stock_valid',
                true
            )

            ->where(
                'is_voucher_valid',
                true
            );
    }

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

    public function isExpired(): bool
    {
        return $this->expired_at?->isPast()
            ?? false;
    }

    public function isReady(): bool
    {
        return $this->status ===
            self::STATUS_READY;
    }

    public function isCheckedOut(): bool
    {
        return $this->status ===
            self::STATUS_CHECKED_OUT;
    }

    public function hasAddress(): bool
    {
        return !is_null(
            $this->shipping_address_id
        );
    }

    public function hasCourier(): bool
    {
        return filled(
            $this->courier_code
        );
    }

    public function customerName(): ?string
    {
        return $this->customerProfile?->full_name;
    }

    public function customerEmail(): ?string
    {
        return $this->customerProfile?->user?->email;
    }

    public function totalItems(): int
    {
        return (int)
            $this->items()
                ->sum('quantity');
    }

    public function canCheckout(): bool
    {
        return $this->status === self::STATUS_READY
            && $this->is_price_valid
            && $this->is_stock_valid
            && $this->is_voucher_valid
            && !$this->isExpired()
            && $this->hasAddress()
            && $this->hasCourier();
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function recalculateTotals(): void
    {
        $subtotal = (float)
            $this->items()->sum('subtotal');

        $discount =
            (float) $this->voucher_discount
            +
            (float) $this->promotion_discount;

        $grandTotal =
            $subtotal
            +
            (float) $this->shipping_cost
            -
            $discount;

        $this->update([
            'subtotal' => $subtotal,
            'total_discount' => $discount,
            'grand_total' => max(
                0,
                $grandTotal
            ),
        ]);
    }

    public function markReady(): void
    {
        $this->update([
            'status' => self::STATUS_READY,
        ]);
    }

    public function markCheckedOut(): void
    {
        $this->update([
            'status' => self::STATUS_CHECKED_OUT,
            'checked_out_at' => now(),
        ]);
    }

    public function markExpired(): void
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    public function invalidateStock(): void
    {
        $this->update([
            'is_stock_valid' => false,
        ]);
    }

    public function invalidatePrice(): void
    {
        $this->update([
            'is_price_valid' => false,
        ]);
    }

    public function invalidateVoucher(): void
    {
        $this->update([
            'is_voucher_valid' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'session_code';
    }
}