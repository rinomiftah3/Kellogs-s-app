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
 * @property int $id
 * @property int $order_id
 * @property string $payment_number
 * @property string $gateway
 * @property string $method
 * @property numeric $amount
 * @property numeric $paid_amount
 * @property numeric $fee_amount
 * @property numeric $refund_amount
 * @property string $status
 * @property string|null $gateway_transaction_id
 * @property string|null $gateway_order_id
 * @property string|null $payment_url
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $expired_at
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaymentCallback> $callbacks
 * @property-read int|null $callbacks_count
 * @property-read bool $is_successful
 * @property-read float $net_amount
 * @property-read float $remaining_amount
 * @property-read \App\Models\Order|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaymentTransaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Payment failed()
 * @method static Builder<static>|Payment gateway(?string $gateway)
 * @method static Builder<static>|Payment newModelQuery()
 * @method static Builder<static>|Payment newQuery()
 * @method static Builder<static>|Payment onlyTrashed()
 * @method static Builder<static>|Payment paid()
 * @method static Builder<static>|Payment pending()
 * @method static Builder<static>|Payment query()
 * @method static Builder<static>|Payment refunded()
 * @method static Builder<static>|Payment search(?string $keyword)
 * @method static Builder<static>|Payment status(?string $status)
 * @method static Builder<static>|Payment whereAmount($value)
 * @method static Builder<static>|Payment whereCreatedAt($value)
 * @method static Builder<static>|Payment whereDeletedAt($value)
 * @method static Builder<static>|Payment whereExpiredAt($value)
 * @method static Builder<static>|Payment whereFeeAmount($value)
 * @method static Builder<static>|Payment whereGateway($value)
 * @method static Builder<static>|Payment whereGatewayOrderId($value)
 * @method static Builder<static>|Payment whereGatewayTransactionId($value)
 * @method static Builder<static>|Payment whereId($value)
 * @method static Builder<static>|Payment whereMetadata($value)
 * @method static Builder<static>|Payment whereMethod($value)
 * @method static Builder<static>|Payment whereNotes($value)
 * @method static Builder<static>|Payment whereOrderId($value)
 * @method static Builder<static>|Payment wherePaidAmount($value)
 * @method static Builder<static>|Payment wherePaidAt($value)
 * @method static Builder<static>|Payment wherePaymentNumber($value)
 * @method static Builder<static>|Payment wherePaymentUrl($value)
 * @method static Builder<static>|Payment whereRefundAmount($value)
 * @method static Builder<static>|Payment whereStatus($value)
 * @method static Builder<static>|Payment whereUpdatedAt($value)
 * @method static Builder<static>|Payment withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Payment withoutTrashed()
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_REFUNDED = 'refunded';

    public const STATUS_PARTIAL_REFUND = 'partial_refund';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'order_id',

        'payment_number',

        'gateway',

        'method',

        'amount',

        'paid_amount',

        'fee_amount',

        'refund_amount',

        'status',

        'gateway_transaction_id',

        'gateway_order_id',

        'payment_url',

        'paid_at',

        'expired_at',

        'metadata',

        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'net_amount',

        'remaining_amount',

        'is_successful',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'amount' => 'decimal:2',

            'paid_amount' => 'decimal:2',

            'fee_amount' => 'decimal:2',

            'refund_amount' => 'decimal:2',

            'paid_at' => 'datetime',

            'expired_at' => 'datetime',

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

            ->useLogName('payment')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Payment {$eventName}"
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

    public function transactions(): HasMany
    {
        return $this->hasMany(
            PaymentTransaction::class
        );
    }

    public function callbacks(): HasMany
    {
        return $this->hasMany(
            PaymentCallback::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getNetAmountAttribute(): float
    {
        return max(
            0,
            (float) $this->paid_amount
            - (float) $this->fee_amount
        );
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(
            0,
            (float) $this->amount
            - (float) $this->paid_amount
        );
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->isSuccessful();
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
                            'payment_number',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'gateway_transaction_id',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'gateway_order_id',
                            'like',
                            "%{$keyword}%"
                        )
                )
        );
    }

    public function scopeStatus(
        Builder $query,
        ?string $status
    ): Builder {

        return $query->when(

            filled($status),

            fn (Builder $query)

                => $query->where(
                    'status',
                    $status
                )
        );
    }

    public function scopeGateway(
        Builder $query,
        ?string $gateway
    ): Builder {

        return $query->when(

            filled($gateway),

            fn (Builder $query)

                => $query->where(
                    'gateway',
                    $gateway
                )
        );
    }

    public function scopePaid(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_PAID
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

    public function scopeFailed(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_FAILED
        );
    }

    public function scopeRefunded(
        Builder $query
    ): Builder {

        return $query->whereIn(
            'status',
            [
                self::STATUS_REFUNDED,
                self::STATUS_PARTIAL_REFUND,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPaid(): bool
    {
        return $this->status ===
            self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->status ===
            self::STATUS_PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status ===
            self::STATUS_FAILED;
    }

    public function isCancelled(): bool
    {
        return $this->status ===
            self::STATUS_CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status ===
            self::STATUS_EXPIRED;
    }

    public function isPartialRefund(): bool
    {
        return $this->status ===
            self::STATUS_PARTIAL_REFUND;
    }

    public function isRefunded(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_REFUNDED,
                self::STATUS_PARTIAL_REFUND,
            ]
        );
    }

    public function isSuccessful(): bool
    {
        return $this->isPaid();
    }

    public function isFullyPaid(): bool
    {
        return (float) $this->paid_amount
            >= (float) $this->amount;
    }

    public function isOverPaid(): bool
    {
        return (float) $this->paid_amount
            > (float) $this->amount;
    }

    public function hasRefund(): bool
    {
        return (float) $this->refund_amount > 0;
    }

    public function hasPaymentUrl(): bool
    {
        return !empty(
            $this->payment_url
        );
    }

    public function hasGatewayTransaction(): bool
    {
        return !empty(
            $this->gateway_transaction_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function markAsPaid(
        ?float $paidAmount = null
    ): void {

        $this->update([

            'status' => self::STATUS_PAID,

            'paid_amount' =>
                $paidAmount ?? $this->amount,

            'paid_at' => now(),
        ]);

        $this->order?->markAsPaid();
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    public function markAsRefunded(
        float $amount
    ): void {

        $this->update([

            'refund_amount' => $amount,

            'status' =>

                $amount >= $this->paid_amount

                    ? self::STATUS_REFUNDED

                    : self::STATUS_PARTIAL_REFUND,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'payment_number';
    }
}