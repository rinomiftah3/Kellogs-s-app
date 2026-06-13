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

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Product Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_ARCHIVED = 'archived';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'category_id',

        'name',

        'slug',

        'short_description',

        'description',

        'thumbnail',

        'status',

        'is_featured',

        'is_active',

        'published_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends
    |--------------------------------------------------------------------------
    */

    protected $appends = [

        'thumbnail_url',

        'primary_image_url',

        'review_count',

        'sku_count',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'is_active' => 'boolean',

            'is_featured' => 'boolean',

            'published_at' => 'datetime',
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

            ->useLogName('product')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Product {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            Category::class
        );
    }

    public function images(): HasMany
    {
        return $this->hasMany(
            ProductImage::class
        );
    }

    public function options(): HasMany
    {
        return $this->hasMany(
            ProductOption::class
        );
    }

    public function skus(): HasMany
    {
        return $this->hasMany(
            ProductSku::class
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(
            ProductReview::class
        );
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(
            Wishlist::class
        );
    }

    public function promoProducts(): HasMany
    {
        return $this->hasMany(
            PromoProduct::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail
            ? asset(
                'storage/' . $this->thumbnail
            )
            : null;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $image = $this->images()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        return $image?->image_url
            ?? $this->thumbnail_url;
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getSkuCountAttribute(): int
    {
        return $this->skus()->count();
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
            fn (Builder $query) =>

            $query->where(function ($q) use ($keyword) {

                $q->where(
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
                    'short_description',
                    'like',
                    "%{$keyword}%"
                )

                ->orWhere(
                    'description',
                    'like',
                    "%{$keyword}%"
                );
            })
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

    public function scopePublished(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'published_at'
        );
    }

    public function scopeFeatured(
        Builder $query
    ): Builder {

        return $query->where(
            'is_featured',
            true
        );
    }

    public function scopeStatus(
        Builder $query,
        string $status
    ): Builder {

        return $query->where(
            'status',
            $status
        );
    }

    public function scopeByCategory(
        Builder $query,
        ?int $categoryId
    ): Builder {

        return $query->when(
            filled($categoryId),
            fn (Builder $query)

                => $query->where(
                    'category_id',
                    $categoryId
                )
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

    public function isFeatured(): bool
    {
        return (bool) $this->is_featured;
    }

    public function isPublished(): bool
    {
        return !is_null(
            $this->published_at
        );
    }

    public function hasImage(): bool
    {
        return !empty(
            $this->thumbnail
        );
    }

    public function hasSku(): bool
    {
        return $this->skus()
            ->exists();
    }

    public function hasReviews(): bool
    {
        return $this->reviews()
            ->exists();
    }

    public function categoryName(): ?string
    {
        return $this->category?->name;
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