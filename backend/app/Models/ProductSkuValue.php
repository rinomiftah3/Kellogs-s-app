<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * ProductSkuValue Model
 *
 * Bridge:
 * ProductSku <-> ProductOptionValue
 *
 * Enterprise SKU Architecture
 */
class ProductSkuValue extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_sku_id',

        'product_option_value_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()

            ->useLogName('product_sku_value')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Product SKU Value {$eventName}"
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

    public function optionValue(): BelongsTo
    {
        return $this->belongsTo(
            ProductOptionValue::class,
            'product_option_value_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeLatest(
        Builder $query
    ): Builder {

        return $query->latest();
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

    public function scopeByOptionValue(
        Builder $query,
        ?int $optionValueId
    ): Builder {

        return $query->when(
            filled($optionValueId),
            fn (Builder $query)

                => $query->where(
                    'product_option_value_id',
                    $optionValueId
                )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function skuId(): ?int
    {
        return $this->product_sku_id;
    }

    public function optionValueId(): ?int
    {
        return $this->product_option_value_id;
    }

    public function skuCode(): ?string
    {
        return $this->sku?->sku;
    }

    public function optionValueName(): ?string
    {
        return $this->optionValue?->value;
    }

    public function optionName(): ?string
    {
        return $this->optionValue?->option?->name;
    }

    public function productName(): ?string
    {
        return $this->sku?->product?->name;
    }

    public function displayLabel(): string
    {
        $option = $this->optionName();
        $value  = $this->optionValueName();

        return trim(
            "{$option}: {$value}"
        );
    }

    public function isValid(): bool
    {
        return !is_null($this->sku)
            && !is_null($this->optionValue);
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