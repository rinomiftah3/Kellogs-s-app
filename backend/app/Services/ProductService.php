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

class ProductService
{
    /**
     * Product pagination.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return Product::query()

            ->with('category')

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) =>
                    $query->where(
                        'name',
                        'like',
                        '%' .
                        $filters['search'] .
                        '%'
                    )
            )

            ->when(
                filled($filters['category_id'] ?? null),
                fn ($query) =>
                    $query->where(
                        'category_id',
                        $filters['category_id']
                    )
            )

            ->when(
                !is_null(
                    $filters['is_active']
                    ?? null
                ),
                fn ($query) =>
                    $query->where(
                        'is_active',
                        $filters['is_active']
                    )
            )

            ->latest()

            ->paginate($perPage);
    }

    /**
     * Find product.
     */
    public function find(
        Product $product
    ): Product {

        return $product->load(
            'category'
        );
    }

    /**
     * Create product.
     */
    public function create(
        array $data,
        ?UploadedFile $image,
        User $actor,
        Request $request
    ): Product {

        return DB::transaction(
            function () use (
                $data,
                $image,
                $actor,
                $request
            ) {

                $data['slug'] =
                    Str::slug(
                        $data['name']
                    );

                if ($image) {

                    $data['image'] =
                        $this->storeImage(
                            $image
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

                        'new' => [

                            'name' =>
                                $product->name,

                            'price' =>
                                $product->price,

                            'stock' =>
                                $product->stock,

                            'category_id' =>
                                $product->category_id,

                            'is_active' =>
                                $product->is_active,
                        ],

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

    /**
     * Update product.
     */
    public function update(
        Product $product,
        array $data,
        ?UploadedFile $image,
        User $actor,
        Request $request
    ): Product {

        return DB::transaction(
            function () use (
                $product,
                $data,
                $image,
                $actor,
                $request
            ) {

                $oldData = [

                    'name' =>
                        $product->name,

                    'price' =>
                        $product->price,

                    'stock' =>
                        $product->stock,

                    'category_id' =>
                        $product->category_id,

                    'image' =>
                        $product->image,

                    'is_active' =>
                        $product->is_active,
                ];

                $data['slug'] =
                    Str::slug(
                        $data['name']
                    );

                if ($image) {

                    if (
                        $product->image &&
                        Storage::disk('public')
                            ->exists(
                                $product->image
                            )
                    ) {

                        Storage::disk('public')
                            ->delete(
                                $product->image
                            );
                    }

                    $data['image'] =
                        $this->storeImage(
                            $image
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

                        'new' => [

                            'name' =>
                                $product->name,

                            'price' =>
                                $product->price,

                            'stock' =>
                                $product->stock,

                            'category_id' =>
                                $product->category_id,

                            'image' =>
                                $product->image,

                            'is_active' =>
                                $product->is_active,
                        ],

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

    /**
     * Delete product.
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

                $oldData = [

                    'id' =>
                        $product->id,

                    'name' =>
                        $product->name,

                    'price' =>
                        $product->price,

                    'stock' =>
                        $product->stock,

                    'category_id' =>
                        $product->category_id,

                    'image' =>
                        $product->image,
                ];

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

                if (
                    $product->image &&
                    Storage::disk('public')
                        ->exists(
                            $product->image
                        )
                ) {

                    Storage::disk('public')
                        ->delete(
                            $product->image
                        );
                }

                $product->delete();

                $this->clearCaches();
            }
        );
    }

    /**
     * Product statistics.
     */
    public function statistics(): array
    {
        return Cache::remember(
            'product.statistics',
            now()->addMinutes(10),
            fn () => [

                'total_products' =>
                    Product::count(),

                'active_products' =>
                    Product::where(
                        'is_active',
                        true
                    )->count(),

                'inactive_products' =>
                    Product::where(
                        'is_active',
                        false
                    )->count(),

                'in_stock_products' =>
                    Product::where(
                        'stock',
                        '>',
                        0
                    )->count(),

                'out_of_stock_products' =>
                    Product::where(
                        'stock',
                        '<=',
                        0
                    )->count(),

                'inventory_value' =>
                    Product::selectRaw(
                        'SUM(price * stock) as total'
                    )->value('total')
                    ?? 0,
            ]
        );
    }

    /**
     * Store image.
     */
    private function storeImage(
        UploadedFile $image
    ): string {

        return $image->store(
            'products',
            'public'
        );
    }

    /**
     * Clear caches.
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