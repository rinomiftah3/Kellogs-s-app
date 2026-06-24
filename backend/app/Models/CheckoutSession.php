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
 *
 * @property int $id
 * @property int $customer_profile_id
 * @property int|null $shipping_address_id
 * @property string $session_code
 * @property string $status
 * @property string|null $voucher_code
 * @property numeric $voucher_discount
 * @property numeric $promotion_discount
 * @property numeric $subtotal
 * @property numeric $shipping_cost
 * @property numeric $total_discount
 * @property numeric $grand_total
 * @property string|null $courier_code
 * @property string|null $courier_service
 * @property int $total_weight
 * @property bool $is_price_valid
 * @property bool $is_stock_valid
 * @property bool $is_voucher_valid
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $expired_at
 * @property \Illuminate\Support\Carbon|null $checked_out_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\CustomerProfile|null $customerProfile
 * @property-read bool $is_checked_out
 * @property-read bool $is_expired
 * @property-read bool $is_ready
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CheckoutItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\CustomerAddress|null $shippingAddress
 * @method static Builder<static>|CheckoutSession active()
 * @method static Builder<static>|CheckoutSession byCustomer(?int $customerId)
 * @method static Builder<static>|CheckoutSession checkedOut()
 * @method static Builder<static>|CheckoutSession draft()
 * @method static Builder<static>|CheckoutSession expired()
 * @method static \Database\Factories\CheckoutSessionFactory factory($count = null, $state = [])
 * @method static Builder<static>|CheckoutSession latestFirst()
 * @method static Builder<static>|CheckoutSession newModelQuery()
 * @method static Builder<static>|CheckoutSession newQuery()
 * @method static Builder<static>|CheckoutSession onlyTrashed()
 * @method static Builder<static>|CheckoutSession query()
 * @method static Builder<static>|CheckoutSession ready()
 * @method static Builder<static>|CheckoutSession valid()
 * @method static Builder<static>|CheckoutSession whereCheckedOutAt($value)
 * @method static Builder<static>|CheckoutSession whereCourierCode($value)
 * @method static Builder<static>|CheckoutSession whereCourierService($value)
 * @method static Builder<static>|CheckoutSession whereCreatedAt($value)
 * @method static Builder<static>|CheckoutSession whereCustomerProfileId($value)
 * @method static Builder<static>|CheckoutSession whereDeletedAt($value)
 * @method static Builder<static>|CheckoutSession whereExpiredAt($value)
 * @method static Builder<static>|CheckoutSession whereGrandTotal($value)
 * @method static Builder<static>|CheckoutSession whereId($value)
 * @method static Builder<static>|CheckoutSession whereIsPriceValid($value)
 * @method static Builder<static>|CheckoutSession whereIsStockValid($value)
 * @method static Builder<static>|CheckoutSession whereIsVoucherValid($value)
 * @method static Builder<static>|CheckoutSession whereNotes($value)
 * @method static Builder<static>|CheckoutSession wherePromotionDiscount($value)
 * @method static Builder<static>|CheckoutSession whereSessionCode($value)
 * @method static Builder<static>|CheckoutSession whereShippingAddressId($value)
 * @method static Builder<static>|CheckoutSession whereShippingCost($value)
 * @method static Builder<static>|CheckoutSession whereStatus($value)
 * @method static Builder<static>|CheckoutSession whereSubtotal($value)
 * @method static Builder<static>|CheckoutSession whereTotalDiscount($value)
 * @method static Builder<static>|CheckoutSession whereTotalWeight($value)
 * @method static Builder<static>|CheckoutSession whereUpdatedAt($value)
 * @method static Builder<static>|CheckoutSession whereVoucherCode($value)
 * @method static Builder<static>|CheckoutSession whereVoucherDiscount($value)
 * @method static Builder<static>|CheckoutSession withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|CheckoutSession withoutTrashed()
 * @mixin \Eloquent
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