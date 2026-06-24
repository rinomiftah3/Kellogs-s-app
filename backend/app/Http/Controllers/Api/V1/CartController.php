<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Requests\BulkRemoveCartItemsRequest;

use App\Http\Resources\V1\CartResource;

use App\Models\Cart;
use App\Models\CartItem;

use App\Services\CartService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Standard JSON Response
    |--------------------------------------------------------------------------
    */

    protected function success(
        string $message,
        mixed $data = null,
        int $status = 200
    ): JsonResponse {

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/v1/carts
    | Admin Only
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): JsonResponse {

        $filters = [

            'customer_profile_id'
                => $request->integer(
                    'customer_profile_id'
                ),

            'is_active'
                => $request->has('is_active')
                    ? $request->boolean('is_active')
                    : null,

            'expired'
                => $request->has('expired')
                    ? $request->boolean('expired')
                    : null,

            'empty'
                => $request->has('empty')
                    ? $request->boolean('empty')
                    : null,

            'abandoned_minutes'
                => $request->integer(
                    'abandoned_minutes'
                ),
        ];

        $carts = $this->cartService
            ->paginate(
                filters: $filters,
                perPage: $request->integer(
                    'per_page',
                    15
                )
            );

        return $this->success(
            'Daftar keranjang berhasil diambil.',
            CartResource::collection(
                $carts
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/v1/carts/{cart}
    | Admin Only
    |--------------------------------------------------------------------------
    */

    public function show(
        Cart $cart
    ): JsonResponse {

        return $this->success(
            'Detail keranjang berhasil diambil.',
            new CartResource(
                $this->cartService
                    ->findOrFail(
                        $cart->id
                    )
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/v1/cart
    | Customer
    |--------------------------------------------------------------------------
    */

    public function myCart(): JsonResponse
    {
        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        $cart = $this->cartService
            ->getOrCreateCart(
                $customer
            );

        return $this->success(
            'Keranjang berhasil diambil.',
            new CartResource($cart)
        );
    }
    /*
    |--------------------------------------------------------------------------
    | POST /api/v1/cart/items
    | Customer
    |--------------------------------------------------------------------------
    */

    public function addToCart(
        AddToCartRequest $request
    ): JsonResponse {

        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        $cart = $this->cartService
            ->addToCart(

                customer: $customer,

                sku: $request->integer(
                    'product_sku_id'
                ),

                quantity: $request->integer(
                    'quantity',
                    1
                ),

                notes: $request->input(
                    'notes'
                ),
            );

        return $this->success(
            'Produk berhasil ditambahkan ke keranjang.',
            new CartResource($cart),
            201
        );
    }
    /*
    |--------------------------------------------------------------------------
    | PATCH /api/v1/cart/items/{cart_item}
    | Customer
    |--------------------------------------------------------------------------
    */

    public function updateItem(
        UpdateCartItemRequest $request,
        CartItem $cartItem
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Ownership Validation
        |--------------------------------------------------------------------------
        */

        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        if (
            $cartItem->cart?->customer_profile_id
            !== $customer->id
        ) {

            abort(
                403,
                'Anda tidak memiliki akses ke item keranjang ini.'
            );
        }

        $cart = $this->cartService
            ->updateItem(
                $cartItem,
                $request->validated()
            );

        return $this->success(
            'Item keranjang berhasil diperbarui.',
            new CartResource($cart)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE /api/v1/cart/items/{cart_item}
    | Customer
    |--------------------------------------------------------------------------
    */

    public function removeItem(
        CartItem $cartItem
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Ownership Validation
        |--------------------------------------------------------------------------
        */

        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        if (
            $cartItem->cart?->customer_profile_id
            !== $customer->id
        ) {

            abort(
                403,
                'Anda tidak memiliki akses ke item keranjang ini.'
            );
        }

        $cart = $this->cartService
            ->removeItem(
                $cartItem
            );

        return $this->success(
            'Item berhasil dihapus dari keranjang.',
            new CartResource($cart)
        );
    }
    /*
    |--------------------------------------------------------------------------
    | DELETE /api/v1/cart/items
    | Customer
    |--------------------------------------------------------------------------
    */

    public function bulkRemoveItems(
        BulkRemoveCartItemsRequest $request
    ): JsonResponse {

        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        $cart = $this->cartService
            ->getOrCreateCart(
                $customer
            );

        $cart = $this->cartService
            ->removeItems(
                $cart,
                $request->validated(
                    'item_ids'
                )
            );

        return $this->success(
            'Item keranjang berhasil dihapus.',
            new CartResource($cart)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE /api/v1/cart
    | Customer
    |--------------------------------------------------------------------------
    */

    public function clearCart(): JsonResponse
    {
        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        $cart = $this->cartService
            ->getOrCreateCart(
                $customer
            );

        $cart = $this->cartService
            ->clearCart(
                $cart
            );

        return $this->success(
            'Keranjang berhasil dikosongkan.',
            new CartResource($cart)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH /api/v1/cart/items/{cart_item}/select
    | Customer
    |--------------------------------------------------------------------------
    */

    public function selectItem(
        CartItem $cartItem
    ): JsonResponse {

        $this->authorizeCartItem(
            $cartItem
        );

        $cart = $this->cartService
            ->selectItem(
                $cartItem
            );

        return $this->success(
            'Item berhasil dipilih.',
            new CartResource($cart)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH /api/v1/cart/items/{cart_item}/unselect
    | Customer
    |--------------------------------------------------------------------------
    */

    public function unselectItem(
        CartItem $cartItem
    ): JsonResponse {

        $this->authorizeCartItem(
            $cartItem
        );

        $cart = $this->cartService
            ->unselectItem(
                $cartItem
            );

        return $this->success(
            'Item berhasil dibatalkan dari pilihan.',
            new CartResource($cart)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH /api/v1/cart/select-all
    | Customer
    |--------------------------------------------------------------------------
    */

    public function selectAll(): JsonResponse
    {
        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        $cart = $this->cartService
            ->getOrCreateCart(
                $customer
            );

        $cart = $this->cartService
            ->selectAll(
                $cart
            );

        return $this->success(
            'Semua item berhasil dipilih.',
            new CartResource($cart)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH /api/v1/cart/unselect-all
    | Customer
    |--------------------------------------------------------------------------
    */

    public function unselectAll(): JsonResponse
    {
        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        $cart = $this->cartService
            ->getOrCreateCart(
                $customer
            );

        $cart = $this->cartService
            ->unselectAll(
                $cart
            );

        return $this->success(
            'Semua item berhasil dibatalkan dari pilihan.',
            new CartResource($cart)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH /api/v1/cart/touch
    | Customer
    |--------------------------------------------------------------------------
    */

    public function touchActivity(): JsonResponse
    {
        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        $cart = $this->cartService
            ->getOrCreateCart(
                $customer
            );

        $cart = $this->cartService
            ->touchActivity(
                $cart
            );

        return $this->success(
            'Aktivitas keranjang berhasil diperbarui.',
            new CartResource($cart)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Internal Helper
    |--------------------------------------------------------------------------
    */

    protected function authorizeCartItem(
        CartItem $cartItem
    ): void {

        $customer = Auth::user()
            ->customerProfile;

        if (! $customer) {

            throw ValidationException::withMessages([
                'customer' => [
                    'Profil pelanggan tidak ditemukan.',
                ],
            ]);
        }

        $cartItem->loadMissing('cart');

        if (
            $cartItem->cart?->customer_profile_id
            !== $customer->id
        ) {

            abort(
                403,
                'Anda tidak memiliki akses ke item keranjang ini.'
            );
        }
    }
}