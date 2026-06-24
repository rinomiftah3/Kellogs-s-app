<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CustomerProfile;
use App\Models\ProductSku;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Default relationships.
     */
    protected array $relations = [
        'customerProfile',
        'items',
        'items.productSku',
        'items.productSku.inventory',
    ];

    /**
     * Get paginated carts.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return Cart::query()

            ->with($this->relations)

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
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
                    'expired',
                    $filters
                ),
                fn ($query) => $filters['expired']
                    ? $query->expired()
                    : $query->notExpired()
            )

            ->when(
                array_key_exists(
                    'empty',
                    $filters
                ),
                fn ($query) => $filters['empty']
                    ? $query->empty()
                    : $query->notEmpty()
            )

            ->when(
                filled($filters['abandoned_minutes'] ?? null),
                fn ($query) => $query->abandoned(
                    (int) $filters['abandoned_minutes']
                )
            )

            ->latestActivity()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all carts.
     */
    public function all(
        array $filters = []
    ): Collection {

        return Cart::query()

            ->with($this->relations)

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
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
                    'expired',
                    $filters
                ),
                fn ($query) => $filters['expired']
                    ? $query->expired()
                    : $query->notExpired()
            )

            ->when(
                array_key_exists(
                    'empty',
                    $filters
                ),
                fn ($query) => $filters['empty']
                    ? $query->empty()
                    : $query->notEmpty()
            )

            ->when(
                filled($filters['abandoned_minutes'] ?? null),
                fn ($query) => $query->abandoned(
                    (int) $filters['abandoned_minutes']
                )
            )

            ->latestActivity()

            ->get();
    }

    /**
     * Find cart by ID.
     */
    public function find(
        int $id
    ): ?Cart {

        return Cart::query()

            ->with($this->relations)

            ->find($id);
    }

    /**
     * Find cart or fail.
     */
    public function findOrFail(
        int $id
    ): Cart {

        return Cart::query()

            ->with($this->relations)

            ->findOrFail($id);
    }

    /**
     * Get or create customer cart.
     *
     * Final Revision:
     * - Tidak menggunakan cart inactive.
     * - Tidak menggunakan cart expired.
     * - Jika tidak ditemukan cart aktif,
     *   maka buat cart baru.
     */
    public function getOrCreateCart(
        CustomerProfile|int $customer
    ): Cart {

        $customerId = $customer instanceof CustomerProfile
            ? $customer->id
            : $customer;

        CustomerProfile::query()
            ->findOrFail($customerId);

        $cart = Cart::query()

            ->with($this->relations)

            ->where(
                'customer_profile_id',
                $customerId
            )

            ->active()

            ->notExpired()

            ->first();

        if (! $cart) {

            $cart = Cart::create([

                'customer_profile_id'
                    => $customerId,

                'total_items'
                    => 0,

                'subtotal'
                    => 0,

                'is_active'
                    => true,

                'last_activity_at'
                    => now(),

                'expires_at'
                    => now()->addDays(30),
            ]);
        }

        return $cart

            ->fresh()

            ->load($this->relations);
    }

    /**
     * Get cart by customer.
     */
    public function getByCustomer(
        CustomerProfile|int $customer
    ): ?Cart {

        $customerId = $customer instanceof CustomerProfile
            ? $customer->id
            : $customer;

        return Cart::query()

            ->with($this->relations)

            ->byCustomer($customerId)

            ->first();
    }
    /**
     * Add product to cart.
     */
    public function addToCart(
        CustomerProfile|int $customer,
        ProductSku|int $sku,
        int $quantity = 1,
        ?string $notes = null
    ): Cart {

        return DB::transaction(
            function () use (
                $customer,
                $sku,
                $quantity,
                $notes
            ) {

                $cart = $this->getOrCreateCart(
                    $customer
                );

                $sku = $sku instanceof ProductSku
                    ? $sku->loadMissing([
                        'product',
                        'inventory',
                    ])
                    : ProductSku::query()
                        ->with([
                            'product',
                            'inventory',
                        ])
                        ->findOrFail($sku);

                $this->validateSku(
                    $sku,
                    $quantity
                );

                /*
                |--------------------------------------------------------------------------
                | Prevent Race Condition
                |--------------------------------------------------------------------------
                */

                $item = CartItem::query()

                    ->lockForUpdate()

                    ->where(
                        'cart_id',
                        $cart->id
                    )

                    ->where(
                        'product_sku_id',
                        $sku->id
                    )

                    ->first();

                /*
                |--------------------------------------------------------------------------
                | Existing Item
                |--------------------------------------------------------------------------
                */

                if ($item) {

                    $newQuantity =
                        $item->quantity
                        + $quantity;

                    $this->validateSku(
                        $sku,
                        $newQuantity
                    );

                    $item->update([

                        'quantity'
                            => $newQuantity,

                        'subtotal'
                            => (float) $sku->price
                            * $newQuantity,

                        'price'
                            => $sku->price,

                        'notes'
                            => $notes
                            ?? $item->notes,

                        'is_available'
                            => true,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | New Item
                |--------------------------------------------------------------------------
                */

                else {

                    $snapshot = $this->buildSnapshot(
                        $sku
                    );

                    CartItem::create([

                        'cart_id'
                            => $cart->id,

                        'product_sku_id'
                            => $sku->id,

                        'product_name'
                            => $snapshot['product_name'],

                        'sku'
                            => $snapshot['sku'],

                        'thumbnail'
                            => $snapshot['thumbnail'],

                        'price'
                            => $sku->price,

                        'quantity'
                            => $quantity,

                        'subtotal'
                            => (float) $sku->price
                            * $quantity,

                        'is_available'
                            => true,

                        'is_selected'
                            => true,

                        'notes'
                            => $notes,

                        'added_at'
                            => now(),
                    ]);
                }

                return $this->refreshCart(
                    $cart
                );
            }
        );
    }

    /**
     * Update cart item.
     */
    public function updateItem(
        CartItem|int $item,
        array $data
    ): Cart {

        return DB::transaction(
            function () use (
                $item,
                $data
            ) {

                $item = $item instanceof CartItem
                    ? $item->loadMissing([
                        'cart',
                        'productSku.inventory',
                    ])
                    : CartItem::query()
                        ->with([
                            'cart',
                            'productSku.inventory',
                        ])
                        ->findOrFail($item);

                /*
                |--------------------------------------------------------------------------
                | Quantity <= 0
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'quantity',
                        $data
                    )
                ) {

                    $quantity = (int)
                        $data['quantity'];

                    if ($quantity <= 0) {

                        return $this->removeItem(
                            $item
                        );
                    }
                }

                $updates = [];

                /*
                |--------------------------------------------------------------------------
                | Quantity
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'quantity',
                        $data
                    )
                ) {

                    $quantity = (int)
                        $data['quantity'];

                    $this->validateSku(
                        $item->productSku,
                        $quantity
                    );

                    $updates['quantity']
                        = $quantity;

                    $updates['subtotal']
                        = (float) $item->price
                        * $quantity;
                }

                /*
                |--------------------------------------------------------------------------
                | Selection
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'is_selected',
                        $data
                    )
                ) {

                    $updates['is_selected']
                        = (bool)
                        $data['is_selected'];
                }

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                if (
                    array_key_exists(
                        'notes',
                        $data
                    )
                ) {

                    $updates['notes']
                        = $data['notes'];
                }

                if (! empty($updates)) {

                    $item->update(
                        $updates
                    );
                }

                return $this->refreshCart(
                    $item->cart
                );
            }
        );
    }
    /**
     * Remove single cart item.
     */
    public function removeItem(
        CartItem|int $item
    ): Cart {

        return DB::transaction(
            function () use ($item) {

                $item = $item instanceof CartItem
                    ? $item->loadMissing('cart')
                    : CartItem::query()
                        ->with('cart')
                        ->findOrFail($item);

                $cart = $item->cart;

                $item->delete();

                return $this->refreshCart(
                    $cart
                );
            }
        );
    }

    /**
     * Remove multiple cart items.
     */
    public function removeItems(
        Cart|int $cart,
        array $itemIds
    ): Cart {

        return DB::transaction(
            function () use (
                $cart,
                $itemIds
            ) {

                $cart = $cart instanceof Cart
                    ? $cart
                    : $this->findOrFail(
                        $cart
                    );

                CartItem::query()

                    ->where(
                        'cart_id',
                        $cart->id
                    )

                    ->whereIn(
                        'id',
                        $itemIds
                    )

                    ->delete();

                return $this->refreshCart(
                    $cart
                );
            }
        );
    }

    /**
     * Clear cart.
     */
    public function clearCart(
        Cart|int $cart
    ): Cart {

        return DB::transaction(
            function () use ($cart) {

                $cart = $cart instanceof Cart
                    ? $cart
                    : $this->findOrFail(
                        $cart
                    );

                $cart->items()
                    ->delete();

                return $this->refreshCart(
                    $cart
                );
            }
        );
    }

    /**
     * Refresh cart summary.
     */
    public function refreshCart(
        Cart|int $cart
    ): Cart {

        $cart = $cart instanceof Cart
            ? $cart
            : $this->findOrFail(
                $cart
            );

        $cart->update([

            'total_items'
                => (int) $cart->items()
                    ->sum('quantity'),

            'subtotal'
                => (float) $cart->items()
                    ->sum('subtotal'),

            'last_activity_at'
                => now(),
        ]);

        return $cart

            ->fresh()

            ->load($this->relations);
    }
    /**
     * Select cart item.
     */
    public function selectItem(
        CartItem|int $item
    ): Cart {

        return DB::transaction(
            function () use ($item) {

                $item = $item instanceof CartItem
                    ? $item->loadMissing('cart')
                    : CartItem::query()
                        ->with('cart')
                        ->findOrFail($item);

                $item->update([
                    'is_selected' => true,
                ]);

                return $this->refreshCart(
                    $item->cart
                );
            }
        );
    }

    /**
     * Unselect cart item.
     */
    public function unselectItem(
        CartItem|int $item
    ): Cart {

        return DB::transaction(
            function () use ($item) {

                $item = $item instanceof CartItem
                    ? $item->loadMissing('cart')
                    : CartItem::query()
                        ->with('cart')
                        ->findOrFail($item);

                $item->update([
                    'is_selected' => false,
                ]);

                return $this->refreshCart(
                    $item->cart
                );
            }
        );
    }

    /**
     * Select all available cart items.
     */
    public function selectAll(
        Cart|int $cart
    ): Cart {

        return DB::transaction(
            function () use ($cart) {

                $cart = $cart instanceof Cart
                    ? $cart
                    : $this->findOrFail(
                        $cart
                    );

                $cart->items()

                    ->where(
                        'is_available',
                        true
                    )

                    ->update([
                        'is_selected' => true,
                    ]);

                return $this->refreshCart(
                    $cart
                );
            }
        );
    }

    /**
     * Unselect all cart items.
     */
    public function unselectAll(
        Cart|int $cart
    ): Cart {

        return DB::transaction(
            function () use ($cart) {

                $cart = $cart instanceof Cart
                    ? $cart
                    : $this->findOrFail(
                        $cart
                    );

                $cart->items()
                    ->update([
                        'is_selected' => false,
                    ]);

                return $this->refreshCart(
                    $cart
                );
            }
        );
    }

    /**
     * Deactivate expired carts.
     */
    public function deactivateExpiredCarts(): int
    {
        return Cart::query()

            ->active()

            ->expired()

            ->update([
                'is_active' => false,
            ]);
    }

    /**
     * Touch cart activity.
     */
    public function touchActivity(
        Cart|int $cart
    ): Cart {

        $cart = $cart instanceof Cart
            ? $cart
            : $this->findOrFail(
                $cart
            );

        $cart->update([
            'last_activity_at' => now(),
        ]);

        return $cart

            ->fresh()

            ->load($this->relations);
    }
    /**
     * Build cart snapshot.
     */
    protected function buildSnapshot(
        ProductSku $sku
    ): array {

        return [

            'product_name'
                => $sku->productName()
                ?? $sku->product?->name
                ?? 'Unknown Product',

            'sku'
                => $sku->sku,

            'thumbnail'
                => $sku->product?->thumbnail
                ?? null,
        ];
    }

    /**
     * Validate SKU before cart operation.
     */
    protected function validateSku(
        ProductSku $sku,
        int $quantity
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Product Status
        |--------------------------------------------------------------------------
        */

        if (! $sku->isActive()) {

            throw ValidationException::withMessages([

                'product_sku_id' => [
                    'Produk sudah tidak aktif.',
                ],
            ]);
        }

        if (! $sku->isPublished()) {

            throw ValidationException::withMessages([

                'product_sku_id' => [
                    'Produk belum tersedia untuk dibeli.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Stock Validation
        |--------------------------------------------------------------------------
        */

        if (! $sku->isInStock()) {

            throw ValidationException::withMessages([

                'quantity' => [
                    'Stok produk sedang habis.',
                ],
            ]);
        }

        if (
            $quantity >
            $sku->availableStock()
        ) {

            throw ValidationException::withMessages([

                'quantity' => [
                    'Jumlah melebihi stok yang tersedia.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Minimum Quantity
        |--------------------------------------------------------------------------
        */

        $minimumQuantity = max(
            1,
            (int) (
                $sku->minimum_order_quantity
                ?? 1
            )
        );

        if (
            $quantity <
            $minimumQuantity
        ) {

            throw ValidationException::withMessages([

                'quantity' => [
                    "Minimum pembelian adalah {$minimumQuantity} item.",
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Maximum Quantity
        |--------------------------------------------------------------------------
        */

        if (

            filled(
                $sku->maximum_order_quantity
            )

            &&

            $quantity >
            (int) $sku->maximum_order_quantity

        ) {

            throw ValidationException::withMessages([

                'quantity' => [
                    'Jumlah melebihi batas maksimum pembelian.',
                ],
            ]);
        }
    }
}