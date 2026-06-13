<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ShipmentTracking extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Source Constants
    |--------------------------------------------------------------------------
    */

    public const SOURCE_SYSTEM = 'system';

    public const SOURCE_COURIER_API = 'courier_api';

    public const SOURCE_ADMIN = 'admin';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'shipment_id',

        'tracking_code',

        'tracking_event_code',

        'status',

        'location',

        'city',

        'province',

        'description',

        'courier_status',

        'courier_code',

        'latitude',

        'longitude',

        'event_sequence',

        'tracked_at',

        'processed_at',

        'is_latest',

        'is_customer_visible',

        'source',

        'payload',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'display_location',

        'is_processed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'latitude' => 'decimal:7',

            'longitude' => 'decimal:7',

            'event_sequence' => 'integer',

            'tracked_at' => 'datetime',

            'processed_at' => 'datetime',

            'is_latest' => 'boolean',

            'is_customer_visible' => 'boolean',

            'payload' => 'array',

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
                'shipment_tracking'
            )

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Shipment Tracking {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(
            Shipment::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayLocationAttribute(): ?string
    {
        $parts = array_filter([
            $this->location,
            $this->city,
            $this->province,
        ]);

        return empty($parts)
            ? null
            : implode(', ', $parts);
    }

    public function getIsProcessedAttribute(): bool
    {
        return ! is_null(
            $this->processed_at
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByShipment(
        Builder $query,
        ?int $shipmentId
    ): Builder {

        return $query->when(

            filled($shipmentId),

            fn (Builder $query)

                => $query->where(
                    'shipment_id',
                    $shipmentId
                )
        );
    }

    public function scopeLatestEvent(
        Builder $query
    ): Builder {

        return $query->where(
            'is_latest',
            true
        );
    }

    public function scopeCustomerVisible(
        Builder $query
    ): Builder {

        return $query->where(
            'is_customer_visible',
            true
        );
    }

    public function scopeCourierApi(
        Builder $query
    ): Builder {

        return $query->where(
            'source',
            self::SOURCE_COURIER_API
        );
    }

    public function scopeProcessed(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'processed_at'
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

    public function scopeTrackingCode(
        Builder $query,
        string $trackingCode
    ): Builder {

        return $query->where(
            'tracking_code',
            $trackingCode
        );
    }

    public function scopeTrackedBetween(
        Builder $query,
        $from,
        $to
    ): Builder {

        return $query->whereBetween(
            'tracked_at',
            [$from, $to]
        );
    }

    public function scopeLatest(
        Builder $query
    ): Builder {

        return $query->latest(
            'tracked_at'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isLatest(): bool
    {
        return (bool)
            $this->is_latest;
    }

    public function isCustomerVisible(): bool
    {
        return (bool)
            $this->is_customer_visible;
    }

    public function hasCoordinates(): bool
    {
        return ! is_null(
            $this->latitude
        )
        &&
        ! is_null(
            $this->longitude
        );
    }

    public function hasTrackingCode(): bool
    {
        return ! empty(
            $this->tracking_code
        );
    }

    public function hasEventCode(): bool
    {
        return ! empty(
            $this->tracking_event_code
        );
    }

    public function isProcessed(): bool
    {
        return ! is_null(
            $this->processed_at
        );
    }

    public function sourceLabel(): string
    {
        return match ($this->source) {

            self::SOURCE_SYSTEM =>
                'System',

            self::SOURCE_COURIER_API =>
                'Courier API',

            self::SOURCE_ADMIN =>
                'Admin',

            default =>
                ucfirst($this->source),
        };
    }

    public function coordinates(): ?string
    {
        if (! $this->hasCoordinates()) {
            return null;
        }

        return sprintf(
            '%s,%s',
            $this->latitude,
            $this->longitude
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function markAsLatest(): void
    {
        static::query()

            ->where(
                'shipment_id',
                $this->shipment_id
            )

            ->update([
                'is_latest' => false,
            ]);

        $this->update([
            'is_latest' => true,
        ]);
    }

    public function markProcessed(): void
    {
        $this->update([
            'processed_at' => now(),
        ]);
    }

    public function markUnprocessed(): void
    {
        $this->update([
            'processed_at' => null,
        ]);
    }

    public function hideFromCustomer(): void
    {
        $this->update([
            'is_customer_visible' => false,
        ]);
    }

    public function showToCustomer(): void
    {
        $this->update([
            'is_customer_visible' => true,
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