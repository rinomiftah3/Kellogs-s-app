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