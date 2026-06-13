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
 * CustomerNotification Model
 *
 * Enterprise Notification Center
 */
class CustomerNotification extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Notification Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_ORDER = 'order';

    public const TYPE_PAYMENT = 'payment';

    public const TYPE_SHIPMENT = 'shipment';

    public const TYPE_PROMOTION = 'promotion';

    public const TYPE_LOYALTY = 'loyalty';

    public const TYPE_SYSTEM = 'system';

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'customer_profile_id',

        'type',

        'title',

        'message',

        'send_in_app',

        'send_push',

        'send_email',

        'send_sms',

        'action_url',

        'action_label',

        'data',

        'is_read',

        'read_at',

        'sent_at',

        'scheduled_at',

        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'data' => 'array',

            'is_read' => 'boolean',

            'send_in_app' => 'boolean',

            'send_push' => 'boolean',

            'send_email' => 'boolean',

            'send_sms' => 'boolean',

            'read_at' => 'datetime',

            'sent_at' => 'datetime',

            'scheduled_at' => 'datetime',
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

            ->useLogName('customer_notification')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn(string $eventName)
                    => "Customer Notification {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            CustomerProfile::class,
            'customer_profile_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUnread(
        Builder $query
    ): Builder {

        return $query->where(
            'is_read',
            false
        );
    }

    public function scopeRead(
        Builder $query
    ): Builder {

        return $query->where(
            'is_read',
            true
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

    public function scopeSent(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_SENT
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

    public function scopeCancelled(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_CANCELLED
        );
    }

    public function scopeScheduled(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'scheduled_at'
        );
    }

    public function scopeByCustomer(
        Builder $query,
        ?int $customerId
    ): Builder {

        return $query->when(
            filled($customerId),

            fn(Builder $query)

                => $query->where(
                    'customer_profile_id',
                    $customerId
                )
        );
    }

    public function scopeByType(
        Builder $query,
        ?string $type
    ): Builder {

        return $query->when(
            filled($type),

            fn(Builder $query)

                => $query->where(
                    'type',
                    $type
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

    public function isRead(): bool
    {
        return (bool)
            $this->is_read;
    }

    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    public function isSent(): bool
    {
        return $this->status ===
            self::STATUS_SENT;
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

    public function isScheduled(): bool
    {
        return !is_null(
            $this->scheduled_at
        );
    }

    public function hasAction(): bool
    {
        return !empty(
            $this->action_url
        );
    }

    public function hasData(): bool
    {
        return !empty(
            $this->data
        );
    }

    public function customerName(): ?string
    {
        return $this->customer?->full_name;
    }

    public function channelCount(): int
    {
        return collect([
            $this->send_in_app,
            $this->send_push,
            $this->send_email,
            $this->send_sms,
        ])->filter()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function markAsSent(): bool
    {
        return $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }

    public function cancel(): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    public function schedule(
        \DateTimeInterface $datetime
    ): bool {

        return $this->update([
            'scheduled_at' => $datetime,
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