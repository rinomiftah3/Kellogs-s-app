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
        return $this->review?->customer?->full_name;
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