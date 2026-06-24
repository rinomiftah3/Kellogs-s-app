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
 * @property int $product_id
 * @property string $name
 * @property string|null $code
 * @property int $sort_order
 * @property bool $is_required
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOptionValue> $values
 * @property-read int|null $values_count
 * @method static Builder<static>|ProductOption active()
 * @method static Builder<static>|ProductOption byProduct(?int $productId)
 * @method static Builder<static>|ProductOption inactive()
 * @method static Builder<static>|ProductOption newModelQuery()
 * @method static Builder<static>|ProductOption newQuery()
 * @method static Builder<static>|ProductOption optional()
 * @method static Builder<static>|ProductOption ordered()
 * @method static Builder<static>|ProductOption query()
 * @method static Builder<static>|ProductOption required()
 * @method static Builder<static>|ProductOption requiredFirst()
 * @method static Builder<static>|ProductOption search(?string $keyword)
 * @method static Builder<static>|ProductOption whereCode($value)
 * @method static Builder<static>|ProductOption whereCreatedAt($value)
 * @method static Builder<static>|ProductOption whereId($value)
 * @method static Builder<static>|ProductOption whereIsActive($value)
 * @method static Builder<static>|ProductOption whereIsRequired($value)
 * @method static Builder<static>|ProductOption whereName($value)
 * @method static Builder<static>|ProductOption whereProductId($value)
 * @method static Builder<static>|ProductOption whereSortOrder($value)
 * @method static Builder<static>|ProductOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductOption extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_id',

        'name',

        'code',

        'sort_order',

        'is_required',

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

            'is_required' => 'boolean',

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

            ->useLogName('product_option')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Product Option {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Product::class
        );
    }

    public function values(): HasMany
    {
        return $this->hasMany(
            ProductOptionValue::class
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

    public function scopeRequired(
        Builder $query
    ): Builder {

        return $query->where(
            'is_required',
            true
        );
    }

    public function scopeOptional(
        Builder $query
    ): Builder {

        return $query->where(
            'is_required',
            false
        );
    }

    public function scopeRequiredFirst(
        Builder $query
    ): Builder {

        return $query
            ->orderByDesc('is_required')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeOrdered(
        Builder $query
    ): Builder {

        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeByProduct(
        Builder $query,
        ?int $productId
    ): Builder {

        return $query->when(
            filled($productId),
            fn (Builder $query)

                => $query->where(
                    'product_id',
                    $productId
                )
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
                        'name',
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

    public function isRequired(): bool
    {
        return (bool) $this->is_required;
    }

    public function isOptional(): bool
    {
        return !$this->is_required;
    }

    public function hasValues(): bool
    {
        return $this->values()
            ->exists();
    }

    public function valuesCount(): int
    {
        return $this->values()
            ->count();
    }

    public function productName(): ?string
    {
        return $this->product?->name;
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