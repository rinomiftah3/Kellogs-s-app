<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Resources\V1\CheckoutSessionResource;
use App\Http\Requests\SetShippingRequest;

use Illuminate\Http\Request;
use App\Models\CheckoutSession;
use App\Http\Requests\PlaceOrderRequest;

use Illuminate\Validation\ValidationException;
use App\Services\CheckoutService;
use App\Http\Requests\CreateCheckoutRequest;
use Illuminate\Http\JsonResponse;


class CheckoutController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        protected CheckoutService $checkoutService,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | List Checkout Sessions
    |--------------------------------------------------------------------------
    */

    /**
     * Display a listing of checkout sessions.
     */
    public function index(
        Request $request
    ): JsonResponse {

        $customerProfileId =
            $request->user()?->customerProfile?->id;

        $checkouts = $this->checkoutService
            ->paginate(
                filters: [

                    'customer_profile_id'
                        => $customerProfileId,

                    'status'
                        => $request->input('status'),
                ],

                perPage: (int) $request->input(
                    'per_page',
                    15
                )
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Daftar checkout berhasil diambil.',

            'data' =>
                CheckoutSessionResource::collection(
                    $checkouts
                ),

            'meta' => [

                'current_page'
                    => $checkouts->currentPage(),

                'last_page'
                    => $checkouts->lastPage(),

                'per_page'
                    => $checkouts->perPage(),

                'total'
                    => $checkouts->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show Checkout Session
    |--------------------------------------------------------------------------
    */

    /**
     * Display the specified checkout session.
     */
    public function show(
        Request $request,
        CheckoutSession $checkoutSession
    ): JsonResponse {

        $customerProfileId =
            $request->user()?->customerProfile?->id;

        abort_unless(

            $checkoutSession->customer_profile_id
                === $customerProfileId,

            403,

            'Checkout bukan milik Anda.'
        );

        return response()->json([

            'success' => true,

            'message' =>
                'Detail checkout berhasil diambil.',

            'data' =>
                new CheckoutSessionResource(

                    $checkoutSession->load([

                        'customerProfile',

                        'shippingAddress',

                        'items',

                        'items.productSku',
                    ])
                ),
        ]);
    }
/*
|--------------------------------------------------------------------------
| Start Checkout
|--------------------------------------------------------------------------
*/

/**
 * Start checkout from selected cart items.
 */
public function store(
    CreateCheckoutRequest $request
): JsonResponse {

    $customerProfileId =
        $request->user()?->customerProfile?->id;

    /*
    |--------------------------------------------------------------------------
    | Start Checkout Session
    |--------------------------------------------------------------------------
    */

    $checkout = $this->checkoutService
        ->startCheckout(
            $customerProfileId
        );

    /*
    |--------------------------------------------------------------------------
    | Set Shipping Information
    |--------------------------------------------------------------------------
    */

    if (

        $request->filled(
            'courier_code'
        )

        &&

        $request->filled(
            'courier_service'
        )

    ) {

        /*
        |----------------------------------------------------------------------
        | Shipping cost sementara 0
        |
        | Nanti dapat diganti dari integrasi RajaOngkir/Biteship.
        |----------------------------------------------------------------------
        */

        $checkout = $this->checkoutService
            ->setShipping(

                $checkout,

                $request->integer(
                    'shipping_address_id'
                ),

                $request->string(
                    'courier_code'
                )->toString(),

                $request->string(
                    'courier_service'
                )->toString(),

                0
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Apply Voucher
    |--------------------------------------------------------------------------
    */

    if (

        $request->filled(
            'voucher_code'
        )

    ) {

        $checkout = $this->checkoutService
            ->applyVoucher(

                $checkout,

                $request->string(
                    'voucher_code'
                )->toString()
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Notes
    |--------------------------------------------------------------------------
    */

    if (

        $request->filled(
            'notes'
        )

    ) {

        $checkout->update([

            'notes' =>

                $request->string(
                    'notes'
                )->toString(),
        ]);

        $checkout->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' => true,

        'message' =>
            'Checkout berhasil dibuat.',

        'data' =>

            new CheckoutSessionResource(

                $checkout->load([

                    'customerProfile',

                    'shippingAddress',

                    'items',

                    'items.productSku',
                ])
            ),
    ], 201);
}
/*
|--------------------------------------------------------------------------
| Apply Voucher
|--------------------------------------------------------------------------
*/

/**
 * Apply voucher to checkout session.
 */
public function applyVoucher(
    Request $request,
    CheckoutSession $checkoutSession
): JsonResponse {

    /*
    |--------------------------------------------------------------------------
    | Ownership Validation
    |--------------------------------------------------------------------------
    */

    $customerProfileId =
        $request->user()?->customerProfile?->id;

    abort_unless(

        $checkoutSession->customer_profile_id
            === $customerProfileId,

        403,

        'Checkout bukan milik Anda.'
    );

    /*
    |--------------------------------------------------------------------------
    | Request Validation
    |--------------------------------------------------------------------------
    */

    $validated = $request->validate([

        'voucher_code' => [

            'required',

            'string',

            'max:100',
        ],
    ]);

    /*
    |--------------------------------------------------------------------------
    | Apply Voucher
    |--------------------------------------------------------------------------
    */

    $checkout = $this->checkoutService
        ->applyVoucher(

            $checkoutSession,

            strtoupper(
                trim(
                    $validated['voucher_code']
                )
            )
        );

    return response()->json([

        'success' => true,

        'message' =>
            'Voucher berhasil diterapkan.',

        'data' =>

            new CheckoutSessionResource(

                $checkout
            ),
    ]);
}
/*
|--------------------------------------------------------------------------
| Validate Checkout
|--------------------------------------------------------------------------
*/

/**
 * Validate checkout session before placing order.
 */
public function validateCheckout(
    Request $request,
    CheckoutSession $checkoutSession
): JsonResponse {

    /*
    |--------------------------------------------------------------------------
    | Ownership Validation
    |--------------------------------------------------------------------------
    */

    $customerProfileId =
        $request->user()?->customerProfile?->id;

    abort_unless(

        $checkoutSession->customer_profile_id
            === $customerProfileId,

        403,

        'Checkout bukan milik Anda.'
    );

    /*
    |--------------------------------------------------------------------------
    | Validate Checkout
    |--------------------------------------------------------------------------
    */

    $checkout = $this->checkoutService
        ->validateCheckout(
            $checkoutSession
        );

    return response()->json([

        'success' => true,

        'message' =>
            'Checkout berhasil divalidasi.',

        'data' =>

            new CheckoutSessionResource(
                $checkout
            ),
    ]);
}
}