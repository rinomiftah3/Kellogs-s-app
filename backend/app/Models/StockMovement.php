<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * StockMovement Model
 *
 * Enterprise Inventory Audit Trail
 *
 * Laravel 13
 * PHP 8.4
 */
class StockMovement extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Movement Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_STOCK_IN = 'stock_in';

    public const TYPE_STOCK_OUT = 'stock_out';

    public const TYPE_SALE = 'sale';

    public const TYPE_RETURN = 'return';

    public const TYPE_ADJUSTMENT = 'adjustment';

    public const TYPE_TRANSFER = 'transfer';

    public const TYPE_DAMAGED = 'damaged';

    public const TYPE_EXPIRED = 'expired';

    /*
    |--------------------------------------------------------------------------
    | Movement Directions
    |--------------------------------------------------------------------------
    */

    public const DIRECTION_IN = 'in';

    public const DIRECTION_OUT = 'out';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_sku_id',

        'type',

        'quantity',

        'stock_before',

        'stock_after',

        'reference_type',

        'reference_id',

        'reference_number',

        'notes',

        'created_by',

        'movement_date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'quantity' => 'integer',

            'stock_before' => 'integer',

            'stock_after' => 'integer',

            'movement_date' => 'datetime',
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

            ->useLogName('stock_movement')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Stock Movement {$eventName}"
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

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

    public function scopeByCreator(
        Builder $query,
        ?int $userId
    ): Builder {

        return $query->when(
            filled($userId),

            fn (Builder $query)
                => $query->where(
                    'created_by',
                    $userId
                )
        );
    }

    public function scopeStockIn(
        Builder $query
    ): Builder {

        return $query->whereIn(
            'type',
            [
                self::TYPE_STOCK_IN,
                self::TYPE_RETURN,
            ]
        );
    }

    public function scopeStockOut(
        Builder $query
    ): Builder {

        return $query->whereIn(
            'type',
            [
                self::TYPE_STOCK_OUT,
                self::TYPE_SALE,
                self::TYPE_DAMAGED,
                self::TYPE_EXPIRED,
            ]
        );
    }

    public function scopeByReference(
        Builder $query,
        ?string $referenceType,
        ?int $referenceId
    ): Builder {

        return $query

            ->when(
                filled($referenceType),

                fn (Builder $query)
                    => $query->where(
                        'reference_type',
                        $referenceType
                    )
            )

            ->when(
                filled($referenceId),

                fn (Builder $query)
                    => $query->where(
                        'reference_id',
                        $referenceId
                    )
            );
    }

    public function scopeDateRange(
        Builder $query,
        $startDate,
        $endDate
    ): Builder {

        return $query->whereBetween(
            'movement_date',
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
            'movement_date',
            today()
        );
    }

    public function scopeLatestFirst(
        Builder $query
    ): Builder {

        return $query

            ->orderByDesc(
                'movement_date'
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

    public function isStockIn(): bool
    {
        return $this->quantity > 0;
    }

    public function isStockOut(): bool
    {
        return $this->quantity < 0;
    }

    public function movementDirection(): string
    {
        return $this->quantity >= 0
            ? self::DIRECTION_IN
            : self::DIRECTION_OUT;
    }

    public function quantityAbs(): int
    {
        return abs(
            $this->quantity
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

    public function creatorName(): ?string
    {
        return $this->creator?->name;
    }

    public function hasReference(): bool
    {
        return !empty(
            $this->reference_type
        ) &&
        !empty(
            $this->reference_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helpers
    |--------------------------------------------------------------------------
    */

    public function stockDifference(): int
    {
        return
            $this->stock_after
            - $this->stock_before;
    }

    public function formattedReference(): ?string
    {
        return $this->reference_number
            ?: null;
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