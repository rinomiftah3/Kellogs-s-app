<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class PointTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Transaction Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_EARN = 'earn';

    public const TYPE_REDEEM = 'redeem';

    public const TYPE_EXPIRE = 'expire';

    public const TYPE_REFUND = 'refund';

    public const TYPE_ADJUSTMENT = 'adjustment';

    public const TYPE_BONUS = 'bonus';

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'customer_profile_id',
        'order_id',

        'transaction_number',

        'type',

        'points',

        'balance_before',
        'balance_after',

        'reference_type',
        'reference_id',

        'title',
        'description',

        'expired_at',

        'approved_by',
        'approved_at',

        'status',

        'metadata',

        'transaction_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'is_credit',
        'is_debit',
        'formatted_points',
        'is_expired',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'points' => 'integer',

            'balance_before' => 'integer',

            'balance_after' => 'integer',

            'expired_at' => 'datetime',

            'approved_at' => 'datetime',

            'transaction_at' => 'datetime',

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
                'point_transaction'
            )

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Point Transaction {$eventName}"
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsCreditAttribute(): bool
    {
        return in_array(
            $this->type,
            [
                self::TYPE_EARN,
                self::TYPE_BONUS,
                self::TYPE_REFUND,
                self::TYPE_ADJUSTMENT,
            ]
        );
    }

    public function getIsDebitAttribute(): bool
    {
        return in_array(
            $this->type,
            [
                self::TYPE_REDEEM,
                self::TYPE_EXPIRE,
            ]
        );
    }

    public function getFormattedPointsAttribute(): string
    {
        $prefix = $this->is_credit
            ? '+'
            : '-';

        return $prefix . number_format(
            abs((int) $this->points)
        );
    }

    public function getIsExpiredAttribute(): bool
    {
        return ! is_null(
            $this->expired_at
        )
        &&
        now()->greaterThan(
            $this->expired_at
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
                            'transaction_number',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'title',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'description',
                            'like',
                            "%{$keyword}%"
                        )
                )
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

    public function scopePending(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_PENDING
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

    public function scopeExpired(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'expired_at'
        )->where(
            'expired_at',
            '<',
            now()
        );
    }

    public function scopeLatest(
        Builder $query
    ): Builder {

        return $query->latest(
            'transaction_at'
        );
    }

    public function scopeForCustomer(
        Builder $query,
        ?int $customerId
    ): Builder {

        return $query->when(

            filled($customerId),

            fn (Builder $query)

                => $query->where(
                    'customer_profile_id',
                    $customerId
                )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCompleted(): bool
    {
        return $this->status ===
            self::STATUS_COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status ===
            self::STATUS_PENDING;
    }

    public function isCancelled(): bool
    {
        return $this->status ===
            self::STATUS_CANCELLED;
    }

    public function isEarn(): bool
    {
        return $this->type ===
            self::TYPE_EARN;
    }

    public function isRedeem(): bool
    {
        return $this->type ===
            self::TYPE_REDEEM;
    }

    public function isBonus(): bool
    {
        return $this->type ===
            self::TYPE_BONUS;
    }

    public function isCredit(): bool
    {
        return $this->is_credit;
    }

    public function isDebit(): bool
    {
        return $this->is_debit;
    }

    public function isExpired(): bool
    {
        return $this->is_expired;
    }

    public function customerName(): ?string
    {
        return $this->customerProfile?->full_name;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Methods
    |--------------------------------------------------------------------------
    */

    public function approve(
        int $userId
    ): void {

        $this->update([
            'approved_by' => $userId,
            'approved_at' => now(),
            'status' => self::STATUS_COMPLETED,
        ]);
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'transaction_number';
    }
}