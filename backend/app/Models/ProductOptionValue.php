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
 * @property int $id
 * @property int $product_option_id
 * @property string $value
 * @property string|null $code
 * @property int $sort_order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\ProductOption $option
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSkuValue> $skuValues
 * @property-read int|null $sku_values_count
 * @method static Builder<static>|ProductOptionValue active()
 * @method static Builder<static>|ProductOptionValue byOption(?int $optionId)
 * @method static Builder<static>|ProductOptionValue inactive()
 * @method static Builder<static>|ProductOptionValue newModelQuery()
 * @method static Builder<static>|ProductOptionValue newQuery()
 * @method static Builder<static>|ProductOptionValue ordered()
 * @method static Builder<static>|ProductOptionValue query()
 * @method static Builder<static>|ProductOptionValue search(?string $keyword)
 * @method static Builder<static>|ProductOptionValue unused()
 * @method static Builder<static>|ProductOptionValue used()
 * @method static Builder<static>|ProductOptionValue whereCode($value)
 * @method static Builder<static>|ProductOptionValue whereCreatedAt($value)
 * @method static Builder<static>|ProductOptionValue whereId($value)
 * @method static Builder<static>|ProductOptionValue whereIsActive($value)
 * @method static Builder<static>|ProductOptionValue whereProductOptionId($value)
 * @method static Builder<static>|ProductOptionValue whereSortOrder($value)
 * @method static Builder<static>|ProductOptionValue whereUpdatedAt($value)
 * @method static Builder<static>|ProductOptionValue whereValue($value)
 * @mixin \Eloquent
 */
class ProductOptionValue extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_option_id',

        'value',

        'code',

        'sort_order',

        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'sort_order' => 'integer',

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

            ->useLogName('product_option_value')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Product Option Value {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function option(): BelongsTo
    {
        return $this->belongsTo(
            ProductOption::class,
            'product_option_id'
        );
    }

    public function skuValues(): HasMany
    {
        return $this->hasMany(
            ProductSkuValue::class
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

    public function scopeOrdered(
        Builder $query
    ): Builder {

        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeByOption(
        Builder $query,
        ?int $optionId
    ): Builder {

        return $query->when(
            filled($optionId),
            fn (Builder $query)

                => $query->where(
                    'product_option_id',
                    $optionId
                )
        );
    }

    public function scopeUsed(
        Builder $query
    ): Builder {

        return $query->has(
            'skuValues'
        );
    }

    public function scopeUnused(
        Builder $query
    ): Builder {

        return $query->doesntHave(
            'skuValues'
        );
    }

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(
            filled($keyword),
            fn (Builder $query)

                => $query->where(function ($q) use ($keyword) {

                    $q->where(
                        'value',
                        'like',
                        "%{$keyword}%"
                    )

                    ->orWhere(
                        'code',
                        'like',
                        "%{$keyword}%"
                    );
                })
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function hasCode(): bool
    {
        return !empty(
            $this->code
        );
    }

    public function hasSku(): bool
    {
        return $this->skuValues()
            ->exists();
    }

    public function isUsed(): bool
    {
        return $this->hasSku();
    }

    public function skuCount(): int
    {
        return $this->skuValues()
            ->count();
    }

    public function optionName(): ?string
    {
        return $this->option?->name;
    }

    public function optionCode(): ?string
    {
        return $this->option?->code;
    }

    public function productName(): ?string
    {
        return $this->option?->product?->name;
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