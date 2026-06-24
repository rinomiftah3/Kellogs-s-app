<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * ProductReviewImage Model
 *
 * Review Image Management
 *
 * Enterprise Review System
 *
 * @property int $id
 * @property int $product_review_id
 * @property string $image_url
 * @property string|null $alt_text
 * @property int $sort_order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\ProductReview|null $review
 * @method static Builder<static>|ProductReviewImage active()
 * @method static Builder<static>|ProductReviewImage byReview(?int $reviewId)
 * @method static Builder<static>|ProductReviewImage hasAltText()
 * @method static Builder<static>|ProductReviewImage inactive()
 * @method static Builder<static>|ProductReviewImage latest()
 * @method static Builder<static>|ProductReviewImage newModelQuery()
 * @method static Builder<static>|ProductReviewImage newQuery()
 * @method static Builder<static>|ProductReviewImage ordered()
 * @method static Builder<static>|ProductReviewImage query()
 * @method static Builder<static>|ProductReviewImage whereAltText($value)
 * @method static Builder<static>|ProductReviewImage whereCreatedAt($value)
 * @method static Builder<static>|ProductReviewImage whereId($value)
 * @method static Builder<static>|ProductReviewImage whereImageUrl($value)
 * @method static Builder<static>|ProductReviewImage whereIsActive($value)
 * @method static Builder<static>|ProductReviewImage whereProductReviewId($value)
 * @method static Builder<static>|ProductReviewImage whereSortOrder($value)
 * @method static Builder<static>|ProductReviewImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductReviewImage extends Model
{
    use HasFactory;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_review_id',

        'image_url',

        'alt_text',

        'sort_order',

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

            ->useLogName('product_review_image')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Product Review Image {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function review(): BelongsTo
    {
        return $this->belongsTo(
            ProductReview::class,
            'product_review_id'
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

        return $query->orderBy(
            'sort_order'
        );
    }

    public function scopeLatest(
        Builder $query
    ): Builder {

        return $query->latest();
    }

    public function scopeHasAltText(
        Builder $query
    ): Builder {

        return $query->whereNotNull(
            'alt_text'
        );
    }

    public function scopeByReview(
        Builder $query,
        ?int $reviewId
    ): Builder {

        return $query->when(
            filled($reviewId),

            fn (Builder $query)

                => $query->where(
                    'product_review_id',
                    $reviewId
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
        return (bool)
            $this->is_active;
    }

    public function hasAltText(): bool
    {
        return !empty(
            $this->alt_text
        );
    }

    public function fullImageUrl(): string
    {
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

    public function reviewId(): ?int
    {
        return $this->review?->id;
    }

    public function reviewTitle(): ?string
    {
        return $this->review?->title;
    }

    public function reviewRating(): ?int
    {
        return $this->review?->rating;
    }

    public function productName(): ?string
    {
        return $this->review?->product?->name;
    }

    public function customerName(): ?string
    {
        return $this->review?->customerProfile?->full_name;
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