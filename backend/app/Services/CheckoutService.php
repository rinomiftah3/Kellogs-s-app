<?php

namespace App\Services;

use App\Models\CheckoutItem;
use App\Models\CheckoutSession;
use App\Models\CustomerProfile;
use App\Models\Payment;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * Default relationships.
     */
    protected array $relations = [

        'customerProfile',

        'shippingAddress',

        'items',

        'items.productSku',

        /*
        |--------------------------------------------------------------------------
        | Final Revision
        |--------------------------------------------------------------------------
        |
        | Dibutuhkan saat membuat OrderItem
        | agar category_id dan product_id dapat diambil.
        |
        */

        'items.productSku.product',
    ];

    /**
     * Constructor.
     */
    public function __construct(
        protected CartService $cartService,
        protected VoucherService $voucherService,
        protected OrderService $orderService,
        protected PaymentService $paymentService,
        protected PaymentMethodService $paymentMethodService,
        protected LoyaltyPointService $loyaltyPointService,
    ) {
    }

    /**
     * Get paginated checkout sessions.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return CheckoutSession::query()

            ->with($this->relations)

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
                )
            )

            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->where(
                    'status',
                    $filters['status']
                )
            )

            ->latestFirst()

            ->paginate($perPage)

            ->withQueryString();
    }

    /**
     * Get all checkout sessions.
     */
    public function all(
        array $filters = []
    ): Collection {

        return CheckoutSession::query()

            ->with($this->relations)

            ->when(
                filled($filters['customer_profile_id'] ?? null),
                fn ($query) => $query->byCustomer(
                    $filters['customer_profile_id']
                )
            )

            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->where(
                    'status',
                    $filters['status']
                )
            )

            ->latestFirst()

            ->get();
    }

    /**
     * Find checkout session by ID.
     */
    public function find(
        int $id
    ): ?CheckoutSession {

        return CheckoutSession::query()

            ->with($this->relations)

            ->find($id);
    }

    /**
     * Find checkout session or fail.
     */
    public function findOrFail(
        int $id
    ): CheckoutSession {

        return CheckoutSession::query()

            ->with($this->relations)

            ->findOrFail($id);
    }

    /**
     * Start checkout from selected cart items.
     */
    public function startCheckout(
        CustomerProfile|int $customer
    ): CheckoutSession {

        return DB::transaction(
            function () use ($customer) {

                $customerId = $customer instanceof CustomerProfile
                    ? $customer->id
                    : $customer;

                $cart = $this->cartService
                    ->getOrCreateCart($customerId);

                $selectedItems = $cart->items

                    ->where('is_selected', true)

                    ->where('is_available', true);

                if ($selectedItems->isEmpty()) {

                    throw ValidationException::withMessages([

                        'cart' => [
                            'Tidak ada produk yang dipilih untuk checkout.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Create Checkout Session
                |--------------------------------------------------------------------------
                */

                $session = CheckoutSession::create([

                    'customer_profile_id'
                        => $customerId,

                    'session_code'
                        => $this->generateSessionCode(),

                    'status'
                        => CheckoutSession::STATUS_DRAFT,

                    'subtotal'
                        => 0,

                    'shipping_cost'
                        => 0,

                    'voucher_discount'
                        => 0,

                    'promotion_discount'
                        => 0,

                    'total_discount'
                        => 0,

                    'grand_total'
                        => 0,

                    'total_weight'
                        => 0,

                    'is_price_valid'
                        => true,

                    'is_stock_valid'
                        => true,

                    'is_voucher_valid'
                        => true,

                    'expired_at'
                        => now()->addHours(24),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Copy Cart Items → Checkout Items
                |--------------------------------------------------------------------------
                */

                foreach ($selectedItems as $item) {

                    CheckoutItem::create([

                        'checkout_session_id'
                            => $session->id,

                        'product_sku_id'
                            => $item->product_sku_id,

                        'product_name'
                            => $item->product_name,

                        'sku'
                            => $item->sku,

                        'thumbnail'
                            => $item->thumbnail,

                        'price'
                            => $item->price,

                        'quantity'
                            => $item->quantity,

                        'subtotal'
                            => $item->subtotal,

                        'discount_amount'
                            => 0,

                        'final_price'
                            => $item->subtotal,

                        'is_available'
                            => true,

                        'is_valid_price'
                            => true,

                        'is_valid_stock'
                            => true,

                        'added_at'
                            => now(),
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Recalculate Totals
                |--------------------------------------------------------------------------
                */

                $session->recalculateTotals();

                return $session

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }

    /**
     * Set shipping information.
     */
    public function setShipping(
        CheckoutSession|int $session,
        int $shippingAddressId,
        string $courierCode,
        string $courierService,
        float $shippingCost
    ): CheckoutSession {

        return DB::transaction(
            function () use (
                $session,
                $shippingAddressId,
                $courierCode,
                $courierService,
                $shippingCost
            ) {

                $session = $session instanceof CheckoutSession
                    ? $session
                    : $this->findOrFail($session);

                if ($shippingCost < 0) {

                    throw ValidationException::withMessages([

                        'shipping_cost' => [
                            'Biaya pengiriman tidak valid.',
                        ],
                    ]);
                }

                $session->update([

                    'shipping_address_id'
                        => $shippingAddressId,

                    'courier_code'
                        => strtolower(
                            trim($courierCode)
                        ),

                    'courier_service'
                        => trim($courierService),

                    'shipping_cost'
                        => $shippingCost,
                ]);

                return $this->refreshSession(
                    $session
                );
            }
        );
    }
    /**
     * Apply voucher to checkout session.
     */
    public function applyVoucher(
        CheckoutSession|int $session,
        string $voucherCode
    ): CheckoutSession {

        return DB::transaction(
            function () use (
                $session,
                $voucherCode
            ) {

                $session = $session instanceof CheckoutSession
                    ? $session
                    : $this->findOrFail($session);

                $voucher = $this->voucherService
                    ->applyVoucher(

                        $voucherCode,

                        $session->customer_profile_id,

                        (float) $session->subtotal
                    );

                $session->update([

                    'voucher_code'
                        => $voucher['voucher']->code,

                    'voucher_discount'
                        => $voucher['discount_amount'],

                    'is_voucher_valid'
                        => true,
                ]);

                return $this->refreshSession(
                    $session
                );
            }
        );
    }

    /**
     * Refresh checkout session summary.
     */
    protected function refreshSession(
        CheckoutSession|int $session
    ): CheckoutSession {

        $session = $session instanceof CheckoutSession
            ? $session->loadMissing([
                'items.productSku',
            ])
            : $this->findOrFail($session);

        /*
        |--------------------------------------------------------------------------
        | Calculate Subtotal
        |--------------------------------------------------------------------------
        */

        $subtotal = round(

            (float) $session->items()
                ->sum('subtotal'),

            2
        );

        /*
        |--------------------------------------------------------------------------
        | Discounts
        |--------------------------------------------------------------------------
        */

        $promotionDiscount = round(

            (float) $session->promotion_discount,

            2
        );

        $voucherDiscount = round(

            (float) $session->voucher_discount,

            2
        );

        $totalDiscount = round(

            $promotionDiscount
            +
            $voucherDiscount,

            2
        );

        /*
        |--------------------------------------------------------------------------
        | Grand Total
        |--------------------------------------------------------------------------
        */

        $grandTotal = round(

            (
                $subtotal
                +
                (float) $session->shipping_cost
            )
            -
            $totalDiscount,

            2
        );

        /*
        |--------------------------------------------------------------------------
        | Total Weight
        |--------------------------------------------------------------------------
        |
        | Final Revision:
        | Tidak menggunakan Query Builder::sum()
        | dengan closure karena menyebabkan
        | PHP0406 / Intelephense error.
        |
        */

        $totalWeight = (int)

            $session->items

                ->sum(

                    fn (CheckoutItem $item)

                        => (

                            (int) (
                                $item->productSku?->weight
                                ?? 0
                            )

                            *

                            (int) $item->quantity
                        )
                );

        /*
        |--------------------------------------------------------------------------
        | Update Session
        |--------------------------------------------------------------------------
        */

        $session->update([

            'subtotal'
                => $subtotal,

            'total_discount'
                => $totalDiscount,

            'grand_total'
                => max(
                    0,
                    $grandTotal
                ),

            'total_weight'
                => $totalWeight,
        ]);

        return $session

            ->fresh()

            ->load($this->relations);
    }
    /**
     * Validate checkout session.
     */
    public function validateCheckout(
        CheckoutSession|int $session
    ): CheckoutSession {

        return DB::transaction(
            function () use ($session) {

                $session = $session instanceof CheckoutSession
                    ? $session->load([
                        'items.productSku.inventory',
                        'items.productSku.product',
                    ])
                    : CheckoutSession::query()
                        ->with([
                            'items.productSku.inventory',
                            'items.productSku.product',
                        ])
                        ->findOrFail($session);

                /*
                |--------------------------------------------------------------------------
                | Session Status
                |--------------------------------------------------------------------------
                */

                if ($session->isCheckedOut()) {

                    throw ValidationException::withMessages([

                        'checkout' => [
                            'Checkout sudah diproses.',
                        ],
                    ]);
                }

                if ($session->isExpired()) {

                    $session->markExpired();

                    throw ValidationException::withMessages([

                        'checkout' => [
                            'Checkout telah kedaluwarsa.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Shipping Validation
                |--------------------------------------------------------------------------
                */

                if (! $session->hasAddress()) {

                    throw ValidationException::withMessages([

                        'shipping_address' => [
                            'Alamat pengiriman belum dipilih.',
                        ],
                    ]);
                }

                if (! $session->hasCourier()) {

                    throw ValidationException::withMessages([

                        'courier' => [
                            'Kurir pengiriman belum dipilih.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Empty Checkout Validation
                |--------------------------------------------------------------------------
                */

                if ($session->items->isEmpty()) {

                    throw ValidationException::withMessages([

                        'checkout' => [
                            'Tidak ada item untuk diproses.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Reset Validation Flags
                |--------------------------------------------------------------------------
                */

                $session->update([

                    'is_stock_valid'
                        => true,

                    'is_price_valid'
                        => true,

                    'is_voucher_valid'
                        => true,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Validate Checkout Items
                |--------------------------------------------------------------------------
                */

                foreach ($session->items as $item) {

                    $sku = $item->productSku;

                    /*
                    |--------------------------------------------------------------------------
                    | Product Missing
                    |--------------------------------------------------------------------------
                    */

                    if (! $sku) {

                        $item->invalidateStock();

                        $session->invalidateStock();

                        throw ValidationException::withMessages([

                            'product' => [
                                "{$item->product_name} tidak ditemukan.",
                            ],
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Product Status
                    |--------------------------------------------------------------------------
                    */

                    if (
                        ! $sku->isActive()
                        ||
                        ! $sku->isPublished()
                    ) {

                        $item->invalidateStock();

                        $session->invalidateStock();

                        throw ValidationException::withMessages([

                            'product' => [

                                "{$item->product_name} sudah tidak tersedia.",
                            ],
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Stock Validation
                    |--------------------------------------------------------------------------
                    */

                    if (

                        ! $sku->isInStock()

                        ||

                        $item->quantity >
                        $sku->availableStock()

                    ) {

                        $item->invalidateStock();

                        $session->invalidateStock();

                        throw ValidationException::withMessages([

                            'stock' => [

                                "Stok {$item->product_name} tidak mencukupi.",
                            ],
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Price Validation
                    |--------------------------------------------------------------------------
                    */

                    if (

                        round(
                            (float) $item->price,
                            2
                        )

                        !==

                        round(
                            (float) $sku->price,
                            2
                        )

                    ) {

                        $item->invalidatePrice();

                        $session->invalidatePrice();

                        throw ValidationException::withMessages([

                            'price' => [

                                "Harga {$item->product_name} telah berubah.",
                            ],
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Item Valid
                    |--------------------------------------------------------------------------
                    */

                    $item->validateStock();

                    $item->validatePrice();
                }

                /*
                |--------------------------------------------------------------------------
                | Voucher Validation
                |--------------------------------------------------------------------------
                */

                if (filled($session->voucher_code)) {

                    try {

                        $voucher = $this->voucherService
                            ->applyVoucher(

                                $session->voucher_code,

                                $session->customer_profile_id,

                                (float) $session->subtotal
                            );

                        $session->update([

                            'voucher_discount'
                                => $voucher['discount_amount'],

                            'is_voucher_valid'
                                => true,
                        ]);

                    } catch (
                        ValidationException
                    ) {

                        $session->invalidateVoucher();

                        throw ValidationException::withMessages([

                            'voucher' => [
                                'Voucher sudah tidak valid.',
                            ],
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Refresh Totals
                |--------------------------------------------------------------------------
                */

                $session = $this->refreshSession(
                    $session
                );

                /*
                |--------------------------------------------------------------------------
                | Mark Ready
                |--------------------------------------------------------------------------
                */

                $session->markReady();

                return $session

                    ->fresh()

                    ->load($this->relations);
            }
        );
    }
/**
 * Place checkout into order.
 */
public function placeOrder(
    CheckoutSession|int $session,
    string $gateway,
    string $method
): array {

    return DB::transaction(
        function () use (
            $session,
            $gateway,
            $method
        ) {

            $session = $session instanceof CheckoutSession
                ? $session->load($this->relations)
                : CheckoutSession::query()
                    ->with($this->relations)
                    ->findOrFail($session);

            /*
            |--------------------------------------------------------------------------
            | Revalidate Checkout
            |--------------------------------------------------------------------------
            */

            $session = $this->validateCheckout(
                $session
            );

            /*
            |--------------------------------------------------------------------------
            | Reserve Voucher
            |--------------------------------------------------------------------------
            */

            $voucherUsage = null;

            if (
                filled($session->voucher_code)
            ) {

                $voucherUsage =

                    $this->voucherService
                        ->reserveVoucher(

                            $session->voucher_code,

                            $session->customer_profile_id,

                            (float) $session->subtotal
                        );
            }

            /*
            |--------------------------------------------------------------------------
            | Create Order
            |--------------------------------------------------------------------------
            */

            $order = $this->orderService
                ->create([

                    'customer_profile_id'
                        => $session->customer_profile_id,

                    'shipping_address_id'
                        => $session->shipping_address_id,

                    'subtotal'
                        => $session->subtotal,

                    'shipping_cost'
                        => $session->shipping_cost,

                    'discount_amount'
                        => $session->total_discount,

                    'grand_total'
                        => $session->grand_total,

                    'voucher_code'
                        => $session->voucher_code,

                    'voucher_discount'
                        => $session->voucher_discount,

                    'courier_code'
                        => $session->courier_code,

                    'courier_service'
                        => $session->courier_service,

                    'total_weight'
                        => $session->total_weight,

                    'customer_notes'
                        => $session->notes,
                ]);

            /*
            |--------------------------------------------------------------------------
            | Create Order Items
            |--------------------------------------------------------------------------
            */

            $items = [];

            foreach (
                $session->items as $item
            ) {

                $sku = $item->productSku;

                $items[] = [

                    'product_sku_id'
                        => $item->product_sku_id,

                    'product_id'
                        => $sku?->product_id,

                    'category_id'
                        => $sku?->product?->category_id,

                    'product_name'
                        => $item->product_name,

                    'sku'
                        => $item->sku,

                    'thumbnail'
                        => $item->thumbnail,

                    'weight'
                        => $sku?->weight ?? 0,

                    'unit_price'
                        => $item->price,

                    'discount_amount'
                        => $item->discount_amount,

                    'final_price'
                        => $item->final_price,

                    'quantity'
                        => $item->quantity,

                    'subtotal'
                        => $item->subtotal,
                ];
            }

            $order = $this->orderService
                ->createItems(
                    $order,
                    $items
                );

            /*
            |--------------------------------------------------------------------------
            | Create Payment
            |--------------------------------------------------------------------------
            */

            $payment = $this->paymentService
                ->create([

                    'order_id'
                        => $order->id,

                    'gateway'
                        => $gateway,

                    'method'
                        => $method,

                    'amount'
                        => $order->grand_total,

                    'status'
                        => Payment::STATUS_PENDING,

                    'expired_at'
                        => now()->addDay(),
                ]);
            /*
            |--------------------------------------------------------------------------
            | Generate Payment Gateway
            |--------------------------------------------------------------------------
            */

            $snapToken = null;

            $redirectUrl = null;

            if (
                strtolower($gateway) === 'midtrans'
            ) {

                $snapToken =

                    $this->paymentMethodService
                        ->createSnapToken(
                            $order,
                            $payment
                        );

                $redirectUrl =

                    $this->paymentMethodService
                        ->createSnapRedirectUrl(
                            $order,
                            $payment
                        );

                $payment =

                    $this->paymentService
                        ->update(

                            $payment,

                            [

                                'payment_url'
                                    => $redirectUrl,

                                'metadata'
                                    => array_merge(

                                        $payment->metadata ?? [],

                                        [
                                            'snap_token'
                                                => $snapToken,
                                        ]
                                    ),
                            ]
                        );
            }

            /*
            |--------------------------------------------------------------------------
            | Attach Voucher Usage
            |--------------------------------------------------------------------------
            */

            if ($voucherUsage) {

                $voucherUsage->update([

                    'order_id'
                        => $order->id,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Loyalty Points
            |--------------------------------------------------------------------------
            */

            $earnedPoints = (int) floor(
                $order->grand_total / 10000
            );

            if ($earnedPoints > 0) {

                $this->loyaltyPointService
                    ->earnPoints(

                        customer: $session->customer_profile_id,

                        points: $earnedPoints,

                        orderId: $order->id,

                        title: 'Checkout Reward',

                        description:
                            'Poin dari transaksi checkout.',

                        metadata: [

                            'order_number'
                                => $order->order_number,
                        ]
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Checkout Completed
            |--------------------------------------------------------------------------
            */

            $session->markCheckedOut();

            /*
            |--------------------------------------------------------------------------
            | Remove Selected Cart Items
            |--------------------------------------------------------------------------
            */

            $cart =

                $this->cartService
                    ->getByCustomer(
                        $session->customer_profile_id
                    );

            if ($cart) {

                $selectedIds =

                    $cart->items()

                        ->where(
                            'is_selected',
                            true
                        )

                        ->pluck('id')

                        ->toArray();

                if (
                    ! empty($selectedIds)
                ) {

                    $this->cartService
                        ->removeItems(
                            $cart,
                            $selectedIds
                        );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Final Response
            |--------------------------------------------------------------------------
            */

            return [

                'checkout_session'
                    => $session
                        ->fresh()
                        ->load(
                            $this->relations
                        ),

                'order'
                    => $order
                        ->fresh()
                        ->load([
                            'items',
                            'payment',
                        ]),

                'payment'
                    => $payment
                        ->fresh(),

                'voucher_usage'
                    => $voucherUsage?->fresh(),

                'snap_token'
                    => $snapToken,

                'redirect_url'
                    => $redirectUrl,
            ];
        }
    );
}
    /**
     * Generate checkout session code.
     */
    protected function generateSessionCode(): string
    {
        do {

            $code =
                'CHK-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        uniqid(),
                        -6
                    )
                );

        } while (

            CheckoutSession::query()

                ->where(
                    'session_code',
                    $code
                )

                ->exists()
        );

        return $code;
    }
}