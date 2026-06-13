<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

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