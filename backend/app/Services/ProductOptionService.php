<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductOption;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Validation\ValidationException;

class ProductOptionService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'product',
        'values',
    ];

    /**
     * Get paginated options.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return ProductOption::query()

            ->with($this->relations)

            ->withCount('values')

            ->when(
                filled($filters['product_id'] ?? null),
                fn ($query) => $query->byProduct(
                    $filters['product_id']
                )
            )

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                array_key_exists(
                    'is_active',
                    $filters
                ),
                fn ($query) => $filters['is_active']
                    ? $query->active()
                    : $query->inactive()
            )

            ->when(
                array_key_exists(
                    'is_required',
                    $filters
                ),
                fn ($query) => $filters['is_required']
                    ? $query->required()
                    : $query->optional()
            )

            ->requiredFirst()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all options.
     */
    public function all(
        array $filters = []
    ): Collection {

        return ProductOption::query()

            ->with($this->relations)

            ->withCount('values')

            ->when(
                filled($filters['product_id'] ?? null),
                fn ($query) => $query->byProduct(
                    $filters['product_id']
                )
            )

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) => $query->search(
                    $filters['search']
                )
            )

            ->when(
                array_key_exists(
                    'is_active',
                    $filters
                ),
                fn ($query) => $filters['is_active']
                    ? $query->active()
                    : $query->inactive()
            )

            ->when(
                array_key_exists(
                    'is_required',
                    $filters
                ),
                fn ($query) => $filters['is_required']
                    ? $query->required()
                    : $query->optional()
            )

            ->requiredFirst()

            ->get();
    }

    /**
     * Get option by ID.
     */
    public function find(
        int $id
    ): ?ProductOption {

        return ProductOption::query()

            ->with($this->relations)

            ->withCount('values')

            ->find($id);
    }

    /**
     * Find or fail.
     */
    public function findOrFail(
        int $id
    ): ProductOption {

        return ProductOption::query()

            ->with($this->relations)

            ->withCount('values')

            ->findOrFail($id);
    }

    /**
     * Get options by product.
     */
    public function getByProduct(
        Product|int $product,
        bool $activeOnly = false
    ): Collection {

        $productId = $product instanceof Product
            ? $product->id
            : $product;

        return ProductOption::query()

            ->with('values')

            ->withCount('values')

            ->byProduct($productId)

            ->when(
                $activeOnly,
                fn ($query) => $query->active()
            )

            ->requiredFirst()

            ->get();
    }

    /**
     * Create option.
     */
    public function create(
        array $data
    ): ProductOption {

        return DB::transaction(
            function () use ($data) {

                Product::query()
                    ->findOrFail(
                        $data['product_id']
                    );

                if (
                    ! isset($data['sort_order'])
                ) {

                    $data['sort_order']
                        = $this->nextSortOrder(
                            $data['product_id']
                        );
                }

                $data['code'] = filled(
                    $data['code'] ?? null
                )
                    ? Str::upper(
                        trim($data['code'])
                    )
                    : null;

                $option = ProductOption::create([

                    'product_id'
                        => $data['product_id'],

                    'name'
                        => trim(
                            $data['name']
                        ),

                    'code'
                        => $data['code'],

                    'sort_order'
                        => $data['sort_order'],

                    'is_required'
                        => $data['is_required']
                        ?? false,

                    'is_active'
                        => $data['is_active']
                        ?? true,
                ]);

                $this->clearCaches();

                return $option->load(
                    $this->relations
                );
            }
        );
    }

    /**
     * Update option.
     */
    public function update(
        ProductOption|int $option,
        array $data
    ): ProductOption {

        return DB::transaction(
            function () use (
                $option,
                $data
            ) {

                $option = $option instanceof ProductOption
                    ? $option
                    : $this->findOrFail(
                        $option
                    );

                unset(
                    $data['product_id']
                );

                if (
                    array_key_exists(
                        'code',
                        $data
                    )
                ) {

                    $data['code'] = filled(
                        $data['code']
                    )
                        ? Str::upper(
                            trim(
                                (string) $data['code']
                            )
                        )
                        : null;
                }

                if (
                    isset($data['name'])
                ) {

                    $data['name']
                        = trim(
                            $data['name']
                        );
                }

                $option->update(
                    $data
                );

                $this->clearCaches();

                return $option

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }

    /**
     * Delete option.
     */
    public function delete(
        ProductOption|int $option
    ): bool {

        return DB::transaction(
            function () use ($option) {

                $option = $option instanceof ProductOption
                    ? $option
                    : $this->findOrFail(
                        $option
                    );

                if (
                    $option->values()
                        ->exists()
                ) {

                    throw ValidationException::withMessages([
                        'option' => [
                            'Option masih memiliki value.',
                        ],
                    ]);
                }

                $deleted = (bool)
                    $option->delete();

                $this->clearCaches();

                return $deleted;
            }
        );
    }

    /**
     * Activate option.
     */
    public function activate(
        ProductOption|int $option
    ): ProductOption {

        return $this->update(
            $option,
            [
                'is_active' => true,
            ]
        );
    }

    /**
     * Deactivate option.
     */
    public function deactivate(
        ProductOption|int $option
    ): ProductOption {

        return $this->update(
            $option,
            [
                'is_active' => false,
            ]
        );
    }

    /**
     * Mark required.
     */
    public function markAsRequired(
        ProductOption|int $option
    ): ProductOption {

        return $this->update(
            $option,
            [
                'is_required' => true,
            ]
        );
    }

    /**
     * Mark optional.
     */
    public function markAsOptional(
        ProductOption|int $option
    ): ProductOption {

        return $this->update(
            $option,
            [
                'is_required' => false,
            ]
        );
    }

    /**
     * Get next sort order.
     */
    protected function nextSortOrder(
        int $productId
    ): int {

        return (
            ProductOption::query()

                ->byProduct($productId)

                ->max('sort_order')

            ?? -1
        ) + 1;
    }

    /**
     * Clear caches.
     */
    private function clearCaches(): void
    {
        Cache::forget(
            'product.statistics'
        );
    }
}