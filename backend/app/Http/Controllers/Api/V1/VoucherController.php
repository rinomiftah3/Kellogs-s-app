<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\ApplyVoucherRequest;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;

use App\Http\Resources\V1\VoucherResource;

use App\Models\CheckoutSession;
use App\Models\Voucher;

use App\Services\VoucherService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Voucher service instance.
     */
    public function __construct(
        protected VoucherService $voucherService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | List Vouchers (Admin)
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): JsonResponse {

        $filters = [

            'search' => $request->input('search'),

            'type' => $request->input('type'),

            'is_active' => $request->has('is_active')
                ? filter_var(
                    $request->input('is_active'),
                    FILTER_VALIDATE_BOOLEAN
                )
                : null,

            'is_public' => $request->has('is_public')
                ? filter_var(
                    $request->input('is_public'),
                    FILTER_VALIDATE_BOOLEAN
                )
                : null,

            'valid' => $request->has('valid')
                ? filter_var(
                    $request->input('valid'),
                    FILTER_VALIDATE_BOOLEAN
                )
                : null,

            'expired' => $request->has('expired')
                ? filter_var(
                    $request->input('expired'),
                    FILTER_VALIDATE_BOOLEAN
                )
                : null,
        ];

        $filters = array_filter(
            $filters,
            fn ($value) => $value !== null
        );

        $perPage = (int) $request->input(
            'per_page',
            15
        );

        $vouchers = $this->voucherService
            ->paginate(
                filters: $filters,
                perPage: $perPage
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Daftar voucher berhasil diambil.',

            'data' => VoucherResource::collection(
                $vouchers
            ),

            'meta' => [

                'current_page'
                    => $vouchers->currentPage(),

                'last_page'
                    => $vouchers->lastPage(),

                'per_page'
                    => $vouchers->perPage(),

                'total'
                    => $vouchers->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | List Public Vouchers (Customer)
    |--------------------------------------------------------------------------
    */

    public function public(
        Request $request
    ): JsonResponse {

        $filters = [

            'is_public' => true,

            'is_active' => true,

            'valid' => true,

            'search' => $request->input('search'),

            'type' => $request->input('type'),
        ];

        $perPage = (int) $request->input(
            'per_page',
            15
        );

        $vouchers = $this->voucherService
            ->paginate(
                filters: $filters,
                perPage: $perPage
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Daftar voucher publik berhasil diambil.',

            'data' => VoucherResource::collection(
                $vouchers
            ),

            'meta' => [

                'current_page'
                    => $vouchers->currentPage(),

                'last_page'
                    => $vouchers->lastPage(),

                'per_page'
                    => $vouchers->perPage(),

                'total'
                    => $vouchers->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show Voucher Detail
    |--------------------------------------------------------------------------
    */

    public function show(
        Voucher $voucher
    ): JsonResponse {

        $voucher = $this->voucherService
            ->findOrFail(
                $voucher->id
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Detail voucher berhasil diambil.',

            'data' => new VoucherResource(
                $voucher
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store Voucher
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreVoucherRequest $request
    ): JsonResponse {

        $voucher = $this->voucherService
            ->create(
                $request->validated()
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Voucher berhasil dibuat.',

            'data' => new VoucherResource(
                $voucher
            ),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Voucher
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateVoucherRequest $request,
        Voucher $voucher
    ): JsonResponse {

        $voucher = $this->voucherService
            ->update(
                $voucher,
                $request->validated()
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Voucher berhasil diperbarui.',

            'data' => new VoucherResource(
                $voucher
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Voucher
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Voucher $voucher
    ): JsonResponse {

        $this->voucherService
            ->delete(
                $voucher
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Voucher berhasil dihapus.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Activate Voucher
    |--------------------------------------------------------------------------
    */

    public function activate(
        Voucher $voucher
    ): JsonResponse {

        $voucher = $this->voucherService
            ->activate(
                $voucher
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Voucher berhasil diaktifkan.',

            'data' => new VoucherResource(
                $voucher
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Deactivate Voucher
    |--------------------------------------------------------------------------
    */

    public function deactivate(
        Voucher $voucher
    ): JsonResponse {

        $voucher = $this->voucherService
            ->deactivate(
                $voucher
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Voucher berhasil dinonaktifkan.',

            'data' => new VoucherResource(
                $voucher
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Apply Voucher
    |--------------------------------------------------------------------------
    */

    public function apply(
        ApplyVoucherRequest $request
    ): JsonResponse {

        $validated = $request->validated();

        $session = CheckoutSession::query()

            ->where(
                'session_code',
                $validated['session_code']
            )

            ->firstOrFail();

        $customerId =
            auth()->user()?->customerProfile?->id;

        $result = $this->voucherService
            ->applyVoucher(

                voucher:
                    $validated['voucher_code'],

                customer:
                    $customerId,

                subtotal:
                    (float) $session->subtotal
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Voucher berhasil diterapkan.',

            'data' => [

                'voucher' =>
                    new VoucherResource(
                        $result['voucher']
                    ),

                'subtotal' =>
                    (float) $session->subtotal,

                'discount_amount' =>
                    (float) $result['discount_amount'],

                'order_total' =>
                    (float) $result['order_total'],

                'voucher_type' =>
                    $result['voucher_type'],

                'is_stackable' =>
                    (bool) $result['is_stackable'],
            ],
        ]);
    }
}