<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * Category Model
 *
 * Product Category Management
 * Enterprise Ready
 */
class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'parent_id',

        'name',

        'slug',

        'description',

        'image',

        'sort_order',

        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appended Attributes
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'products_count',

        'children_count',

        'image_url',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'parent_id' => 'integer',

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

            ->useLogName('category')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Category {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        );
    }

    public function products(): HasMany
    {
        return $this->hasMany(
            Product::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getProductsCountAttribute(): int
    {
        if (
            array_key_exists(
                'products_count',
                $this->attributes
            )
        ) {
            return (int)
                $this->attributes['products_count'];
        }

        return $this->products()->count();
    }

    public function getChildrenCountAttribute(): int
    {
        if (
            array_key_exists(
                'children_count',
                $this->attributes
            )
        ) {
            return (int)
                $this->attributes['children_count'];
        }

        return $this->children()->count();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (
            empty($this->image)
        ) {
            return null;
        }

        if (
            str_starts_with(
                $this->image,
                'http'
            )
        ) {
            return $this->image;
        }

        return asset(
            'storage/' .
            ltrim(
                $this->image,
                '/'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(
        Builder $query,
        ?string $keyword
    ): Builder {

        return $query->when(
            filled($keyword),
            fn (Builder $query)

                => $query->where(
                    fn ($q)

                        => $q->where(
                            'name',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'slug',
                            'like',
                            "%{$keyword}%"
                        )

                        ->orWhere(
                            'description',
                            'like',
                            "%{$keyword}%"
                        )
                )
        );
    }

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

    public function scopeParentCategories(
        Builder $query
    ): Builder {

        return $query->whereNull(
            'parent_id'
        );
    }

    public function scopeChildCategories(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'parent_id'
        );
    }

    public function scopeOrdered(
        Builder $query
    ): Builder {

        return $query
            ->orderBy(
                'sort_order'
            )
            ->orderBy(
                'name'
            );
    }

    public function scopeLatest(
        Builder $query
    ): Builder {

        return $query->latest();
    }

    public function scopeOldest(
        Builder $query
    ): Builder {

        return $query->oldest();
    }

    public function scopeHasProducts(
        Builder $query
    ): Builder {

        return $query->has(
            'products'
        );
    }

    public function scopeEmpty(
        Builder $query
    ): Builder {

        return $query->doesntHave(
            'products'
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

    public function isParent(): bool
    {
        return is_null(
            $this->parent_id
        );
    }

    public function isChild(): bool
    {
        return !is_null(
            $this->parent_id
        );
    }

    public function hasChildren(): bool
    {
        return $this->children()
            ->exists();
    }

    public function hasProducts(): bool
    {
        return $this->products()
            ->exists();
    }

    public function isEmpty(): bool
    {
        return !$this->hasProducts();
    }

    public function hasDescription(): bool
    {
        return !empty(
            $this->description
        );
    }

    public function hasImage(): bool
    {
        return !empty(
            $this->image
        );
    }

    public function productsCount(): int
    {
        return $this->products()
            ->count();
    }

    public function childrenCount(): int
    {
        return $this->children()
            ->count();
    }

    public function parentName(): ?string
    {
        return $this->parent?->name;
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}