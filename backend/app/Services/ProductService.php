<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Validation\ValidationException;

class ProductService
{
    /*
    |--------------------------------------------------------------------------
    | Product List
    |--------------------------------------------------------------------------
    */

    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        $query = Product::query()

        ->with([
            'category',
        ])

        ->withCount([
            'reviews',
            'skus',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Filtering
        |--------------------------------------------------------------------------
        */

        $query->search(
            $filters['search'] ?? null
        );

        $query->byCategory(
            $filters['category_id'] ?? null
        );

        if (filled($filters['status'] ?? null)) {

            $query->status(
                $filters['status']
            );
        }

        if (
            ! is_null(
                $filters['is_active'] ?? null
            )
        ) {

            $filters['is_active']
                ? $query->active()
                : $query->inactive();
        }

        if (
            ! is_null(
                $filters['is_featured'] ?? null
            )
        ) {

            if ($filters['is_featured']) {

                $query->featured();

            } else {

                $query->where(
                    'is_featured',
                    false
                );
            }
        }

        if (
            ! is_null(
                $filters['published'] ?? null
            )
        ) {

            if ($filters['published']) {

                $query->published();

            } else {

                $query->whereNull(
                    'published_at'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort =
            $filters['sort']
            ?? 'latest';

        $direction =
            $filters['direction']
            ?? 'desc';

        match ($sort) {

            'oldest'
                => $query->oldest(),

            'name'
                => $query->orderBy(
                    'name',
                    $direction
                ),

            'published_at'
                => $query->orderBy(
                    'published_at',
                    $direction
                ),

            'created_at'
                => $query->orderBy(
                    'created_at',
                    $direction
                ),

            'updated_at'
                => $query->orderBy(
                    'updated_at',
                    $direction
                ),

            default
                => $query->latest(),
        };

        return $query->paginate(
            $perPage
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Product Detail
    |--------------------------------------------------------------------------
    */

    public function find(
        Product $product
    ): Product {

        return $product->load([
            'category',
            'images',
            'skus',
            'reviews',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Product
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data,
        ?UploadedFile $thumbnail,
        User $actor,
        Request $request
    ): Product {

        return DB::transaction(

            function () use (
                $data,
                $thumbnail,
                $actor,
                $request
            ) {

                $data['slug'] =
                    $this->generateUniqueSlug(
                        $data['name']
                    );

                $this->validateFeaturedProduct(
                    $data
                );

                if ($thumbnail) {

                    $data['thumbnail'] =
                        $this->storeThumbnail(
                            $thumbnail
                        );
                }

                $product =
                    Product::create(
                        $data
                    );

                activity()

                    ->causedBy($actor)

                    ->performedOn($product)

                    ->event(
                        'product_created'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'attributes' =>
                            $product->only([

                                'id',

                                'category_id',

                                'name',

                                'slug',

                                'status',

                                'is_featured',

                                'is_active',

                                'published_at',
                            ]),
                    ])

                    ->log(
                        'Product created'
                    );

                $this->clearCaches();

                return $product->load(
                    'category'
                );
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Product
    |--------------------------------------------------------------------------
    */

    public function update(
        Product $product,
        array $data,
        ?UploadedFile $thumbnail,
        User $actor,
        Request $request
    ): Product {

        return DB::transaction(

            function () use (
                $product,
                $data,
                $thumbnail,
                $actor,
                $request
            ) {

                $oldData =
                    $product->only([

                        'id',

                        'category_id',

                        'name',

                        'slug',

                        'thumbnail',

                        'status',

                        'is_featured',

                        'is_active',

                        'published_at',
                    ]);

                if (
                    isset($data['name'])
                    &&
                    $data['name']
                    !== $product->name
                ) {

                    $data['slug'] =
                        $this->generateUniqueSlug(
                            $data['name'],
                            $product->id
                        );
                }

                $this->validateFeaturedProduct(
                    array_merge(
                        [
                            'is_featured' =>
                                $product->is_featured,

                            'is_active' =>
                                $product->is_active,
                        ],
                        $data
                    )
                );

                if ($thumbnail) {

                    $this->deleteThumbnail(
                        $product
                    );

                    $data['thumbnail'] =
                        $this->storeThumbnail(
                            $thumbnail
                        );
                }

                $product->update(
                    $data
                );

                activity()

                    ->causedBy($actor)

                    ->performedOn($product)

                    ->event(
                        'product_updated'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,

                        'new' =>
                            $product->fresh()
                                ->only([

                                    'id',

                                    'category_id',

                                    'name',

                                    'slug',

                                    'thumbnail',

                                    'status',

                                    'is_featured',

                                    'is_active',

                                    'published_at',
                                ]),
                    ])

                    ->log(
                        'Product updated'
                    );

                $this->clearCaches();

                return $product

                    ->fresh()

                    ->load(
                        'category'
                    );
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Product
    |--------------------------------------------------------------------------
    */

    public function delete(
        Product $product,
        User $actor,
        Request $request
    ): void {

        DB::transaction(

            function () use (
                $product,
                $actor,
                $request
            ) {

                if (
                    $product->hasSku()
                ) {

                    throw ValidationException::withMessages([
                        'product' => [
                            'Produk tidak dapat dihapus karena masih memiliki SKU.',
                        ],
                    ]);
                }

                if (
                    $product->hasReviews()
                ) {

                    throw ValidationException::withMessages([
                        'product' => [
                            'Produk tidak dapat dihapus karena masih memiliki review.',
                        ],
                    ]);
                }

                $oldData =
                    $product->only([

                        'id',

                        'category_id',

                        'name',

                        'slug',

                        'thumbnail',

                        'status',

                        'is_featured',

                        'is_active',

                        'published_at',
                    ]);

                activity()

                    ->causedBy($actor)

                    ->performedOn($product)

                    ->event(
                        'product_deleted'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,
                    ])

                    ->log(
                        'Product deleted'
                    );

                $this->deleteThumbnail(
                    $product
                );

                $product->delete();

                $this->clearCaches();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Thumbnail
    |--------------------------------------------------------------------------
    */

    private function storeThumbnail(
        UploadedFile $thumbnail
    ): string {

        return $thumbnail->store(
            'products',
            'public'
        );
    }

    private function deleteThumbnail(
        Product $product
    ): void {

        if (
            ! $product->hasImage()
        ) {
            return;
        }

        if (
            Storage::disk('public')
                ->exists(
                    $product->thumbnail
                )
        ) {

            Storage::disk('public')
                ->delete(
                    $product->thumbnail
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Business Rules
    |--------------------------------------------------------------------------
    */

    private function validateFeaturedProduct(
        array $data
    ): void {

        if (
            ($data['is_featured'] ?? false)
            &&
            ! ($data['is_active'] ?? false)
        ) {

            throw ValidationException::withMessages([
                'is_featured' => [
                    'Produk unggulan harus dalam kondisi aktif.',
                ],
            ]);
        }
    }

    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {

        $slug = Str::slug(
            $name
        );

        $originalSlug = $slug;

        $counter = 1;

        while (
            Product::query()

                ->when(
                    $ignoreId,
                    fn ($query) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
                )

                ->where(
                    'slug',
                    $slug
                )

                ->exists()
        ) {

            $slug =
                $originalSlug
                . '-'
                . $counter;

            $counter++;
        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */

    private function clearCaches(): void
    {
        Cache::forget(
            'dashboard.overview'
        );

        Cache::forget(
            'product.statistics'
        );
    }
}