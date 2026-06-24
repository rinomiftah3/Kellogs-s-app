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
 * StockOpname Model
 *
 * Enterprise Inventory Audit
 *
 * Laravel 13
 * PHP 8.4
 *
 * @property int $id
 * @property int $product_sku_id
 * @property string $opname_number
 * @property \Illuminate\Support\Carbon $opname_date
 * @property int $system_stock
 * @property int $physical_stock
 * @property int $difference
 * @property string $status
 * @property string|null $notes
 * @property int|null $counted_by
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\User|null $counter
 * @property-read \App\Models\ProductSku|null $sku
 * @property-read \App\Models\User|null $verifier
 * @method static Builder<static>|StockOpname bySku(?int $skuId)
 * @method static Builder<static>|StockOpname byStatus(?string $status)
 * @method static Builder<static>|StockOpname dateRange($startDate, $endDate)
 * @method static Builder<static>|StockOpname excess()
 * @method static Builder<static>|StockOpname latestFirst()
 * @method static Builder<static>|StockOpname match()
 * @method static Builder<static>|StockOpname newModelQuery()
 * @method static Builder<static>|StockOpname newQuery()
 * @method static Builder<static>|StockOpname onlyTrashed()
 * @method static Builder<static>|StockOpname query()
 * @method static Builder<static>|StockOpname shortage()
 * @method static Builder<static>|StockOpname today()
 * @method static Builder<static>|StockOpname unverified()
 * @method static Builder<static>|StockOpname verified()
 * @method static Builder<static>|StockOpname whereCountedBy($value)
 * @method static Builder<static>|StockOpname whereCreatedAt($value)
 * @method static Builder<static>|StockOpname whereDeletedAt($value)
 * @method static Builder<static>|StockOpname whereDifference($value)
 * @method static Builder<static>|StockOpname whereId($value)
 * @method static Builder<static>|StockOpname whereNotes($value)
 * @method static Builder<static>|StockOpname whereOpnameDate($value)
 * @method static Builder<static>|StockOpname whereOpnameNumber($value)
 * @method static Builder<static>|StockOpname wherePhysicalStock($value)
 * @method static Builder<static>|StockOpname whereProductSkuId($value)
 * @method static Builder<static>|StockOpname whereStatus($value)
 * @method static Builder<static>|StockOpname whereSystemStock($value)
 * @method static Builder<static>|StockOpname whereUpdatedAt($value)
 * @method static Builder<static>|StockOpname whereVerifiedAt($value)
 * @method static Builder<static>|StockOpname whereVerifiedBy($value)
 * @method static Builder<static>|StockOpname withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|StockOpname withoutTrashed()
 * @mixin \Eloquent
 */
class StockOpname extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_MATCH = 'match';

    public const STATUS_SHORTAGE = 'shortage';

    public const STATUS_EXCESS = 'excess';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_sku_id',

        'opname_number',

        'opname_date',

        'system_stock',

        'physical_stock',

        'difference',

        'status',

        'notes',

        'counted_by',

        'verified_by',

        'verified_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'opname_date' => 'date',

            'system_stock' => 'integer',

            'physical_stock' => 'integer',

            'difference' => 'integer',

            'verified_at' => 'datetime',
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

            ->useLogName('stock_opname')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Stock Opname {$eventName}"
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

    public function counter(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'counted_by'
        );
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'verified_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeVerified(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'verified_at'
        );
    }

    public function scopeUnverified(
        Builder $query
    ): Builder {

        return $query->whereNull(
            'verified_at'
        );
    }

    public function scopeMatch(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_MATCH
        );
    }

    public function scopeShortage(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_SHORTAGE
        );
    }

    public function scopeExcess(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_EXCESS
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
            'opname_date',
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
            'opname_date',
            today()
        );
    }

    public function scopeLatestFirst(
        Builder $query
    ): Builder {

        return $query

            ->orderByDesc(
                'opname_date'
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

    public function isVerified(): bool
    {
        return !is_null(
            $this->verified_at
        );
    }

    public function hasVerifier(): bool
    {
        return !is_null(
            $this->verified_by
        );
    }

    public function isMatch(): bool
    {
        return $this->status ===
            self::STATUS_MATCH;
    }

    public function isShortage(): bool
    {
        return $this->status ===
            self::STATUS_SHORTAGE;
    }

    public function isExcess(): bool
    {
        return $this->status ===
            self::STATUS_EXCESS;
    }

    public function skuCode(): ?string
    {
        return $this->sku?->sku;
    }

    public function productName(): ?string
    {
        return $this->sku?->product?->name;
    }

    public function counterName(): ?string
    {
        return $this->counter?->name;
    }

    public function verifierName(): ?string
    {
        return $this->verifier?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function verify(
        int $userId
    ): bool {

        if (
            $this->isVerified()
        ) {
            return false;
        }

        return $this->update([
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);
    }

    public function calculateDifference(): int
    {
        return
            $this->physical_stock
            - $this->system_stock;
    }

    public function determineStatus(): string
    {
        if ($this->difference === 0) {
            return self::STATUS_MATCH;
        }

        if ($this->difference < 0) {
            return self::STATUS_SHORTAGE;
        }

        return self::STATUS_EXCESS;
    }

    public function formattedDifference(): string
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
        return 'opname_number';
    }
}