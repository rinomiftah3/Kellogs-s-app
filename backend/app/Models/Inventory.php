<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * Inventory Model
 *
 * Enterprise Inventory Management
 *
 * @property int $id
 * @property int $product_sku_id
 * @property int $current_stock
 * @property int $reserved_stock
 * @property int $available_stock
 * @property int $minimum_stock
 * @property int|null $maximum_stock
 * @property int $reorder_point
 * @property bool $allow_backorder
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\ProductSku|null $sku
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockAdjustment> $stockAdjustments
 * @property-read int|null $stock_adjustments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOpname> $stockOpnames
 * @property-read int|null $stock_opnames_count
 * @method static Builder<static>|Inventory active()
 * @method static Builder<static>|Inventory available()
 * @method static \Database\Factories\InventoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Inventory inactive()
 * @method static Builder<static>|Inventory lowStock()
 * @method static Builder<static>|Inventory needReorder()
 * @method static Builder<static>|Inventory newModelQuery()
 * @method static Builder<static>|Inventory newQuery()
 * @method static Builder<static>|Inventory outOfStock()
 * @method static Builder<static>|Inventory query()
 * @method static Builder<static>|Inventory whereAllowBackorder($value)
 * @method static Builder<static>|Inventory whereAvailableStock($value)
 * @method static Builder<static>|Inventory whereCreatedAt($value)
 * @method static Builder<static>|Inventory whereCurrentStock($value)
 * @method static Builder<static>|Inventory whereId($value)
 * @method static Builder<static>|Inventory whereIsActive($value)
 * @method static Builder<static>|Inventory whereMaximumStock($value)
 * @method static Builder<static>|Inventory whereMinimumStock($value)
 * @method static Builder<static>|Inventory whereProductSkuId($value)
 * @method static Builder<static>|Inventory whereReorderPoint($value)
 * @method static Builder<static>|Inventory whereReservedStock($value)
 * @method static Builder<static>|Inventory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Inventory extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Inventory Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_IN_STOCK = 'in_stock';

    public const STATUS_LOW_STOCK = 'low_stock';

    public const STATUS_OUT_OF_STOCK = 'out_of_stock';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_sku_id',

        'current_stock',

        'reserved_stock',

        'available_stock',

        'minimum_stock',

        'maximum_stock',

        'reorder_point',

        'allow_backorder',

        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'current_stock' => 'integer',

            'reserved_stock' => 'integer',

            'available_stock' => 'integer',

            'minimum_stock' => 'integer',

            'maximum_stock' => 'integer',

            'reorder_point' => 'integer',

            'allow_backorder' => 'boolean',

            'is_active' => 'boolean',
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

            ->useLogName('inventory')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Inventory {$eventName}"
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

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(
            StockAdjustment::class
        );
    }

    public function stockOpnames(): HasMany
    {
        return $this->hasMany(
            StockOpname::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(
        Builder $query
    ): Builder {

        return $query->where(
            'is_active',
            true
        );
    }

    public function scopeInactive(
        Builder $query
    ): Builder {

        return $query->where(
            'is_active',
            false
        );
    }

    public function scopeAvailable(
        Builder $query
    ): Builder {

        return $query->where(
            'available_stock',
            '>',
            0
        );
    }

    public function scopeLowStock(
        Builder $query
    ): Builder {

        return $query->whereColumn(
            'available_stock',
            '<=',
            'minimum_stock'
        );
    }

    public function scopeOutOfStock(
        Builder $query
    ): Builder {

        return $query->where(
            'available_stock',
            '<=',
            0
        );
    }

    public function scopeNeedReorder(
        Builder $query
    ): Builder {

        return $query->whereColumn(
            'available_stock',
            '<=',
            'reorder_point'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return (bool)
            $this->is_active;
    }

    public function isLowStock(): bool
    {
        return $this->available_stock
            <= $this->minimum_stock
            && $this->available_stock > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->available_stock <= 0;
    }

    public function canBackorder(): bool
    {
        return (bool)
            $this->allow_backorder;
    }
public function canReduceTo(
    int $stock
): bool {

    return $stock >= $this->reserved_stock;
}
    public function needsReorder(): bool
    {
        return $this->available_stock
            <= $this->reorder_point;
    }

    public function stockStatus(): string
    {
        if ($this->available_stock <= 0) {
            return self::STATUS_OUT_OF_STOCK;
        }

        if (
            $this->available_stock
            <= $this->minimum_stock
        ) {
            return self::STATUS_LOW_STOCK;
        }

        return self::STATUS_IN_STOCK;
    }

    public function skuCode(): ?string
    {
        return $this->sku?->sku;
    }

    public function productName(): ?string
    {
        return $this->sku?->product?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Stock Operations
    |--------------------------------------------------------------------------
    */

    public function increaseStock(
        int $quantity
    ): bool {

        $updated = $this->update([

            'current_stock'
                => $this->current_stock + $quantity,

            'available_stock'
                => $this->available_stock + $quantity,
        ]);

        $this->refresh();

        return $updated;
    }

    public function decreaseStock(
        int $quantity
    ): bool {

        $updated = $this->update([

            'current_stock'
                => max(
                    0,
                    $this->current_stock - $quantity
                ),

            'available_stock'
                => max(
                    0,
                    $this->available_stock - $quantity
                ),
        ]);

        $this->refresh();

        return $updated;
    }

    public function reserveStock(
        int $quantity
    ): bool {

        if (
            $quantity <= 0 ||
            $quantity > $this->available_stock
        ) {
            return false;
        }

        $updated = $this->update([

            'reserved_stock'
                => $this->reserved_stock + $quantity,

            'available_stock'
                => $this->available_stock - $quantity,
        ]);

        $this->refresh();

        return $updated;
    }

    public function releaseReservedStock(
        int $quantity
    ): bool {

        if (
            $quantity <= 0 ||
            $quantity > $this->reserved_stock
        ) {
            return false;
        }

        $updated = $this->update([

            'reserved_stock'
                => max(
                    0,
                    $this->reserved_stock - $quantity
                ),

            'available_stock'
                => $this->available_stock + $quantity,
        ]);

        $this->refresh();

        return $updated;
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