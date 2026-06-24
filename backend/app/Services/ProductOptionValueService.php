<?php

namespace App\Services;

use App\Models\ProductOption;
use App\Models\ProductOptionValue;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class ProductOptionValueService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'option',
        'option.product',
        'skuValues',
    ];

    /**
     * Get paginated values.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return ProductOptionValue::query()

            ->with($this->relations)

            ->withCount('skuValues')

            ->when(
                filled($filters['product_option_id'] ?? null),
                fn ($query) => $query->byOption(
                    $filters['product_option_id']
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

            ->ordered()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all values.
     */
    public function all(
        array $filters = []
    ): Collection {

        return ProductOptionValue::query()

            ->with($this->relations)

            ->withCount('skuValues')

            ->when(
                filled($filters['product_option_id'] ?? null),
                fn ($query) => $query->byOption(
                    $filters['product_option_id']
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

            ->ordered()

            ->get();
    }

    /**
     * Find value by ID.
     */
    public function find(
        int $id
    ): ?ProductOptionValue {

        return ProductOptionValue::query()

            ->with($this->relations)

            ->withCount('skuValues')

            ->find($id);
    }

    /**
     * Find value or fail.
     */
    public function findOrFail(
        int $id
    ): ProductOptionValue {

        return ProductOptionValue::query()

            ->with($this->relations)

            ->withCount('skuValues')

            ->findOrFail($id);
    }

    /**
     * Get values by option.
     */
    public function getByOption(
        ProductOption|int $option,
        bool $activeOnly = false
    ): Collection {

        $optionId = $option instanceof ProductOption
            ? $option->id
            : $option;

        return ProductOptionValue::query()

            ->with($this->relations)

            ->withCount('skuValues')

            ->byOption($optionId)

            ->when(
                $activeOnly,
                fn ($query) => $query->active()
            )

            ->ordered()

            ->get();
    }

    /**
     * Create value.
     */
    public function create(
        array $data
    ): ProductOptionValue {

        return DB::transaction(
            function () use ($data) {

                ProductOption::query()
                    ->findOrFail(
                        $data['product_option_id']
                    );

                if (
                    ! isset($data['sort_order'])
                ) {

                    $data['sort_order']
                        = $this->nextSortOrder(
                            $data['product_option_id']
                        );
                }

                if (array_key_exists('code', $data)) {

                    $data['code'] = filled($data['code'])
                        ? Str::upper(
                            trim((string) $data['code'])
                        )
                        : null;
                }

                $value = ProductOptionValue::create([

                    'product_option_id'
                        => $data['product_option_id'],

                    'value'
                        => trim(
                            $data['value']
                        ),

                    'code'
                        => $data['code']
                        ?? null,

                    'sort_order'
                        => $data['sort_order'],

                    'is_active'
                        => $data['is_active']
                        ?? true,
                ]);

                return $value->load(
                    $this->relations
                );
            }
        );
    }

    /**
     * Update value.
     */
    public function update(
        ProductOptionValue|int $value,
        array $data
    ): ProductOptionValue {

        return DB::transaction(
            function () use (
                $value,
                $data
            ) {

                $value = $value instanceof ProductOptionValue
                    ? $value
                    : $this->findOrFail(
                        $value
                    );

                if (
                    isset($data['code'])
                ) {

                    $data['code']
                        = filled($data['code'])
                        ? Str::upper(
                            trim(
                                (string) $data['code']
                            )
                        )
                        : null;
                }

                if (
                    isset($data['value'])
                ) {

                    $data['value']
                        = trim(
                            $data['value']
                        );
                }

                $value->update($data);

                return $value

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }

    /**
 * Delete value.
 */
public function delete(
    ProductOptionValue|int $value
): bool {

    return DB::transaction(
        function () use ($value) {

            $value = $value instanceof ProductOptionValue
                ? $value
                : $this->findOrFail(
                    $value
                );

            /*
            |--------------------------------------------------------------------------
            | Business Rule
            |--------------------------------------------------------------------------
            |
            | Option value yang sudah digunakan
            | oleh ProductSkuValue tidak boleh dihapus.
            |
            */

            if (
                $value->skuValues()
                    ->exists()
            ) {

                throw ValidationException::withMessages([

                    'value' => [
                        'Option value sudah digunakan oleh SKU.',
                    ],
                ]);
            }

            return (bool)
                $value->delete();
        }
    );
}

    /**
     * Activate value.
     */
    public function activate(
        ProductOptionValue|int $value
    ): ProductOptionValue {

        return $this->update(
            $value,
            [
                'is_active' => true,
            ]
        );
    }

    /**
     * Deactivate value.
     */
    public function deactivate(
        ProductOptionValue|int $value
    ): ProductOptionValue {

        return $this->update(
            $value,
            [
                'is_active' => false,
            ]
        );
    }

    /**
     * Get used values.
     */
    public function getUsed(): Collection
    {
        return ProductOptionValue::query()

            ->with($this->relations)

            ->withCount('skuValues')

            ->used()

            ->ordered()

            ->get();
    }

    /**
     * Get unused values.
     */
    public function getUnused(): Collection
    {
        return ProductOptionValue::query()

            ->with($this->relations)

            ->withCount('skuValues')

            ->unused()

            ->ordered()

            ->get();
    }

    /**
     * Get next sort order.
     */
    protected function nextSortOrder(
        int $optionId
    ): int {

        return (
            ProductOptionValue::query()

                ->byOption($optionId)

                ->max('sort_order')

            ?? -1
        ) + 1;
    }
}