<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class PaymentTransaction extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Transaction Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_PAYMENT = 'payment';

    public const TYPE_CAPTURE = 'capture';

    public const TYPE_SETTLEMENT = 'settlement';

    public const TYPE_REFUND = 'refund';

    public const TYPE_PARTIAL_REFUND = 'partial_refund';

    public const TYPE_CHARGEBACK = 'chargeback';

    public const TYPE_VOID = 'void';

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_EXPIRED = 'expired';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'payment_id',

        'transaction_id',

        'gateway_transaction_id',

        'gateway_order_id',

        'gateway',

        'method',

        'type',

        'amount',

        'fee_amount',

        'net_amount',

        'status',

        'reference_number',

        'request_payload',

        'response_payload',

        'notes',

        'processed_at',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'profit_loss_amount',

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

            'fee_amount' => 'decimal:2',

            'net_amount' => 'decimal:2',

            'processed_at' => 'datetime',

            'request_payload' => 'array',

            'response_payload' => 'array',

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

            ->useLogName(
                'payment_transaction'
            )

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Payment Transaction {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getProfitLossAmountAttribute(): float
    {
        return (float)
            $this->net_amount;
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->isSuccess();
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
                            'transaction_id',
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

                        ->orWhere(
                            'reference_number',
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

    public function scopeType(
        Builder $query,
        ?string $type
    ): Builder {

        return $query->when(

            filled($type),

            fn (Builder $query)

                => $query->where(
                    'type',
                    $type
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

    public function scopeSuccessful(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_SUCCESS
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

    public function scopePending(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_PENDING
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

    public function scopeExpired(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_EXPIRED
        );
    }

    public function scopeRefund(
        Builder $query
    ): Builder {

        return $query->whereIn(
            'type',
            [
                self::TYPE_REFUND,
                self::TYPE_PARTIAL_REFUND,
            ]
        );
    }

    public function scopeLatestProcessed(
        Builder $query
    ): Builder {

        return $query->latest(
            'processed_at'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isSuccess(): bool
    {
        return $this->status ===
            self::STATUS_SUCCESS;
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

    public function isPayment(): bool
    {
        return $this->type ===
            self::TYPE_PAYMENT;
    }

    public function isCapture(): bool
    {
        return $this->type ===
            self::TYPE_CAPTURE;
    }

    public function isSettlement(): bool
    {
        return $this->type ===
            self::TYPE_SETTLEMENT;
    }

    public function isRefund(): bool
    {
        return in_array(
            $this->type,
            [
                self::TYPE_REFUND,
                self::TYPE_PARTIAL_REFUND,
            ]
        );
    }

    public function isChargeback(): bool
    {
        return $this->type ===
            self::TYPE_CHARGEBACK;
    }

    public function isVoid(): bool
    {
        return $this->type ===
            self::TYPE_VOID;
    }

    public function hasGatewayTransaction(): bool
    {
        return !empty(
            $this->gateway_transaction_id
        );
    }

    public function hasRequestPayload(): bool
    {
        return !empty(
            $this->request_payload
        );
    }

    public function hasResponsePayload(): bool
    {
        return !empty(
            $this->response_payload
        );
    }

    public function paymentNumber(): ?string
    {
        return $this->payment?->payment_number;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function markSuccess(): void
    {
        $this->update([

            'status' =>
                self::STATUS_SUCCESS,

            'processed_at' =>
                now(),
        ]);

        if (
            in_array(
                $this->type,
                [
                    self::TYPE_PAYMENT,
                    self::TYPE_CAPTURE,
                    self::TYPE_SETTLEMENT,
                ]
            )
        ) {

            $this->payment?->markAsPaid(
                (float) $this->amount
            );
        }
    }

    public function markFailed(): void
    {
        $this->update([

            'status' =>
                self::STATUS_FAILED,

            'processed_at' =>
                now(),
        ]);
    }

    public function markCancelled(): void
    {
        $this->update([

            'status' =>
                self::STATUS_CANCELLED,

            'processed_at' =>
                now(),
        ]);
    }

    public function markExpired(): void
    {
        $this->update([

            'status' =>
                self::STATUS_EXPIRED,

            'processed_at' =>
                now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'transaction_id';
    }
}