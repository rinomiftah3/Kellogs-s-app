<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * VoucherUsage Model
 *
 * Voucher Redemption History
 *
 * Laravel 13
 * PHP 8.4
 *
 * @property int $id
 * @property int $voucher_id
 * @property int $customer_profile_id
 * @property int|null $order_id
 * @property string $voucher_code
 * @property string $voucher_name
 * @property numeric $discount_amount
 * @property numeric $order_subtotal
 * @property numeric $order_total
 * @property string $status
 * @property bool $is_valid
 * @property \Illuminate\Support\Carbon $used_at
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\CustomerProfile|null $customerProfile
 * @property-read float $saving_amount
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Voucher|null $voucher
 * @method static Builder<static>|VoucherUsage byCustomer(?int $customerId)
 * @method static Builder<static>|VoucherUsage byStatus(?string $status)
 * @method static Builder<static>|VoucherUsage byVoucher(?int $voucherId)
 * @method static Builder<static>|VoucherUsage cancelled()
 * @method static Builder<static>|VoucherUsage expired()
 * @method static Builder<static>|VoucherUsage invalid()
 * @method static Builder<static>|VoucherUsage latestFirst()
 * @method static Builder<static>|VoucherUsage newModelQuery()
 * @method static Builder<static>|VoucherUsage newQuery()
 * @method static Builder<static>|VoucherUsage onlyTrashed()
 * @method static Builder<static>|VoucherUsage query()
 * @method static Builder<static>|VoucherUsage reserved()
 * @method static Builder<static>|VoucherUsage used()
 * @method static Builder<static>|VoucherUsage valid()
 * @method static Builder<static>|VoucherUsage whereCreatedAt($value)
 * @method static Builder<static>|VoucherUsage whereCustomerProfileId($value)
 * @method static Builder<static>|VoucherUsage whereDeletedAt($value)
 * @method static Builder<static>|VoucherUsage whereDiscountAmount($value)
 * @method static Builder<static>|VoucherUsage whereId($value)
 * @method static Builder<static>|VoucherUsage whereIsValid($value)
 * @method static Builder<static>|VoucherUsage whereMetadata($value)
 * @method static Builder<static>|VoucherUsage whereNotes($value)
 * @method static Builder<static>|VoucherUsage whereOrderId($value)
 * @method static Builder<static>|VoucherUsage whereOrderSubtotal($value)
 * @method static Builder<static>|VoucherUsage whereOrderTotal($value)
 * @method static Builder<static>|VoucherUsage whereStatus($value)
 * @method static Builder<static>|VoucherUsage whereUpdatedAt($value)
 * @method static Builder<static>|VoucherUsage whereUsedAt($value)
 * @method static Builder<static>|VoucherUsage whereVoucherCode($value)
 * @method static Builder<static>|VoucherUsage whereVoucherId($value)
 * @method static Builder<static>|VoucherUsage whereVoucherName($value)
 * @method static Builder<static>|VoucherUsage withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|VoucherUsage withoutTrashed()
 * @mixin \Eloquent
 */
class VoucherUsage extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_RESERVED = 'reserved';

    public const STATUS_USED = 'used';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_EXPIRED = 'expired';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'voucher_id',

        'customer_profile_id',

        'order_id',

        'voucher_code',

        'voucher_name',

        'discount_amount',

        'order_subtotal',

        'order_total',

        'status',

        'is_valid',

        'used_at',

        'metadata',

        'notes',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'saving_amount',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'discount_amount' => 'decimal:2',

            'order_subtotal' => 'decimal:2',

            'order_total' => 'decimal:2',

            'is_valid' => 'boolean',

            'used_at' => 'datetime',

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

            ->useLogName('voucher_usage')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Voucher Usage {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(
            Voucher::class
        );
    }

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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getSavingAmountAttribute(): float
    {
        return (float)
            $this->discount_amount;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeValid(
        Builder $query
    ): Builder {

        return $query->where(
            'is_valid',
            true
        );
    }

    public function scopeInvalid(
        Builder $query
    ): Builder {

        return $query->where(
            'is_valid',
            false
        );
    }

    public function scopeUsed(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_USED
        );
    }

    public function scopeReserved(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_RESERVED
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

    public function scopeByStatus(
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

    public function scopeByVoucher(
        Builder $query,
        ?int $voucherId
    ): Builder {

        return $query->when(
            filled($voucherId),

            fn (Builder $query)

                => $query->where(
                    'voucher_id',
                    $voucherId
                )
        );
    }

    public function scopeByCustomer(
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

    public function scopeLatestFirst(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'used_at'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isUsed(): bool
    {
        return $this->status ===
            self::STATUS_USED;
    }

    public function isReserved(): bool
    {
        return $this->status ===
            self::STATUS_RESERVED;
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

    public function isValidUsage(): bool
    {
        return (bool)
            $this->is_valid;
    }

    public function customerName(): ?string
    {
        return $this->customerProfile?->full_name;
    }

    public function voucherName(): string
    {
        return $this->voucher_name;
    }

    public function voucherCodeDisplay(): string
    {
        return $this->voucher_code;
    }

    public function discountAmount(): float
    {
        return (float)
            $this->discount_amount;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function markAsUsed(): void
    {
        $this->update([
            'status' => self::STATUS_USED,
            'is_valid' => true,
            'used_at' => now(),
        ]);
    }

    public function markAsReserved(): void
    {
        $this->update([
            'status' => self::STATUS_RESERVED,
            'is_valid' => true,
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'is_valid' => false,
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
            'is_valid' => false,
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