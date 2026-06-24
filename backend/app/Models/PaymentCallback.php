<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * @property int $id
 * @property int $payment_id
 * @property string $gateway
 * @property string|null $event_type
 * @property string|null $gateway_transaction_id
 * @property string|null $gateway_order_id
 * @property string $status
 * @property string|null $http_method
 * @property int|null $http_status
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $signature
 * @property bool $signature_valid
 * @property array<array-key, mixed>|null $headers
 * @property array<array-key, mixed>|null $payload
 * @property array<array-key, mixed>|null $processing_result
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon $received_at
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read bool $is_processed
 * @property-read bool $is_verified
 * @property-read \App\Models\Payment|null $payment
 * @method static Builder<static>|PaymentCallback failed()
 * @method static Builder<static>|PaymentCallback gateway(?string $gateway)
 * @method static Builder<static>|PaymentCallback ignored()
 * @method static Builder<static>|PaymentCallback latestReceived()
 * @method static Builder<static>|PaymentCallback newModelQuery()
 * @method static Builder<static>|PaymentCallback newQuery()
 * @method static Builder<static>|PaymentCallback processed()
 * @method static Builder<static>|PaymentCallback query()
 * @method static Builder<static>|PaymentCallback search(?string $keyword)
 * @method static Builder<static>|PaymentCallback status(?string $status)
 * @method static Builder<static>|PaymentCallback verified()
 * @method static Builder<static>|PaymentCallback whereCreatedAt($value)
 * @method static Builder<static>|PaymentCallback whereErrorMessage($value)
 * @method static Builder<static>|PaymentCallback whereEventType($value)
 * @method static Builder<static>|PaymentCallback whereGateway($value)
 * @method static Builder<static>|PaymentCallback whereGatewayOrderId($value)
 * @method static Builder<static>|PaymentCallback whereGatewayTransactionId($value)
 * @method static Builder<static>|PaymentCallback whereHeaders($value)
 * @method static Builder<static>|PaymentCallback whereHttpMethod($value)
 * @method static Builder<static>|PaymentCallback whereHttpStatus($value)
 * @method static Builder<static>|PaymentCallback whereId($value)
 * @method static Builder<static>|PaymentCallback whereIpAddress($value)
 * @method static Builder<static>|PaymentCallback whereMetadata($value)
 * @method static Builder<static>|PaymentCallback wherePayload($value)
 * @method static Builder<static>|PaymentCallback wherePaymentId($value)
 * @method static Builder<static>|PaymentCallback whereProcessedAt($value)
 * @method static Builder<static>|PaymentCallback whereProcessingResult($value)
 * @method static Builder<static>|PaymentCallback whereReceivedAt($value)
 * @method static Builder<static>|PaymentCallback whereSignature($value)
 * @method static Builder<static>|PaymentCallback whereSignatureValid($value)
 * @method static Builder<static>|PaymentCallback whereStatus($value)
 * @method static Builder<static>|PaymentCallback whereUpdatedAt($value)
 * @method static Builder<static>|PaymentCallback whereUserAgent($value)
 * @mixin \Eloquent
 */
class PaymentCallback extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_RECEIVED = 'received';

    public const STATUS_PROCESSED = 'processed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_IGNORED = 'ignored';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'payment_id',

        'gateway',

        'event_type',

        'gateway_transaction_id',

        'gateway_order_id',

        'status',

        'http_method',

        'http_status',

        'ip_address',

        'user_agent',

        'signature',

        'signature_valid',

        'headers',

        'payload',

        'processing_result',

        'error_message',

        'received_at',

        'processed_at',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'is_processed',

        'is_verified',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'signature_valid' => 'boolean',

            'http_status' => 'integer',

            'headers' => 'array',

            'payload' => 'array',

            'processing_result' => 'array',

            'metadata' => 'array',

            'received_at' => 'datetime',

            'processed_at' => 'datetime',
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
                'payment_callback'
            )

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Payment Callback {$eventName}"
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

    public function getIsProcessedAttribute(): bool
    {
        return $this->isProcessed();
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->isVerified();
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
                            'event_type',
                            'like',
                            "%{$keyword}%"
                        )
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

    public function scopeProcessed(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_PROCESSED
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

    public function scopeIgnored(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_IGNORED
        );
    }

    public function scopeVerified(
        Builder $query
    ): Builder {

        return $query->where(
            'signature_valid',
            true
        );
    }

    public function scopeLatestReceived(
        Builder $query
    ): Builder {

        return $query->latest(
            'received_at'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isReceived(): bool
    {
        return $this->status ===
            self::STATUS_RECEIVED;
    }

    public function isProcessed(): bool
    {
        return $this->status ===
            self::STATUS_PROCESSED;
    }

    public function isFailed(): bool
    {
        return $this->status ===
            self::STATUS_FAILED;
    }

    public function isIgnored(): bool
    {
        return $this->status ===
            self::STATUS_IGNORED;
    }

    public function isVerified(): bool
    {
        return (bool)
            $this->signature_valid;
    }

    public function hasError(): bool
    {
        return !empty(
            $this->error_message
        );
    }

    public function hasPayload(): bool
    {
        return !empty(
            $this->payload
        );
    }

    public function hasHeaders(): bool
    {
        return !empty(
            $this->headers
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

    public function markProcessed(
        array $result = []
    ): void {

        $this->update([

            'status' =>
                self::STATUS_PROCESSED,

            'processed_at' =>
                now(),

            'processing_result' =>
                $result,
        ]);
    }

    public function markFailed(
        ?string $message = null
    ): void {

        $this->update([

            'status' =>
                self::STATUS_FAILED,

            'error_message' =>
                $message,

            'processed_at' =>
                now(),
        ]);
    }

    public function markIgnored(): void
    {
        $this->update([

            'status' =>
                self::STATUS_IGNORED,

            'processed_at' =>
                now(),
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