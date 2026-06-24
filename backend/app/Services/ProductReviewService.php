<?php

namespace App\Services;

use App\Models\CustomerProfile;
use App\Models\Product;
use App\Models\ProductReview;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductReviewService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'product',
        'customerProfile',
        'images',
    ];

    /**
     * Get paginated reviews.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return ProductReview::query()

            ->with($this->relations)

            ->withCount('images')

            ->when(
                filled($filters['product_id'] ?? null),
                fn ($query) => $query->byProduct(
                    $filters['product_id']
                )
            )

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
                )
            )

            ->when(
                filled($filters['rating'] ?? null),
                fn ($query) => $query->rating(
                    $filters['rating']
                )
            )

            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->where(
                    'status',
                    $filters['status']
                )
            )

            ->when(
                    !is_null(
                        $filters['verified_purchase']
                        ?? null
                    ),
                    fn ($query) =>
                        $filters['verified_purchase']
                            ? $query->verifiedPurchase()
                            : $query->where(
                                'is_verified_purchase',
                                false
                            )
                )

                ->when(
                    !is_null(
                        $filters['has_images']
                        ?? null
                    ),
                    fn ($query) =>
                        $filters['has_images']
                            ? $query->withImages()
                            : $query->withoutImages()
                )

            ->latestReview()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all reviews.
     */
    public function all(
        array $filters = []
    ): Collection {

        return ProductReview::query()

            ->with($this->relations)

            ->withCount('images')

            ->when(
                filled($filters['product_id'] ?? null),
                fn ($query) => $query->byProduct(
                    $filters['product_id']
                )
            )

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
                )
            )

            ->when(
                filled($filters['rating'] ?? null),
                fn ($query) => $query->rating(
                    $filters['rating']
                )
            )

            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->where(
                    'status',
                    $filters['status']
                )
            )

            ->when(
                    !is_null(
                        $filters['verified_purchase']
                        ?? null
                    ),
                    fn ($query) =>
                        $filters['verified_purchase']
                            ? $query->verifiedPurchase()
                            : $query->where(
                                'is_verified_purchase',
                                false
                            )
                )

                ->when(
                    !is_null(
                        $filters['has_images']
                        ?? null
                    ),
                    fn ($query) =>
                        $filters['has_images']
                            ? $query->withImages()
                            : $query->withoutImages()
                )

            ->latestReview()

            ->get();
    }

    /**
     * Find review by ID.
     */
    public function find(
        int $id
    ): ?ProductReview {

        return ProductReview::query()

            ->with($this->relations)

            ->withCount('images')

            ->find($id);
    }

    /**
     * Find review or fail.
     */
    public function findOrFail(
        int $id
    ): ProductReview {

        return ProductReview::query()

            ->with($this->relations)

            ->withCount('images')

            ->findOrFail($id);
    }

    /**
     * Get reviews by product.
     */
    public function getByProduct(
        Product|int $product,
        bool $approvedOnly = true
    ): Collection {

        $productId = $product instanceof Product
            ? $product->id
            : $product;

        return ProductReview::query()

            ->with($this->relations)

            ->withCount('images')

            ->byProduct($productId)

            ->when(
                $approvedOnly,
                fn ($query) => $query->approved()
            )

            ->latestReview()

            ->get();
    }

    /**
     * Get reviews by customer.
     */
    public function getByCustomer(
        CustomerProfile|int $customer,
        bool $approvedOnly = false
    ): Collection {

        $customerId = $customer instanceof CustomerProfile
            ? $customer->id
            : $customer;

        return ProductReview::query()

            ->with($this->relations)

            ->withCount('images')

            ->byCustomer($customerId)

            ->when(
                $approvedOnly,
                fn ($query) => $query->approved()
            )

            ->latestReview()

            ->get();
    }
    /**
     * Create review.
     */
    public function create(
        array $data
    ): ProductReview {

        return DB::transaction(
            function () use ($data) {

                Product::query()
                    ->findOrFail(
                        $data['product_id']
                    );
                /*
                |--------------------------------------------------------------------------
                | One Review Per Product
                |--------------------------------------------------------------------------
                */

                if (! auth()->user()->customerProfile) {

                    throw new \RuntimeException(
                        'Customer profile tidak ditemukan.'
                    );

                }

                $customerId =
                    auth()
                        ->user()
                        ->customerProfile
                        ->id;

                $exists = ProductReview::query()

                    ->where(
                        'product_id',
                        $data['product_id']
                    )

                    ->where(
                        'customer_profile_id',
                        $customerId
                    )

                    ->exists();

                if ($exists) {

                    throw new \RuntimeException(
                        'Customer sudah memberikan review untuk produk ini.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | TODO: Verified Purchase Validation
                |--------------------------------------------------------------------------
                |
                | Saat OrderService selesai,
                | validasi bahwa customer benar-benar
                | pernah membeli produk ini.
                |
                */

                /*
                |--------------------------------------------------------------------------
                | Create Review
                |--------------------------------------------------------------------------
                */

                $review = ProductReview::create([

                    'product_id'
                        => $data['product_id'],

                    'customer_profile_id'
                        => auth()->user()
                                ->customerProfile
                                ->id,

                    'rating'
                        => $data['rating'],

                    'title'
                        => $data['title'] ?? null,

                    'review'
                        => $data['review'] ?? null,

                    'is_verified_purchase'
                        => $data['is_verified_purchase']
                        ?? false,

                    'status'
                        => ProductReview::STATUS_PENDING,

                    'moderation_notes'
                        => null,

                    'helpful_count'
                        => 0,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Images
                |--------------------------------------------------------------------------
                */

                if (
                    ! empty($data['images'])
                ) {

                    $this->syncImages(
                        $review,
                        $data['images']
                    );
                }

                return $review

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }

    /**
     * Update review.
     *
     * Business Rule:
     * Customer hanya dapat mengubah
     * isi review.
     *
     * Moderation dilakukan melalui
     * approve() atau reject().
     */
    public function update(
        ProductReview|int $review,
        array $data
    ): ProductReview {

        return DB::transaction(
            function () use (
                $review,
                $data
            ) {

                $review = $review instanceof ProductReview
                    ? $review
                    : $this->findOrFail(
                        $review
                    );

                $review->update([

                'rating' =>
                    $data['rating']
                    ?? $review->rating,

                'title' =>
                    array_key_exists(
                        'title',
                        $data
                    )
                    ? $data['title']
                    : $review->title,

                'review' =>
                    array_key_exists(
                        'review',
                        $data
                    )
                    ? $data['review']
                    : $review->review,

                /*
                |--------------------------------------------------------------------------
                | Re Moderation
                |--------------------------------------------------------------------------
                */

                'status' =>
                    ProductReview::STATUS_PENDING,

                'moderation_notes' =>
                    null,

                /*
                |--------------------------------------------------------------------------
                | Helpful Reset
                |--------------------------------------------------------------------------
                */

                'helpful_count' => 0,
            ]);
                if (
                    array_key_exists(
                        'images',
                        $data
                    )
                ) {

                    $this->replaceImages(
                        $review,
                        $data['images']
                    );
                }
                                    return $review

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    
    /**
     * Delete review.
     */
    public function delete(
        ProductReview|int $review
    ): bool {

        return DB::transaction(
            function () use ($review) {

                $review = $review instanceof ProductReview
                    ? $review
                    : $this->findOrFail(
                        $review
                    );

                /*
                |--------------------------------------------------------------------------
                | Delete Image Files
                |--------------------------------------------------------------------------
                */

                foreach (
                    $review->images as $image
                ) {

                    if (
                        ! empty(
                            $image->image_url
                        )
                        &&
                        ! str_starts_with(
                            $image->image_url,
                            'http'
                        )
                        &&
                        Storage::disk('public')
                            ->exists(
                                $image->image_url
                            )
                    ) {

                        Storage::disk('public')
                            ->delete(
                                $image->image_url
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Delete Image Records
                |--------------------------------------------------------------------------
                */

                $review->images()
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | Delete Review
                |--------------------------------------------------------------------------
                */

                return (bool)
                    $review->delete();
            }
        );
    }

    /*
|--------------------------------------------------------------------------
| Replace All Images
|--------------------------------------------------------------------------
|
| Business Rule:
| Update review akan menghapus seluruh gambar lama
| kemudian mengganti dengan gambar baru.
|
| Frontend wajib mengirim seluruh gambar terbaru.
|
*/
protected function replaceImages(
    ProductReview $review,
    array $images
): void
{
    foreach ($review->images as $image) {

        if (
            ! empty($image->image_url)
            &&
            ! str_starts_with(
                $image->image_url,
                'http'
            )
            &&
            Storage::disk('public')->exists(
                $image->image_url
            )
        ) {

            Storage::disk('public')->delete(
                $image->image_url
            );
        }
    }

    $review->images()->delete();

    $this->syncImages(
        $review,
        $images
    );
}
    /**
     * Approve review.
     */
    public function approve(
        ProductReview|int $review
    ): ProductReview {

        return DB::transaction(
            function () use ($review) {

                $review = $review instanceof ProductReview
                    ? $review
                    : $this->findOrFail(
                        $review
                    );

                $review->update([

                    'status'
                        => ProductReview::STATUS_APPROVED,

                    'moderation_notes'
                        => null,
                ]);

                return $review

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }

    /**
     * Reject review.
     */
    public function reject(
        ProductReview|int $review,
        string $notes
    ): ProductReview {

        return DB::transaction(
            function () use (
                $review,
                $notes
            ) {

                $review = $review instanceof ProductReview
                    ? $review
                    : $this->findOrFail(
                        $review
                    );

                $review->update([

                    'status'
                        => ProductReview::STATUS_REJECTED,

                    'moderation_notes'
                        => trim(
                            $notes
                        ),
                ]);

                return $review

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }

    /**
     * Increase helpful count.
     */
    public function increaseHelpful(
        ProductReview|int $review
    ): ProductReview {

        return DB::transaction(
            function () use ($review) {

                $review = $review instanceof ProductReview
                    ? $review
                    : $this->findOrFail(
                        $review
                    );

                $review->increment(
                    'helpful_count'
                );

                return $review

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    /**
     * Get average rating by product.
     */
    public function getAverageRating(
        Product|int $product
    ): float {

        $productId = $product instanceof Product
            ? $product->id
            : $product;

        return round(

            (float) ProductReview::query()

                ->approved()

                ->byProduct($productId)

                ->avg('rating'),

            2
        );
    }

    /**
     * Get rating distribution.
     *
     * Example:
     *
     * [
     *     5 => 10,
     *     4 => 6,
     *     3 => 2,
     *     2 => 0,
     *     1 => 1,
     * ]
     */
    public function getRatingDistribution(
        Product|int $product
    ): array {

        $productId = $product instanceof Product
            ? $product->id
            : $product;

        $distribution = ProductReview::query()

            ->approved()

            ->byProduct($productId)

            ->selectRaw(
                'rating, COUNT(*) as total'
            )

            ->groupBy('rating')

            ->pluck(
                'total',
                'rating'
            )

            ->toArray();

        return [

            5 => (int) ($distribution[5] ?? 0),

            4 => (int) ($distribution[4] ?? 0),

            3 => (int) ($distribution[3] ?? 0),

            2 => (int) ($distribution[2] ?? 0),

            1 => (int) ($distribution[1] ?? 0),
        ];
    }
    /**
     * Sync review images.
     *
     * Business Rule:
     * Maksimal 5 gambar per review.
     */
    protected function syncImages(
        ProductReview $review,
        array $images
    ): void {

        $images = array_slice(
            array_values($images),
            0,
            5
        );

        foreach (
            $images as $index => $image
        ) {

            $review->images()->create([

                'image_url'
                    => $this->storeImage(
                        $image
                    ),

                'alt_text'
                    => $review->title,

                'sort_order'
                    => $index + 1,

                'is_active'
                    => true,
            ]);
        }
    }

    /**
     * Store image.
     */
    protected function storeImage(
        mixed $image
    ): string {

        /*
        |--------------------------------------------------------------------------
        | Uploaded File
        |--------------------------------------------------------------------------
        */

        if (
            $image instanceof UploadedFile
        ) {

            return $image->store(
                'product-reviews',
                'public'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Existing URL / Path
        |--------------------------------------------------------------------------
        */

        return (string) $image;
    }
}