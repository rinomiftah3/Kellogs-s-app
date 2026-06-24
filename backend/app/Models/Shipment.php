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
 * @property int $id
 * @property int $order_id
 * @property int $shipping_method_id
 * @property string $shipment_number
 * @property string|null $tracking_number
 * @property string $courier_name
 * @property string $courier_code
 * @property string $service_name
 * @property string $service_code
 * @property string|null $tracking_url
 * @property numeric $shipping_cost
 * @property numeric $insurance_cost
 * @property bool $is_insured
 * @property numeric $weight
 * @property int $item_count
 * @property string $status
 * @property string $recipient_name
 * @property string $recipient_phone
 * @property string $recipient_address
 * @property string $recipient_city
 * @property string $recipient_province
 * @property string $recipient_postal_code
 * @property \Illuminate\Support\Carbon|null $pickup_at
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $estimated_delivery_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property int $delivery_attempts
 * @property int|null $delivery_duration_hours
 * @property \Illuminate\Support\Carbon|null $last_tracking_sync_at
 * @property string|null $received_by
 * @property string|null $signed_proof
 * @property string|null $failed_reason
 * @property string|null $return_reason
 * @property string|null $notes
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read bool $is_cancelled
 * @property-read bool $is_delivered
 * @property-read bool $is_in_transit
 * @property-read string|null $tracking_link
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\ShippingMethod|null $shippingMethod
 * @method static Builder<static>|Shipment byTrackingNumber(?string $trackingNumber)
 * @method static Builder<static>|Shipment cancelled()
 * @method static Builder<static>|Shipment delivered()
 * @method static \Database\Factories\ShipmentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Shipment inTransit()
 * @method static Builder<static>|Shipment latest()
 * @method static Builder<static>|Shipment newModelQuery()
 * @method static Builder<static>|Shipment newQuery()
 * @method static Builder<static>|Shipment onlyTrashed()
 * @method static Builder<static>|Shipment pending()
 * @method static Builder<static>|Shipment query()
 * @method static Builder<static>|Shipment readyToShip()
 * @method static Builder<static>|Shipment returned()
 * @method static Builder<static>|Shipment search(?string $keyword)
 * @method static Builder<static>|Shipment whereCourierCode($value)
 * @method static Builder<static>|Shipment whereCourierName($value)
 * @method static Builder<static>|Shipment whereCreatedAt($value)
 * @method static Builder<static>|Shipment whereDeletedAt($value)
 * @method static Builder<static>|Shipment whereDeliveredAt($value)
 * @method static Builder<static>|Shipment whereDeliveryAttempts($value)
 * @method static Builder<static>|Shipment whereDeliveryDurationHours($value)
 * @method static Builder<static>|Shipment whereEstimatedDeliveryAt($value)
 * @method static Builder<static>|Shipment whereFailedReason($value)
 * @method static Builder<static>|Shipment whereId($value)
 * @method static Builder<static>|Shipment whereInsuranceCost($value)
 * @method static Builder<static>|Shipment whereIsInsured($value)
 * @method static Builder<static>|Shipment whereItemCount($value)
 * @method static Builder<static>|Shipment whereLastTrackingSyncAt($value)
 * @method static Builder<static>|Shipment whereMetadata($value)
 * @method static Builder<static>|Shipment whereNotes($value)
 * @method static Builder<static>|Shipment whereOrderId($value)
 * @method static Builder<static>|Shipment wherePickupAt($value)
 * @method static Builder<static>|Shipment whereReceivedBy($value)
 * @method static Builder<static>|Shipment whereRecipientAddress($value)
 * @method static Builder<static>|Shipment whereRecipientCity($value)
 * @method static Builder<static>|Shipment whereRecipientName($value)
 * @method static Builder<static>|Shipment whereRecipientPhone($value)
 * @method static Builder<static>|Shipment whereRecipientPostalCode($value)
 * @method static Builder<static>|Shipment whereRecipientProvince($value)
 * @method static Builder<static>|Shipment whereReturnReason($value)
 * @method static Builder<static>|Shipment whereServiceCode($value)
 * @method static Builder<static>|Shipment whereServiceName($value)
 * @method static Builder<static>|Shipment whereShipmentNumber($value)
 * @method static Builder<static>|Shipment whereShippedAt($value)
 * @method static Builder<static>|Shipment whereShippingCost($value)
 * @method static Builder<static>|Shipment whereShippingMethodId($value)
 * @method static Builder<static>|Shipment whereSignedProof($value)
 * @method static Builder<static>|Shipment whereStatus($value)
 * @method static Builder<static>|Shipment whereTrackingNumber($value)
 * @method static Builder<static>|Shipment whereTrackingUrl($value)
 * @method static Builder<static>|Shipment whereUpdatedAt($value)
 * @method static Builder<static>|Shipment whereWeight($value)
 * @method static Builder<static>|Shipment withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Shipment withoutTrashed()
 * @mixin \Eloquent
 */
class Shipment extends Model
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

    public const STATUS_READY_TO_SHIP = 'ready_to_ship';

    public const STATUS_PICKED_UP = 'picked_up';

    public const STATUS_IN_TRANSIT = 'in_transit';

    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_FAILED_DELIVERY = 'failed_delivery';

    public const STATUS_RETURNED = 'returned';

    public const STATUS_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'order_id',

        'shipping_method_id',

        'shipment_number',

        'tracking_number',

        'courier_name',

        'courier_code',

        'service_name',

        'service_code',

        'tracking_url',

        'shipping_cost',

        'insurance_cost',

        'is_insured',

        'weight',

        'item_count',

        'status',

        'recipient_name',

        'recipient_phone',

        'recipient_address',

        'recipient_city',

        'recipient_province',

        'recipient_postal_code',

        'pickup_at',

        'shipped_at',

        'estimated_delivery_at',

        'delivered_at',

        'delivery_attempts',

        'delivery_duration_hours',

        'last_tracking_sync_at',

        'received_by',

        'signed_proof',

        'failed_reason',

        'return_reason',

        'notes',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'tracking_link',

        'is_delivered',

        'is_in_transit',

        'is_cancelled',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'shipping_cost' => 'decimal:2',

            'insurance_cost' => 'decimal:2',

            'weight' => 'decimal:2',

            'is_insured' => 'boolean',

            'delivery_attempts' => 'integer',

            'delivery_duration_hours' => 'integer',

            'metadata' => 'array',

            'pickup_at' => 'datetime',

            'shipped_at' => 'datetime',

            'estimated_delivery_at' => 'datetime',

            'delivered_at' => 'datetime',

            'last_tracking_sync_at' => 'datetime',
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

            ->useLogName('shipment')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Shipment {$eventName}"
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

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(
            ShippingMethod::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTrackingLinkAttribute(): ?string
    {
        return $this->tracking_url;
    }

    public function getIsDeliveredAttribute(): bool
    {
        return $this->status ===
            self::STATUS_DELIVERED;
    }

    public function getIsInTransitAttribute(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PICKED_UP,
                self::STATUS_IN_TRANSIT,
                self::STATUS_OUT_FOR_DELIVERY,
            ]
        );
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status ===
            self::STATUS_CANCELLED;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }

    public function scopeReadyToShip(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_READY_TO_SHIP
        );
    }

    public function scopeInTransit(
        Builder $query
    ): Builder {

        return $query->whereIn(
            'status',
            [
                self::STATUS_PICKED_UP,
                self::STATUS_IN_TRANSIT,
                self::STATUS_OUT_FOR_DELIVERY,
            ]
        );
    }

    public function scopeDelivered(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_DELIVERED
        );
    }

    public function scopeReturned(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_RETURNED
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

    public function scopeByTrackingNumber(
        Builder $query,
        ?string $trackingNumber
    ): Builder {

        return $query->when(

            filled($trackingNumber),

            fn (Builder $query)

                => $query->where(
                    'tracking_number',
                    $trackingNumber
                )
        );
    }

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
                            'shipment_number',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'tracking_number',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'recipient_name',
                            'like',
                            "%{$keyword}%"
                        )
                )
        );
    }

    public function scopeLatest(
        Builder $query
    ): Builder {

        return $query->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status ===
            self::STATUS_PENDING;
    }

    public function isDelivered(): bool
    {
        return $this->status ===
            self::STATUS_DELIVERED;
    }

    public function isReturned(): bool
    {
        return $this->status ===
            self::STATUS_RETURNED;
    }

    public function isCancelled(): bool
    {
        return $this->status ===
            self::STATUS_CANCELLED;
    }

    public function isInTransit(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PICKED_UP,
                self::STATUS_IN_TRANSIT,
                self::STATUS_OUT_FOR_DELIVERY,
            ]
        );
    }

    public function hasTrackingNumber(): bool
    {
        return !empty(
            $this->tracking_number
        );
    }

    public function hasProofOfDelivery(): bool
    {
        return !empty(
            $this->signed_proof
        );
    }

    public function courierDisplay(): string
    {
        return trim(
            $this->courier_name .
            (
                $this->service_name
                    ? ' - ' . $this->service_name
                    : ''
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function markPickedUp(): void
    {
        $this->update([

            'status' =>
                self::STATUS_PICKED_UP,

            'pickup_at' =>
                now(),

            'shipped_at' =>
                now(),
        ]);
    }

    public function markInTransit(): void
    {
        $this->update([
            'status' => self::STATUS_IN_TRANSIT,
        ]);
    }

    public function markDelivered(
        ?string $receivedBy = null
    ): void {

        $this->update([
            'status' => self::STATUS_DELIVERED,
            'received_by' => $receivedBy,
            'delivered_at' => now(),
        ]);
    }

    public function markReturned(
        ?string $reason = null
    ): void {

        $this->update([
            'status' => self::STATUS_RETURNED,
            'return_reason' => $reason,
        ]);
    }

    public function markCancelled(
        ?string $reason = null
    ): void {

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $reason,
        ]);
    }

    public function syncTracking(): void
    {
        $this->update([
            'last_tracking_sync_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'shipment_number';
    }
}