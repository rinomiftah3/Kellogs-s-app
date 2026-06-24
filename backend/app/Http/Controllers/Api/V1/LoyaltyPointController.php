<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreLoyaltyPointRequest;
use App\Http\Requests\UpdateLoyaltyPointRequest;
use App\Http\Requests\AddPointRequest;
use App\Http\Requests\RedeemPointRequest;
use App\Http\Resources\V1\LoyaltyPointResource;

use App\Models\LoyaltyPoint;

use App\Services\LoyaltyPointService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use Throwable;

class LoyaltyPointController extends Controller
{
    /**
     * Loyalty Service.
     */
    public function __construct(
        protected LoyaltyPointService $loyaltyService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Loyalty Account CRUD
    |--------------------------------------------------------------------------
    */

    /**
     * Display a listing of loyalty accounts.
     */
    public function index(
        Request $request
    ): JsonResponse {

        try {

            $loyalties = $this->loyaltyService
                ->paginate(

                    filters: [

                        'customer_profile_id'
                            => $request->integer(
                                'customer_profile_id'
                            ),

                        'tier'
                            => $request->input(
                                'tier'
                            ),

                        'is_active'
                            => $request->has(
                                'is_active'
                            )
                                ? filter_var(
                                    $request->input(
                                        'is_active'
                                    ),
                                    FILTER_VALIDATE_BOOLEAN
                                )
                                : null,

                        'published'
                            => $request->has(
                                'published'
                            )
                                ? filter_var(
                                    $request->input(
                                        'published'
                                    ),
                                    FILTER_VALIDATE_BOOLEAN
                                )
                                : null,

                        'expired_tier'
                            => $request->boolean(
                                'expired_tier'
                            ),

                        'highest_points'
                            => $request->boolean(
                                'highest_points'
                            ),
                    ],

                    perPage: $request->integer(
                        'per_page',
                        15
                    )
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Daftar loyalty point berhasil diambil.',

                'data' =>
                    LoyaltyPointResource::collection(
                        $loyalties
                    ),

                'meta' => [

                    'current_page'
                        => $loyalties->currentPage(),

                    'last_page'
                        => $loyalties->lastPage(),

                    'per_page'
                        => $loyalties->perPage(),

                    'total'
                        => $loyalties->total(),
                ],
            ]);
        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal mengambil data loyalty point.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Store a newly created loyalty account.
     */
    public function store(
        StoreLoyaltyPointRequest $request
    ): JsonResponse {

        try {

            $loyalty = $this->loyaltyService
                ->create(
                    $request->validated()
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Akun loyalty berhasil dibuat.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ], 201);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal membuat akun loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Display the specified loyalty account.
     */
    public function show(
        LoyaltyPoint $loyaltyPoint
    ): JsonResponse {

        try {

            $loyalty = $this->loyaltyService
                ->findOrFail(
                    $loyaltyPoint->id
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Detail loyalty point berhasil diambil.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Data loyalty point tidak ditemukan.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 404);
        }
    }

    /**
     * Update the specified loyalty account.
     */
    public function update(
        UpdateLoyaltyPointRequest $request,
        LoyaltyPoint $loyaltyPoint
    ): JsonResponse {

        try {

            $loyalty = $this->loyaltyService
                ->update(
                    $loyaltyPoint,
                    $request->validated()
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Akun loyalty berhasil diperbarui.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal memperbarui akun loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Remove the specified loyalty account.
     */
    public function destroy(
        LoyaltyPoint $loyaltyPoint
    ): JsonResponse {

        try {

            $this->loyaltyService
                ->delete(
                    $loyaltyPoint
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Akun loyalty berhasil dihapus.',
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal menghapus akun loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Loyalty Account Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Activate loyalty account.
     */
    public function activate(
        LoyaltyPoint $loyaltyPoint
    ): JsonResponse {

        try {

            $loyalty = $this->loyaltyService
                ->activate(
                    $loyaltyPoint
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Akun loyalty berhasil diaktifkan.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal mengaktifkan akun loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Deactivate loyalty account.
     */
    public function deactivate(
        LoyaltyPoint $loyaltyPoint
    ): JsonResponse {

        try {

            $loyalty = $this->loyaltyService
                ->deactivate(
                    $loyaltyPoint
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Akun loyalty berhasil dinonaktifkan.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal menonaktifkan akun loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Publish loyalty account.
     */
    public function publish(
        LoyaltyPoint $loyaltyPoint
    ): JsonResponse {

        try {

            $loyalty = $this->loyaltyService
                ->publish(
                    $loyaltyPoint
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Loyalty account berhasil dipublish.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal mempublish loyalty account.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Get authenticated customer's loyalty balance.
     */
    public function myBalance(): JsonResponse
    {
        try {

            $customerProfile =
                auth()->user()?->customerProfile;

            if (! $customerProfile) {

                return response()->json([

                    'success' => false,

                    'message' =>
                        'Profil customer tidak ditemukan.',
                ], 404);
            }

            $loyalty = $this->loyaltyService
                ->getOrCreate(
                    $customerProfile
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Saldo loyalty berhasil diambil.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal mengambil saldo loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Point Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Earn loyalty points.
     */
    public function earn(
        AddPointRequest $request
    ): JsonResponse {

        try {

            $validated = $request->validated();

            $loyalty = $this->loyaltyService
                ->earnPoints(

                    customer:
                        $validated['customer_profile_id'],

                    points:
                        $validated['points'],

                    orderId:
                        $validated['order_id']
                        ?? null,

                    title:
                        $validated['title']
                        ?? null,

                    description:
                        $validated['description']
                        ?? null,

                    metadata:
                        $validated['metadata']
                        ?? null,

                    expiredAt:
                        $validated['expired_at']
                        ?? null,
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Poin loyalty berhasil ditambahkan.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (
            Throwable $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal menambahkan poin loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Redeem loyalty points.
     */
    public function redeem(
        RedeemPointRequest $request
    ): JsonResponse {

        try {

            $validated = $request->validated();

            $customerProfile =
                auth()->user()?->customerProfile;

            if (! $customerProfile) {

                return response()->json([

                    'success' => false,

                    'message' =>
                        'Profil customer tidak ditemukan.',
                ], 404);
            }

            $loyalty = $this->loyaltyService
                ->redeemPoints(

                    customer:
                        $customerProfile,

                    points:
                        $validated['points'],

                    orderId:
                        $validated['order_id']
                        ?? null,

                    title:
                        $validated['title']
                        ?? null,

                    description:
                        $validated['description']
                        ?? null,

                    metadata:
                        $validated['metadata']
                        ?? null,
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Poin loyalty berhasil ditukar.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (
            Throwable $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal menukarkan poin loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Refund redeemed points.
     */
    public function refund(
        AddPointRequest $request
    ): JsonResponse {

        try {

            $validated = $request->validated();

            $loyalty = $this->loyaltyService
                ->refundPoints(

                    customer:
                        $validated['customer_profile_id'],

                    points:
                        $validated['points'],

                    orderId:
                        $validated['order_id']
                        ?? null,

                    title:
                        $validated['title']
                        ?? null,

                    description:
                        $validated['description']
                        ?? null,

                    metadata:
                        $validated['metadata']
                        ?? null,
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Refund poin loyalty berhasil.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (
            Throwable $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal melakukan refund poin.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Grant bonus points.
     */
    public function bonus(
        AddPointRequest $request
    ): JsonResponse {

        try {

            $validated = $request->validated();

            $loyalty = $this->loyaltyService
                ->bonusPoints(

                    customer:
                        $validated['customer_profile_id'],

                    points:
                        $validated['points'],

                    approvedBy:
                        auth()->id(),

                    title:
                        $validated['title']
                        ?? null,

                    description:
                        $validated['description']
                        ?? null,

                    metadata:
                        $validated['metadata']
                        ?? null,
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Bonus poin berhasil diberikan.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (
            Throwable $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal memberikan bonus poin.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Manual point adjustment.
     */
    public function adjust(
        AddPointRequest $request
    ): JsonResponse {

        try {

            $validated = $request->validated();

            $loyalty = $this->loyaltyService
                ->adjustPoints(

                    customer:
                        $validated['customer_profile_id'],

                    points:
                        $validated['points'],

                    approvedBy:
                        auth()->id(),

                    title:
                        $validated['title']
                        ?? null,

                    description:
                        $validated['description']
                        ?? null,

                    metadata:
                        $validated['metadata']
                        ?? null,
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Penyesuaian poin berhasil.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (
            Throwable $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal melakukan penyesuaian poin.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Expire loyalty points.
     */
    public function expire(
        AddPointRequest $request
    ): JsonResponse {

        try {

            $validated = $request->validated();

            $loyalty = $this->loyaltyService
                ->expirePoints(

                    customer:
                        $validated['customer_profile_id'],

                    points:
                        $validated['points'],

                    title:
                        $validated['title']
                        ?? null,

                    description:
                        $validated['description']
                        ?? null,

                    metadata:
                        $validated['metadata']
                        ?? null,
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Poin loyalty berhasil kedaluwarsa.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (
            Throwable $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal memproses kedaluwarsa poin.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Transaction Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Display loyalty point transactions.
     */
    public function transactions(
        Request $request
    ): JsonResponse {

        try {

            $transactions = $this->loyaltyService
                ->paginateTransactions(

                    filters: [

                        'search'
                            => $request->input(
                                'search'
                            ),

                        'customer_profile_id'
                            => $request->integer(
                                'customer_profile_id'
                            ),

                        'type'
                            => $request->input(
                                'type'
                            ),

                        'status'
                            => $request->input(
                                'status'
                            ),

                        'expired'
                            => $request->boolean(
                                'expired'
                            ),
                    ],

                    perPage: $request->integer(
                        'per_page',
                        15
                    )
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Riwayat transaksi loyalty berhasil diambil.',

                'data' =>
                    $transactions->items(),

                'meta' => [

                    'current_page'
                        => $transactions->currentPage(),

                    'last_page'
                        => $transactions->lastPage(),

                    'per_page'
                        => $transactions->perPage(),

                    'total'
                        => $transactions->total(),
                ],
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal mengambil riwayat transaksi loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Approve point transaction.
     */
    public function approveTransaction(
        int $transactionId
    ): JsonResponse {

        try {

            $transaction = $this->loyaltyService
                ->approveTransaction(

                    transaction:
                        $transactionId,

                    approvedBy:
                        auth()->id()
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Transaksi loyalty berhasil disetujui.',

                'data' =>
                    $transaction,
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal menyetujui transaksi loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Cancel point transaction.
     */
    public function cancelTransaction(
        int $transactionId
    ): JsonResponse {

        try {

            $transaction = $this->loyaltyService
                ->cancelTransaction(
                    $transactionId
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Transaksi loyalty berhasil dibatalkan.',

                'data' =>
                    $transaction,
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal membatalkan transaksi loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Tier Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Upgrade loyalty tier manually.
     */
    public function upgradeTier(
        Request $request,
        LoyaltyPoint $loyaltyPoint
    ): JsonResponse {

        try {

            $request->validate([

                'tier' => [
                    'required',
                    'string',
                ],

                'expires_at' => [
                    'nullable',
                    'date',
                ],
            ]);

            $loyalty = $this->loyaltyService
                ->upgradeTier(

                    loyalty:
                        $loyaltyPoint,

                    tier:
                        $request->string(
                            'tier'
                        )->toString(),

                    expiresAt:
                        $request->input(
                            'expires_at'
                        )
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'Tier loyalty berhasil diperbarui.',

                'data' =>
                    new LoyaltyPointResource(
                        $loyalty
                    ),
            ]);

        } catch (
            ValidationException $e
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $e->errors(),
            ], 422);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal memperbarui tier loyalty.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }

    /**
     * Downgrade expired loyalty tiers.
     */
    public function downgradeExpiredTiers(): JsonResponse
    {
        try {

            $affected =
                $this->loyaltyService
                    ->downgradeExpiredTiers();

            return response()->json([

                'success' => true,

                'message' =>
                    'Tier loyalty yang kedaluwarsa berhasil diturunkan.',

                'affected_rows' =>
                    $affected,
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal menurunkan tier loyalty yang kedaluwarsa.',

                'error' =>
                    app()->environment('local')
                        ? $e->getMessage()
                        : null,
            ], 500);
        }
    }
}