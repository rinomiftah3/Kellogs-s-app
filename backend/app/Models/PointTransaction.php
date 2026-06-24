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

/**
 * @property int $id
 * @property int $customer_profile_id
 * @property int|null $order_id
 * @property string $transaction_number
 * @property string $type
 * @property int $points
 * @property int $balance_before
 * @property int $balance_after
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $expired_at
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string $status
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $transaction_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\CustomerProfile|null $customerProfile
 * @property-read string $formatted_points
 * @property-read bool $is_credit
 * @property-read bool $is_debit
 * @property-read bool $is_expired
 * @property-read \App\Models\Order|null $order
 * @property-read Model|\Eloquent|null $reference
 * @method static Builder<static>|PointTransaction completed()
 * @method static Builder<static>|PointTransaction expired()
 * @method static Builder<static>|PointTransaction forCustomer(?int $customerId)
 * @method static Builder<static>|PointTransaction latest()
 * @method static Builder<static>|PointTransaction newModelQuery()
 * @method static Builder<static>|PointTransaction newQuery()
 * @method static Builder<static>|PointTransaction onlyTrashed()
 * @method static Builder<static>|PointTransaction pending()
 * @method static Builder<static>|PointTransaction query()
 * @method static Builder<static>|PointTransaction search(?string $keyword)
 * @method static Builder<static>|PointTransaction status(?string $status)
 * @method static Builder<static>|PointTransaction type(?string $type)
 * @method static Builder<static>|PointTransaction whereApprovedAt($value)
 * @method static Builder<static>|PointTransaction whereApprovedBy($value)
 * @method static Builder<static>|PointTransaction whereBalanceAfter($value)
 * @method static Builder<static>|PointTransaction whereBalanceBefore($value)
 * @method static Builder<static>|PointTransaction whereCreatedAt($value)
 * @method static Builder<static>|PointTransaction whereCustomerProfileId($value)
 * @method static Builder<static>|PointTransaction whereDescription($value)
 * @method static Builder<static>|PointTransaction whereExpiredAt($value)
 * @method static Builder<static>|PointTransaction whereId($value)
 * @method static Builder<static>|PointTransaction whereMetadata($value)
 * @method static Builder<static>|PointTransaction whereOrderId($value)
 * @method static Builder<static>|PointTransaction wherePoints($value)
 * @method static Builder<static>|PointTransaction whereReferenceId($value)
 * @method static Builder<static>|PointTransaction whereReferenceType($value)
 * @method static Builder<static>|PointTransaction whereStatus($value)
 * @method static Builder<static>|PointTransaction whereTitle($value)
 * @method static Builder<static>|PointTransaction whereTransactionAt($value)
 * @method static Builder<static>|PointTransaction whereTransactionNumber($value)
 * @method static Builder<static>|PointTransaction whereType($value)
 * @method static Builder<static>|PointTransaction whereUpdatedAt($value)
 * @method static Builder<static>|PointTransaction withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PointTransaction withoutTrashed()
 * @mixin \Eloquent
 */
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