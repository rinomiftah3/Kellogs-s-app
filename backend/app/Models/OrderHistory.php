<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class OrderHistory extends Model
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

        'action',

        'old_status',

        'new_status',

        'description',

        'notes',

        'source',

        'metadata',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appended Attributes
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'status_transition',

        'summary_text',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

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

            ->useLogName('order_history')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Order History {$eventName}"
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

    public function getStatusTransitionAttribute(): ?string
    {
        if (
            empty($this->old_status)
            || empty($this->new_status)
        ) {
            return null;
        }

        return sprintf(
            '%s → %s',
            $this->old_status,
            $this->new_status
        );
    }

    public function getSummaryTextAttribute(): string
    {
        return trim(
            $this->action .
            (
                $this->description
                    ? ' - ' .
                    $this->description
                    : ''
            )
        );
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

    public function scopeByAction(
        Builder $query,
        ?string $action
    ): Builder {

        return $query->when(

            filled($action),

            fn (Builder $query)

                => $query->where(
                    'action',
                    $action
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

    public function scopeCustomer(
        Builder $query
    ): Builder {

        return $query->where(
            'source',
            self::SOURCE_CUSTOMER
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

        return $query->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isSystem(): bool
    {
        return $this->source ===
            self::SOURCE_SYSTEM;
    }

    public function isCustomer(): bool
    {
        return $this->source ===
            self::SOURCE_CUSTOMER;
    }

    public function isAdmin(): bool
    {
        return $this->source ===
            self::SOURCE_ADMIN;
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

    public function hasStatusChange(): bool
    {
        return !empty(
            $this->old_status
        ) || !empty(
            $this->new_status
        );
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

    public function statusTransition(): ?string
    {
        return $this->status_transition;
    }

    public function summary(): string
    {
        return $this->summary_text;
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