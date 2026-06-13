<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class OrderStatusLog extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Source Constants
    |--------------------------------------------------------------------------
    */

    public const SOURCE_SYSTEM = 'system';

    public const SOURCE_CUSTOMER = 'customer';

    public const SOURCE_ADMIN = 'admin';

    public const SOURCE_PAYMENT_GATEWAY = 'payment_gateway';

    public const SOURCE_COURIER = 'courier';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'order_id',

        'user_id',

        'from_status',

        'to_status',

        'changed_at',

        'duration_seconds',

        'source',

        'reason',

        'notes',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appended Attributes
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'transition',

        'duration_minutes',

        'duration_hours',

        'duration_human',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'changed_at' => 'datetime',

            'duration_seconds' => 'integer',

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

            ->useLogName('order_status_log')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Order Status Log {$eventName}"
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTransitionAttribute(): string
    {
        return sprintf(
            '%s → %s',
            $this->from_status ?? '-',
            $this->to_status
        );
    }

    public function getDurationMinutesAttribute(): ?float
    {
        if (
            is_null($this->duration_seconds)
        ) {
            return null;
        }

        return round(
            $this->duration_seconds / 60,
            2
        );
    }

    public function getDurationHoursAttribute(): ?float
    {
        if (
            is_null($this->duration_seconds)
        ) {
            return null;
        }

        return round(
            $this->duration_seconds / 3600,
            2
        );
    }

    public function getDurationHumanAttribute(): ?string
    {
        if (
            is_null($this->duration_seconds)
        ) {
            return null;
        }

        $seconds =
            $this->duration_seconds;

        $days =
            floor($seconds / 86400);

        $hours =
            floor(($seconds % 86400) / 3600);

        $minutes =
            floor(($seconds % 3600) / 60);

        $parts = [];

        if ($days > 0) {
            $parts[] = "{$days}d";
        }

        if ($hours > 0) {
            $parts[] = "{$hours}h";
        }

        if ($minutes > 0) {
            $parts[] = "{$minutes}m";
        }

        return implode(' ', $parts);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByOrder(
        Builder $query,
        ?int $orderId
    ): Builder {

        return $query->when(

            filled($orderId),

            fn (Builder $query)

                => $query->where(
                    'order_id',
                    $orderId
                )
        );
    }

    public function scopeByStatus(
        Builder $query,
        ?string $status
    ): Builder {

        return $query->when(

            filled($status),

            fn (Builder $query)

                => $query->where(
                    'to_status',
                    $status
                )
        );
    }

    public function scopeBySource(
        Builder $query,
        ?string $source
    ): Builder {

        return $query->when(

            filled($source),

            fn (Builder $query)

                => $query->where(
                    'source',
                    $source
                )
        );
    }

    public function scopeSystem(
        Builder $query
    ): Builder {

        return $query->where(
            'source',
            self::SOURCE_SYSTEM
        );
    }

    public function scopeAdmin(
        Builder $query
    ): Builder {

        return $query->where(
            'source',
            self::SOURCE_ADMIN
        );
    }

    public function scopeCustomer(
        Builder $query
    ): Builder {

        return $query->where(
            'source',
            self::SOURCE_CUSTOMER
        );
    }

    public function scopePaymentGateway(
        Builder $query
    ): Builder {

        return $query->where(
            'source',
            self::SOURCE_PAYMENT_GATEWAY
        );
    }

    public function scopeCourier(
        Builder $query
    ): Builder {

        return $query->where(
            'source',
            self::SOURCE_COURIER
        );
    }

    public function scopeLatestFirst(
        Builder $query
    ): Builder {

        return $query->latest(
            'changed_at'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasPreviousStatus(): bool
    {
        return !empty(
            $this->from_status
        );
    }

    public function hasDuration(): bool
    {
        return !is_null(
            $this->duration_seconds
        );
    }

    public function isSystem(): bool
    {
        return $this->source ===
            self::SOURCE_SYSTEM;
    }

    public function isAdmin(): bool
    {
        return $this->source ===
            self::SOURCE_ADMIN;
    }

    public function isCustomer(): bool
    {
        return $this->source ===
            self::SOURCE_CUSTOMER;
    }

    public function isPaymentGateway(): bool
    {
        return $this->source ===
            self::SOURCE_PAYMENT_GATEWAY;
    }

    public function isCourier(): bool
    {
        return $this->source ===
            self::SOURCE_COURIER;
    }

    public function actorName(): ?string
    {
        return $this->user?->name;
    }

    public function actorId(): ?int
    {
        return $this->user?->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function transition(): string
    {
        return $this->transition;
    }

    public function durationForHumans(): ?string
    {
        return $this->duration_human;
    }

    /*
    |--------------------------------------------------------------------------
    | Route Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}