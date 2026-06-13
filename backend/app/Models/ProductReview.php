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
 * ProductReview Model
 *
 * Enterprise Review System
 */
class ProductReview extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'product_id',

        'customer_profile_id',

        'rating',

        'title',

        'review',

        'is_verified_purchase',

        'status',

        'moderation_notes',

        'helpful_count',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'rating' => 'integer',

            'helpful_count' => 'integer',

            'is_verified_purchase' => 'boolean',
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

            ->useLogName('product_review')

            ->logFillable()

            ->logOnlyDirty()

            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])

            ->setDescriptionForEvent(
                fn (string $eventName)
                    => "Product Review {$eventName}"
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            CustomerProfile::class,
            'customer_profile_id'
        );
    }

    public function images(): HasMany
    {
        return $this->hasMany(
            ProductReviewImage::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_APPROVED
        );
    }

    public function scopePending(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }

    public function scopeRejected(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_REJECTED
        );
    }

    public function scopeVerifiedPurchase(
        Builder $query
    ): Builder {

        return $query->where(
            'is_verified_purchase',
            true
        );
    }

    public function scopeWithImages(
        Builder $query
    ): Builder {

        return $query->has(
            'images'
        );
    }

    public function scopeWithoutImages(
        Builder $query
    ): Builder {

        return $query->doesntHave(
            'images'
        );
    }

    public function scopeHelpful(
        Builder $query
    ): Builder {

        return $query->where(
            'helpful_count',
            '>',
            0
        );
    }

    public function scopeHighestRated(
        Builder $query
    ): Builder {

        return $query->orderByDesc(
            'rating'
        );
    }

    public function scopeLowestRated(
        Builder $query
    ): Builder {

        return $query->orderBy(
            'rating'
        );
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

    public function scopeByCustomer(
        Builder $query,
        ?int $customerId
    ): Builder {

        return $query->when(
            filled($customerId),

            fn (Builder $query)

                => $query->where(
                    'customer_profile_id',
                    $customerId
                )
        );
    }

    public function scopeRating(
        Builder $query,
        ?int $rating
    ): Builder {

        return $query->when(
            filled($rating),

            fn (Builder $query)

                => $query->where(
                    'rating',
                    $rating
                )
        );
    }

    public function scopeLatestReview(
        Builder $query
    ): Builder {

        return $query->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function customerName(): ?string
    {
        return $this->customer?->full_name;
    }

    public function productName(): ?string
    {
        return $this->product?->name;
    }

    public function ratingStars(): string
    {
        return str_repeat(
            '★',
            $this->rating
        );
    }

    public function ratingPercentage(): int
    {
        return (int) (
            ($this->rating / 5) * 100
        );
    }

    public function hasImages(): bool
    {
        return $this->images()
            ->exists();
    }

    public function hasReviewText(): bool
    {
        return !empty(
            $this->review
        );
    }

    public function imagesCount(): int
    {
        return $this->images()
            ->count();
    }

    public function isApproved(): bool
    {
        return $this->status ===
            self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status ===
            self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status ===
            self::STATUS_REJECTED;
    }

    public function isVerifiedPurchase(): bool
    {
        return (bool)
            $this->is_verified_purchase;
    }

    public function approve(): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
        ]);
    }

    public function reject(
        ?string $notes = null
    ): bool {

        return $this->update([
            'status' => self::STATUS_REJECTED,
            'moderation_notes' => $notes,
        ]);
    }

    public function increaseHelpful(): void
    {
        $this->increment(
            'helpful_count'
        );

        $this->refresh();
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