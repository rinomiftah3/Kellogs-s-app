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
 *
 * @property int $id
 * @property int $product_id
 * @property int $customer_profile_id
 * @property int $rating
 * @property string|null $title
 * @property string|null $review
 * @property bool $is_verified_purchase
 * @property string $status
 * @property string|null $moderation_notes
 * @property int $helpful_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\CustomerProfile|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductReviewImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Product|null $product
 * @method static Builder<static>|ProductReview approved()
 * @method static Builder<static>|ProductReview byCustomer(?int $customerId)
 * @method static Builder<static>|ProductReview byProduct(?int $productId)
 * @method static Builder<static>|ProductReview helpful()
 * @method static Builder<static>|ProductReview highestRated()
 * @method static Builder<static>|ProductReview latestReview()
 * @method static Builder<static>|ProductReview lowestRated()
 * @method static Builder<static>|ProductReview newModelQuery()
 * @method static Builder<static>|ProductReview newQuery()
 * @method static Builder<static>|ProductReview onlyTrashed()
 * @method static Builder<static>|ProductReview pending()
 * @method static Builder<static>|ProductReview query()
 * @method static Builder<static>|ProductReview rating(?int $rating)
 * @method static Builder<static>|ProductReview rejected()
 * @method static Builder<static>|ProductReview verifiedPurchase()
 * @method static Builder<static>|ProductReview whereCreatedAt($value)
 * @method static Builder<static>|ProductReview whereCustomerProfileId($value)
 * @method static Builder<static>|ProductReview whereDeletedAt($value)
 * @method static Builder<static>|ProductReview whereHelpfulCount($value)
 * @method static Builder<static>|ProductReview whereId($value)
 * @method static Builder<static>|ProductReview whereIsVerifiedPurchase($value)
 * @method static Builder<static>|ProductReview whereModerationNotes($value)
 * @method static Builder<static>|ProductReview whereProductId($value)
 * @method static Builder<static>|ProductReview whereRating($value)
 * @method static Builder<static>|ProductReview whereReview($value)
 * @method static Builder<static>|ProductReview whereStatus($value)
 * @method static Builder<static>|ProductReview whereTitle($value)
 * @method static Builder<static>|ProductReview whereUpdatedAt($value)
 * @method static Builder<static>|ProductReview withImages()
 * @method static Builder<static>|ProductReview withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|ProductReview withoutImages()
 * @method static Builder<static>|ProductReview withoutTrashed()
 * @mixin \Eloquent
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

    public function customerProfile(): BelongsTo
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
        return $this->customerProfile?->full_name;
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