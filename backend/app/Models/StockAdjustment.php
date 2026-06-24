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
 * StockAdjustment Model
 *
 * Enterprise Inventory Adjustment
 *
 * Laravel 13
 * PHP 8.4
 *
 * @property int $id
 * @property int $product_sku_id
 * @property string $adjustment_number
 * @property string $type
 * @property int $old_stock
 * @property int $new_stock
 * @property int $difference
 * @property string $reason
 * @property string|null $notes
 * @property string $status
 * @property int|null $requested_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon $adjustment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\User|null $requester
 * @property-read \App\Models\ProductSku|null $sku
 * @method static Builder<static>|StockAdjustment approved()
 * @method static Builder<static>|StockAdjustment bySku(?int $skuId)
 * @method static Builder<static>|StockAdjustment byStatus(?string $status)
 * @method static Builder<static>|StockAdjustment byType(?string $type)
 * @method static Builder<static>|StockAdjustment dateRange($startDate, $endDate)
 * @method static Builder<static>|StockAdjustment latestFirst()
 * @method static Builder<static>|StockAdjustment newModelQuery()
 * @method static Builder<static>|StockAdjustment newQuery()
 * @method static Builder<static>|StockAdjustment onlyTrashed()
 * @method static Builder<static>|StockAdjustment pending()
 * @method static Builder<static>|StockAdjustment query()
 * @method static Builder<static>|StockAdjustment rejected()
 * @method static Builder<static>|StockAdjustment today()
 * @method static Builder<static>|StockAdjustment whereAdjustmentDate($value)
 * @method static Builder<static>|StockAdjustment whereAdjustmentNumber($value)
 * @method static Builder<static>|StockAdjustment whereApprovedAt($value)
 * @method static Builder<static>|StockAdjustment whereApprovedBy($value)
 * @method static Builder<static>|StockAdjustment whereCreatedAt($value)
 * @method static Builder<static>|StockAdjustment whereDeletedAt($value)
 * @method static Builder<static>|StockAdjustment whereDifference($value)
 * @method static Builder<static>|StockAdjustment whereId($value)
 * @method static Builder<static>|StockAdjustment whereNewStock($value)
 * @method static Builder<static>|StockAdjustment whereNotes($value)
 * @method static Builder<static>|StockAdjustment whereOldStock($value)
 * @method static Builder<static>|StockAdjustment whereProductSkuId($value)
 * @method static Builder<static>|StockAdjustment whereReason($value)
 * @method static Builder<static>|StockAdjustment whereRequestedBy($value)
 * @method static Builder<static>|StockAdjustment whereStatus($value)
 * @method static Builder<static>|StockAdjustment whereType($value)
 * @method static Builder<static>|StockAdjustment whereUpdatedAt($value)
 * @method static Builder<static>|StockAdjustment withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|StockAdjustment withoutTrashed()
 * @mixin \Eloquent
 */
class StockAdjustment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Adjustment Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_INCREASE = 'increase';

    public const TYPE_DECREASE = 'decrease';

    public const TYPE_CORRECTION = 'correction';

    public const TYPE_DAMAGED = 'damaged';

    public const TYPE_EXPIRED = 'expired';

    public const TYPE_LOST = 'lost';

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /*
    |--------------------------------------------------------------------------
    | Directions
    |--------------------------------------------------------------------------
    */

    public const DIRECTION_INCREASE = 'increase';

    public const DIRECTION_DECREASE = 'decrease';

    public const DIRECTION_NEUTRAL = 'neutral';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_sku_id',

        'adjustment_number',

        'type',

        'old_stock',

        'new_stock',

        'difference',

        'reason',

        'notes',

        'status',

        'requested_by',

        'approved_by',

        'approved_at',

        'adjustment_date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'old_stock' => 'integer',

            'new_stock' => 'integer',

            'difference' => 'integer',

            'approved_at' => 'datetime',

            'adjustment_date' => 'datetime',
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

            ->useLogName('stock_adjustment')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Stock Adjustment {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function sku(): BelongsTo
    {
        return $this->belongsTo(
            ProductSku::class,
            'product_sku_id'
        );
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'requested_by'
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
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

    public function scopeApproved(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_APPROVED
        );
    }

    public function scopeRejected(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_REJECTED
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

    public function scopeByType(
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

    public function scopeBySku(
        Builder $query,
        ?int $skuId
    ): Builder {

        return $query->when(
            filled($skuId),

            fn (Builder $query)
                => $query->where(
                    'product_sku_id',
                    $skuId
                )
        );
    }

    public function scopeDateRange(
        Builder $query,
        $startDate,
        $endDate
    ): Builder {

        return $query->whereBetween(
            'adjustment_date',
            [
                $startDate,
                $endDate,
            ]
        );
    }

    public function scopeToday(
        Builder $query
    ): Builder {

        return $query->whereDate(
            'adjustment_date',
            today()
        );
    }

    public function scopeLatestFirst(
        Builder $query
    ): Builder {

        return $query

            ->orderByDesc(
                'adjustment_date'
            )

            ->orderByDesc(
                'id'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isApproved(): bool
    {
        return $this->status ===
            self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status ===
            self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status ===
            self::STATUS_REJECTED;
    }

    public function isProcessed(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_APPROVED,
                self::STATUS_REJECTED,
            ],
            true
        );
    }

    public function isIncrease(): bool
    {
        return $this->difference > 0;
    }

    public function isDecrease(): bool
    {
        return $this->difference < 0;
    }

    public function adjustmentDirection(): string
    {
        if ($this->difference > 0) {
            return self::DIRECTION_INCREASE;
        }

        if ($this->difference < 0) {
            return self::DIRECTION_DECREASE;
        }

        return self::DIRECTION_NEUTRAL;
    }

    public function hasApprover(): bool
    {
        return !is_null(
            $this->approved_by
        );
    }

    public function skuCode(): ?string
    {
        return $this->sku?->sku;
    }

    public function productName(): ?string
    {
        return $this->sku?->product?->name;
    }

    public function requesterName(): ?string
    {
        return $this->requester?->name;
    }

    public function approverName(): ?string
    {
        return $this->approver?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function approve(
        int $userId
    ): bool {

        if (
            $this->isProcessed()
        ) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function reject(
        ?string $notes = null
    ): bool {

        if (
            $this->isProcessed()
        ) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_REJECTED,
            'notes' => $notes
                ?? $this->notes,
        ]);
    }

    public function stockDifference(): int
    {
        return $this->difference;
    }

    public function formattedAdjustment(): string
    {
        return sprintf(
            '%+d',
            $this->difference
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'adjustment_number';
    }
}