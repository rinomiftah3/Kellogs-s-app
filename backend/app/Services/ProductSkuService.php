<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductOptionValue;
use App\Models\ProductSku;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\DB;

class ProductSkuService
{
    /**
     * Default eager load relationships.
     */
    protected array $relations = [
        'product',
        'inventory',
        'optionValues',
        'optionValues.option',
    ];

    /**
     * Get paginated SKUs.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return ProductSku::query()

            ->with($this->relations)

            ->when(
                filled($filters['product_id'] ?? null),
                fn ($query) => $query->where(
                    'product_id',
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
                filled($filters['status'] ?? null),
                fn ($query) => $query->status(
                    $filters['status']
                )
            )

            ->when(
                array_key_exists(
                    'is_active',
                    $filters
                ),
                fn ($query) => $filters['is_active']
                    ? $query->active()
                    : $query->where(
                        'is_active',
                        false
                    )
            )

            ->when(
                array_key_exists(
                    'is_default',
                    $filters
                ),
                fn ($query) => $filters['is_default']
                    ? $query->default()
                    : $query->where(
                        'is_default',
                        false
                    )
            )

            ->latest()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all SKUs.
     */
    public function all(
        array $filters = []
    ): Collection {

        return ProductSku::query()

            ->with($this->relations)

            ->when(
                filled($filters['product_id'] ?? null),
                fn ($query) => $query->where(
                    'product_id',
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
                filled($filters['status'] ?? null),
                fn ($query) => $query->status(
                    $filters['status']
                )
            )

            ->when(
                array_key_exists(
                    'is_active',
                    $filters
                ),
                fn ($query) => $filters['is_active']
                    ? $query->active()
                    : $query->where(
                        'is_active',
                        false
                    )
            )

            ->when(
                array_key_exists(
                    'is_default',
                    $filters
                ),
                fn ($query) => $filters['is_default']
                    ? $query->default()
                    : $query->where(
                        'is_default',
                        false
                    )
            )

            ->latest()

            ->get();
    }

    /**
     * Find SKU by ID.
     */
    public function find(
        int $id
    ): ?ProductSku {

        return ProductSku::query()

            ->with($this->relations)

            ->find($id);
    }

    /**
     * Find SKU by model, ID, or SKU code.
     */
    public function findOrFail(
        ProductSku|int|string $sku
    ): ProductSku {

        if ($sku instanceof ProductSku) {

            return $sku->load(
                $this->relations
            );
        }

        return ProductSku::query()

            ->with($this->relations)

            ->where(function ($query) use ($sku) {

                $query

                    ->where('id', $sku)

                    ->orWhere('sku', $sku);
            })

            ->firstOrFail();
    }

    /**
     * Get SKUs by product.
     */
    public function getByProduct(
        Product|int $product,
        bool $activeOnly = false
    ): Collection {

        $productId = $product instanceof Product
            ? $product->id
            : $product;

        return ProductSku::query()

            ->with($this->relations)

            ->where(
                'product_id',
                $productId
            )

            ->when(
                $activeOnly,
                fn ($query) => $query->active()
            )

            ->latest()

            ->get();
    }
    /**
     * Create SKU.
     */
    public function create(
        array $data
    ): ProductSku {

        return DB::transaction(
            function () use ($data) {

                Product::query()
                    ->findOrFail(
                        $data['product_id']
                    );

                /*
                |--------------------------------------------------------------------------
                | Default SKU
                |--------------------------------------------------------------------------
                */

                if (
                    !empty(
                        $data['is_default']
                    )
                ) {

                    $this->clearDefaultSku(
                        $data['product_id']
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Auto Publish Timestamp
                |--------------------------------------------------------------------------
                */

                if (
                    (
                        $data['status']
                        ?? ProductSku::STATUS_DRAFT
                    ) === ProductSku::STATUS_ACTIVE
                    &&
                    empty(
                        $data['published_at']
                    )
                ) {

                    $data['published_at']
                        = now();
                }

                /*
                |--------------------------------------------------------------------------
                | Create SKU
                |--------------------------------------------------------------------------
                */

                $sku = ProductSku::create([

                    'product_id'
                        => $data['product_id'],

                    'sku'
                        => strtoupper(
                            trim(
                                (string) $data['sku']
                            )
                        ),

                    'barcode'
                        => $data['barcode']
                        ?? null,

                    'price'
                        => $data['price'],

                    'compare_at_price'
                        => $data['compare_at_price']
                        ?? null,

                    'cost_price'
                        => $data['cost_price']
                        ?? null,

                    'weight'
                        => $data['weight']
                        ?? 0,

                    'length'
                        => $data['length']
                        ?? null,

                    'width'
                        => $data['width']
                        ?? null,

                    'height'
                        => $data['height']
                        ?? null,

                    'minimum_order_quantity'
                        => $data['minimum_order_quantity']
                        ?? 1,

                    'maximum_order_quantity'
                        => $data['maximum_order_quantity']
                        ?? null,

                    'is_default'
                        => $data['is_default']
                        ?? false,

                    'status'
                        => $data['status']
                        ?? ProductSku::STATUS_DRAFT,

                    'is_active'
                        => $data['is_active']
                        ?? true,

                    'published_at'
                        => $data['published_at']
                        ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Sync Option Values
                |--------------------------------------------------------------------------
                */

                if (
                    !empty(
                        $data['option_value_ids']
                    )
                ) {

                    $this->syncOptionValues(
                        $sku,
                        $data['option_value_ids']
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Create Inventory
                |--------------------------------------------------------------------------
                */

                $this->syncInventory(
                    $sku,
                    $data
                );

                return $sku

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    /**
     * Update SKU.
     */
    public function update(
        ProductSku|int|string $sku,
        array $data
    ): ProductSku {

        return DB::transaction(
            function () use (
                $sku,
                $data
            ) {

                $sku = $this->findOrFail(
                    $sku
                );

                /*
                |--------------------------------------------------------------------------
                | Validate Product
                |--------------------------------------------------------------------------
                */

                if (
    isset($data['product_id'])
    &&
    $data['product_id']
        !== $sku->product_id
) {

    throw new \RuntimeException(
        'Produk SKU tidak dapat diubah.'
    );
}

                /*
                |--------------------------------------------------------------------------
                | Default SKU
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'is_default',
                        $data
                    )
                    &&
                    $data['is_default']
                ) {

                    $this->clearDefaultSku(
                        $sku->product_id,
                        $sku->id
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Auto Publish Timestamp
                |--------------------------------------------------------------------------
                */

                $newStatus = $data['status']
                    ?? $sku->status;

                if (
                    $newStatus
                        === ProductSku::STATUS_ACTIVE
                    &&
                    !array_key_exists(
                        'published_at',
                        $data
                    )
                    &&
                    empty(
                        $sku->published_at
                    )
                ) {

                    $data['published_at']
                        = now();
                }

                /*
                |--------------------------------------------------------------------------
                | Update Attributes
                |--------------------------------------------------------------------------
                */

                $sku->update([

                    'product_id'
                        => $data['product_id']
                        ?? $sku->product_id,

                    'sku'
                        => isset($data['sku'])
                        ? strtoupper(
                            trim(
                                (string) $data['sku']
                            )
                        )
                        : $sku->sku,

                    'barcode'
                        => array_key_exists(
                            'barcode',
                            $data
                        )
                        ? $data['barcode']
                        : $sku->barcode,

                    'price'
                        => $data['price']
                        ?? $sku->price,

                    'compare_at_price'
                        => array_key_exists(
                            'compare_at_price',
                            $data
                        )
                        ? $data['compare_at_price']
                        : $sku->compare_at_price,

                    'cost_price'
                        => array_key_exists(
                            'cost_price',
                            $data
                        )
                        ? $data['cost_price']
                        : $sku->cost_price,

                    'weight'
                        => $data['weight']
                        ?? $sku->weight,

                    'length'
                        => array_key_exists(
                            'length',
                            $data
                        )
                        ? $data['length']
                        : $sku->length,

                    'width'
                        => array_key_exists(
                            'width',
                            $data
                        )
                        ? $data['width']
                        : $sku->width,

                    'height'
                        => array_key_exists(
                            'height',
                            $data
                        )
                        ? $data['height']
                        : $sku->height,

                    'minimum_order_quantity'
                        => $data['minimum_order_quantity']
                        ?? $sku->minimum_order_quantity,

                    'maximum_order_quantity'
                        => array_key_exists(
                            'maximum_order_quantity',
                            $data
                        )
                        ? $data['maximum_order_quantity']
                        : $sku->maximum_order_quantity,

                    'is_default'
                        => array_key_exists(
                            'is_default',
                            $data
                        )
                        ? $data['is_default']
                        : $sku->is_default,

                    'status'
                        => $newStatus,

                    'is_active'
                        => array_key_exists(
                            'is_active',
                            $data
                        )
                        ? $data['is_active']
                        : $sku->is_active,

                    'published_at'
                        => array_key_exists(
                            'published_at',
                            $data
                        )
                        ? $data['published_at']
                        : $sku->published_at,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Sync Option Values
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'option_value_ids',
                        $data
                    )
                ) {

                    $this->syncOptionValues(
                        $sku,
                        $data['option_value_ids']
                        ?? []
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Ensure Inventory Exists
                |--------------------------------------------------------------------------
                */

                $this->syncInventory(
                    $sku,
                    $data
                );

                return $sku

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    /**
     * Delete SKU.
     */
    public function delete(
        ProductSku|int|string $sku
    ): bool {

        return DB::transaction(
            function () use ($sku) {

                $sku = $this->findOrFail(
                    $sku
                );

                /*
                |--------------------------------------------------------------------------
                | Business Rule
                |--------------------------------------------------------------------------
                |
                | Default SKU tidak boleh dihapus.
                |
                */

                if (
                    $sku->isDefault()
                ) {

                    throw new \RuntimeException(
                        'SKU default tidak dapat dihapus.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Business Rule
                |--------------------------------------------------------------------------
                |
                | SKU yang sudah digunakan
                | pada transaksi tidak boleh dihapus.
                |
                */

                if (
                    $sku->cartItems()->exists()
                    || $sku->checkoutItems()->exists()
                    || $sku->orderItems()->exists()
                ) {

                    throw new \RuntimeException(
                        'SKU sudah digunakan dalam transaksi dan tidak dapat dihapus.'
                    );
                }

                return (bool)
                    $sku->delete();
            }
        );
    }

    /**
     * Activate SKU.
     */
    public function activate(
        ProductSku|int|string $sku
    ): ProductSku {

        return $this->update(
            $sku,
            [
                'is_active' => true,
            ]
        );
    }

    /**
     * Deactivate SKU.
     */
    public function deactivate(
        ProductSku|int|string $sku
    ): ProductSku {

        $sku = $this->findOrFail($sku);

if ($sku->isDefault()) {

    throw new \RuntimeException(
        'SKU default tidak dapat dinonaktifkan.'
    );
}

return $this->update(
    $sku,
    [
        'is_active' => false,
    ]
);
    }

    /**
     * Publish SKU.
     */
    public function publish(
        ProductSku|int|string $sku
    ): ProductSku {

        return $this->update(
            $sku,
            [
                'status'
                    => ProductSku::STATUS_ACTIVE,

                'is_active'
                    => true,

                'published_at'
                    => now(),
            ]
        );
    }

    /**
     * Archive SKU.
     */
    public function archive(
        ProductSku|int|string $sku
    ): ProductSku {
$sku = $this->findOrFail($sku);

if ($sku->isDefault()) {

    throw new \RuntimeException(
        'SKU default tidak dapat diarsipkan.'
    );
}
        return $this->update(
            $sku,
            [
                'status'
                    => ProductSku::STATUS_ARCHIVED,

                'is_active'
                    => false,
            ]
        );
    }

    /**
     * Set default SKU.
     */
    public function setDefault(
        ProductSku|int|string $sku
    ): ProductSku {

        return DB::transaction(
            function () use ($sku) {

                $sku = $this->findOrFail(
                    $sku
                );

                /*
                |--------------------------------------------------------------------------
                | Business Rule
                |--------------------------------------------------------------------------
                |
                | Hanya satu default SKU
                | untuk setiap produk.
                |
                */

                $this->clearDefaultSku(
                    $sku->product_id,
                    $sku->id
                );

                $sku->update([

                    'is_default'
                        => true,

                    'is_active'
                        => true,

                    'status'
                        => ProductSku::STATUS_ACTIVE,

                    'published_at'
                        => $sku->published_at
                        ?? now(),
                ]);

                return $sku

                    ->fresh()

                    ->load(
                        $this->relations
                    );
            }
        );
    }
    /**
     * Sync option values.
     */
    protected function syncOptionValues(
        ProductSku $sku,
        array $optionValueIds
    ): void {

        $optionValueIds = collect(
            $optionValueIds
        )

            ->filter()

            ->map(
                fn ($id) => (int) $id
            )

            ->unique()

            ->values()

            ->all();

        /*
        |--------------------------------------------------------------------------
        | Remove All Values
        |--------------------------------------------------------------------------
        */

        if (empty($optionValueIds)) {

            $sku->optionValues()
                ->sync([]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Business Rules
        |--------------------------------------------------------------------------
        */

        $this->validateOptionValues(
            $sku,
            $optionValueIds
        );

        /*
        |--------------------------------------------------------------------------
        | Sync Pivot
        |--------------------------------------------------------------------------
        */

        $sku->optionValues()
            ->sync(
                $optionValueIds
            );
    }

    /**
     * Validate option values.
     *
     * Business Rules:
     *
     * - Semua value harus berasal dari product yang sama.
     * - Tidak boleh ada lebih dari satu value
     *   dari option yang sama.
     * - Option value harus aktif.
     */
    protected function validateOptionValues(
        ProductSku $sku,
        array $optionValueIds
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Same Product Validation
        |--------------------------------------------------------------------------
        */

        $count = ProductOptionValue::query()

            ->active()

            ->whereIn(
                'id',
                $optionValueIds
            )

            ->whereHas(
                'option',
                fn ($query)

                    => $query->where(
                        'product_id',
                        $sku->product_id
                    )
            )

            ->count();

        if (
            $count !== count(
                $optionValueIds
            )
        ) {

            throw new \RuntimeException(
                'Terdapat option value yang tidak sesuai dengan produk SKU atau sudah tidak aktif.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Option
        |--------------------------------------------------------------------------
        |
        | Contoh yang tidak diperbolehkan:
        |
        | Warna : Merah
        | Warna : Biru
        |
        */

        $optionCount = ProductOptionValue::query()

            ->whereIn(
                'id',
                $optionValueIds
            )

            ->distinct()

            ->count(
                'product_option_id'
            );

        if (
            $optionCount !== count(
                $optionValueIds
            )
        ) {

            throw new \RuntimeException(
                'Satu SKU tidak boleh memiliki lebih dari satu value untuk option yang sama.'
            );
        }
    }

    /**
     * Create inventory if not exists.
     */
    protected function createInventoryIfNotExists(
        ProductSku $sku
    ): Inventory {

        return Inventory::query()

            ->firstOrCreate(

                [
                    'product_sku_id'
                        => $sku->id,
                ],

                [
                    'current_stock'
                        => 0,

                    'reserved_stock'
                        => 0,

                    'available_stock'
                        => 0,

                    'minimum_stock'
                        => 0,

                    'maximum_stock'
                        => null,

                    'reorder_point'
                        => 0,

                    'allow_backorder'
                        => false,

                    'is_active'
                        => true,
                ]
            );
    }

protected function syncInventory(
    ProductSku $sku,
    array $data
): void {

    $inventory = Inventory::query()

        ->firstOrCreate(

            [
                'product_sku_id'
                    => $sku->id,
            ],

            [
                'current_stock' => 0,
                'reserved_stock' => 0,
                'available_stock' => 0,
                'minimum_stock' => 0,
                'maximum_stock' => null,
                'reorder_point' => 0,
                'allow_backorder' => false,
                'is_active' => true,
            ]
        );

    $stock =
        $data['stock']
        ?? $inventory->current_stock;

    $reserved =
        $inventory->reserved_stock;

    $inventory->update([

        'current_stock'
            => $stock,

        'available_stock'
            => max(
                0,
                $stock - $reserved
            ),

        'minimum_stock'
            => $data['minimum_stock']
            ?? $inventory->minimum_stock,

        'maximum_stock'
            => $data['maximum_stock']
            ?? $inventory->maximum_stock,

        'reorder_point'
            => $data['reorder_point']
            ?? $inventory->reorder_point,

        'allow_backorder'
            => $data['allow_backorder']
            ?? $inventory->allow_backorder,
    ]);
}

    /**
     * Clear default SKU.
     *
     * Business Rule:
     * Satu produk hanya boleh
     * memiliki satu default SKU.
     */
    protected function clearDefaultSku(
        int $productId,
        ?int $exceptId = null
    ): void {

        ProductSku::query()

            ->where(
                'product_id',
                $productId
            )

            ->when(
                filled($exceptId),
                fn ($query)

                    => $query->where(
                        'id',
                        '!=',
                        $exceptId
                    )
            )

            ->where(
                'is_default',
                true
            )

            ->update([

                'is_default'
                    => false,
            ]);
    }
}