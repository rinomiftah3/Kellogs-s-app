<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * @property int $id
 * @property int $product_id
 * @property string $image_url
 * @property string|null $alt_text
 * @property int $sort_order
 * @property bool $is_primary
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read string|null $full_image_url
 * @property-read \App\Models\Product|null $product
 * @method static Builder<static>|ProductImage active()
 * @method static Builder<static>|ProductImage byProduct(?int $productId)
 * @method static Builder<static>|ProductImage inactive()
 * @method static Builder<static>|ProductImage newModelQuery()
 * @method static Builder<static>|ProductImage newQuery()
 * @method static Builder<static>|ProductImage ordered()
 * @method static Builder<static>|ProductImage primary()
 * @method static Builder<static>|ProductImage primaryFirst()
 * @method static Builder<static>|ProductImage query()
 * @method static Builder<static>|ProductImage whereAltText($value)
 * @method static Builder<static>|ProductImage whereCreatedAt($value)
 * @method static Builder<static>|ProductImage whereId($value)
 * @method static Builder<static>|ProductImage whereImageUrl($value)
 * @method static Builder<static>|ProductImage whereIsActive($value)
 * @method static Builder<static>|ProductImage whereIsPrimary($value)
 * @method static Builder<static>|ProductImage whereProductId($value)
 * @method static Builder<static>|ProductImage whereSortOrder($value)
 * @method static Builder<static>|ProductImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductImage extends Model
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

        'image_url',

        'alt_text',

        'sort_order',

        'is_primary',

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

            'is_primary' => 'boolean',

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

            ->useLogName('product_image')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Product Image {$eventName}"
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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullImageUrlAttribute(): ?string
    {
        if (empty($this->image_url)) {
            return null;
        }

        if (
            str_starts_with(
                $this->image_url,
                'http'
            )
        ) {
            return $this->image_url;
        }

        return asset(
            'storage/' .
            ltrim(
                $this->image_url,
                '/'
            )
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

    public function scopePrimary(
        Builder $query
    ): Builder {

        return $query->where(
            'is_primary',
            true
        );
    }

    public function scopeOrdered(
        Builder $query
    ): Builder {

        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopePrimaryFirst(
        Builder $query
    ): Builder {

        return $query
            ->orderByDesc('is_primary')
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

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPrimary(): bool
    {
        return (bool) $this->is_primary;
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function hasAltText(): bool
    {
        return filled(
            $this->alt_text
        );
    }

    public function hasImage(): bool
    {
        return filled(
            $this->image_url
        );
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