<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Order Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Payment Status
    |--------------------------------------------------------------------------
    */

    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_REFUNDED = 'refunded';

    /*
    |--------------------------------------------------------------------------
    | Fulfillment Status
    |--------------------------------------------------------------------------
    */

    public const FULFILLMENT_PENDING = 'pending';

    public const FULFILLMENT_PACKED = 'packed';

    public const FULFILLMENT_SHIPPED = 'shipped';

    public const FULFILLMENT_DELIVERED = 'delivered';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'customer_profile_id',

        'shipping_address_id',

        'order_number',

        'status',

        'payment_status',

        'fulfillment_status',

        'customer_name',

        'customer_email',

        'customer_phone',

        'recipient_name',

        'recipient_phone',

        'shipping_address',

        'province',

        'city',

        'district',

        'postal_code',

        'subtotal',

        'shipping_cost',

        'discount_amount',

        'tax_amount',

        'grand_total',

        'voucher_code',

        'voucher_discount',

        'courier_code',

        'courier_service',

        'tracking_number',

        'total_weight',

        'customer_notes',

        'admin_notes',

        'ordered_at',

        'paid_at',

        'shipped_at',

        'completed_at',

        'cancelled_at',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'formatted_total',

        'item_count',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'subtotal' => 'decimal:2',

            'shipping_cost' => 'decimal:2',

            'discount_amount' => 'decimal:2',

            'tax_amount' => 'decimal:2',

            'grand_total' => 'decimal:2',

            'voucher_discount' => 'decimal:2',

            'total_weight' => 'integer',

            'metadata' => 'array',

            'ordered_at' => 'datetime',

            'paid_at' => 'datetime',

            'shipped_at' => 'datetime',

            'completed_at' => 'datetime',

            'cancelled_at' => 'datetime',
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

            ->useLogName('order')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Order {$eventName}"
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
            OrderItem::class
        );
    }

    public function histories(): HasMany
    {
        return $this->hasMany(
            OrderHistory::class
        );
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(
            OrderStatusLog::class
        );
    }

    public function payment(): HasOne
    {
        return $this->hasOne(
            Payment::class
        );
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(
            Shipment::class
        );
    }

    public function voucherUsage(): HasOne
    {
        return $this->hasOne(
            VoucherUsage::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedTotalAttribute(): string
    {
        return number_format(
            (float) $this->grand_total,
            0,
            ',',
            '.'
        );
    }

    public function getItemCountAttribute(): int
    {
        return (int) (

            $this->relationLoaded('items')

                ? $this->items->sum('quantity')

                : $this->items()->sum('quantity')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(

            filled($keyword),

            fn (Builder $query)

                => $query->where(

                    fn ($q)

                        => $q->where(
                            'order_number',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'customer_name',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'customer_email',
                            'like',
                            "%{$keyword}%"
                        )
                )
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

    public function scopePending(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }

    public function scopeConfirmed(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_CONFIRMED
        );
    }

    public function scopeProcessing(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_PROCESSING
        );
    }

    public function scopeShipped(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_SHIPPED
        );
    }

    public function scopeCompleted(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_COMPLETED
        );
    }

    public function scopeCancelled(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_CANCELLED
        );
    }

    public function scopePaymentPending(
        Builder $query
    ): Builder {

        return $query->where(
            'payment_status',
            self::PAYMENT_PENDING
        );
    }

    public function scopePaymentPaid(
        Builder $query
    ): Builder {

        return $query->where(
            'payment_status',
            self::PAYMENT_PAID
        );
    }

    public function scopePaymentFailed(
        Builder $query
    ): Builder {

        return $query->where(
            'payment_status',
            self::PAYMENT_FAILED
        );
    }

    public function scopePaymentRefunded(
        Builder $query
    ): Builder {

        return $query->where(
            'payment_status',
            self::PAYMENT_REFUNDED
        );
    }

    public function scopePacked(
        Builder $query
    ): Builder {

        return $query->where(
            'fulfillment_status',
            self::FULFILLMENT_PACKED
        );
    }

    public function scopeDelivered(
        Builder $query
    ): Builder {

        return $query->where(
            'fulfillment_status',
            self::FULFILLMENT_DELIVERED
        );
    }

    public function scopeLatestFirst(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'ordered_at'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPaid(): bool
    {
        return $this->payment_status
            === self::PAYMENT_PAID;
    }

    public function isPending(): bool
    {
        return $this->status
            === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status
            === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status
            === self::STATUS_CANCELLED;
    }

    public function hasShipment(): bool
    {
        return $this->shipment()
            ->exists();
    }

    public function hasPayment(): bool
    {
        return $this->payment()
            ->exists();
    }

    public function customerDisplay(): string
    {
        return $this->customer_name;
    }

    public function grandTotalAmount(): float
    {
        return (float)
            $this->grand_total;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Methods
    |--------------------------------------------------------------------------
    */

    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => self::PAYMENT_PAID,
            'status' => self::STATUS_CONFIRMED,
            'paid_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'fulfillment_status'
                => self::FULFILLMENT_DELIVERED,
            'completed_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        if ($this->isPaid()) {
            return;
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }
}