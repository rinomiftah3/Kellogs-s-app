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
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string|null $short_description
 * @property string|null $description
 * @property string|null $thumbnail
 * @property string $status
 * @property bool $is_featured
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\Category|null $category
 * @property-read string|null $primary_image_url
 * @property-read int $review_count
 * @property-read int $sku_count
 * @property-read string|null $thumbnail_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductOption> $options
 * @property-read int|null $options_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PromoProduct> $promoProducts
 * @property-read int|null $promo_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductReview> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSku> $skus
 * @property-read int|null $skus_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wishlist> $wishlists
 * @property-read int|null $wishlists_count
 * @method static Builder<static>|Product active()
 * @method static Builder<static>|Product byCategory(?int $categoryId)
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static Builder<static>|Product featured()
 * @method static Builder<static>|Product inactive()
 * @method static Builder<static>|Product newModelQuery()
 * @method static Builder<static>|Product newQuery()
 * @method static Builder<static>|Product onlyTrashed()
 * @method static Builder<static>|Product published()
 * @method static Builder<static>|Product query()
 * @method static Builder<static>|Product search(?string $keyword)
 * @method static Builder<static>|Product status(string $status)
 * @method static Builder<static>|Product whereCategoryId($value)
 * @method static Builder<static>|Product whereCreatedAt($value)
 * @method static Builder<static>|Product whereDeletedAt($value)
 * @method static Builder<static>|Product whereDescription($value)
 * @method static Builder<static>|Product whereId($value)
 * @method static Builder<static>|Product whereIsActive($value)
 * @method static Builder<static>|Product whereIsFeatured($value)
 * @method static Builder<static>|Product whereName($value)
 * @method static Builder<static>|Product wherePublishedAt($value)
 * @method static Builder<static>|Product whereShortDescription($value)
 * @method static Builder<static>|Product whereSlug($value)
 * @method static Builder<static>|Product whereStatus($value)
 * @method static Builder<static>|Product whereThumbnail($value)
 * @method static Builder<static>|Product whereUpdatedAt($value)
 * @method static Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Product withoutTrashed()
 * @mixin \Eloquent
 */
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
        return (int) (
            $this->reviews_count
            ?? $this->reviews()->count()
        );
    }

    public function getSkuCountAttribute(): int
    {
        return (int) (
            $this->skus_count
            ?? $this->skus()->count()
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