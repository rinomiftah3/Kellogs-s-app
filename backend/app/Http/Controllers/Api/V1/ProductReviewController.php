<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreProductReviewRequest;
use App\Http\Requests\UpdateProductReviewRequest;

use App\Http\Resources\V1\ProductReviewResource;

use App\Models\ProductReview;

use App\Services\ProductReviewService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Throwable;

class ProductReviewController extends Controller
{
    public function __construct(
        protected ProductReviewService $service
    ) {
    }

    /**
     * Display listing.
     */
    public function index(
        Request $request
    ): JsonResponse {
$this->authorize('viewAny', ProductReview::class);
        $filters = [

                'product_id' =>
                    $request->filled('product_id')
                        ? $request->integer('product_id')
                        : null,

                'customer_profile_id' =>
                    $request->filled('customer_profile_id')
                        ? $request->integer('customer_profile_id')
                        : null,

                'rating' =>
                    $request->filled('rating')
                        ? $request->integer('rating')
                        : null,

                'status' =>
                    $request->input('status'),

                'verified_purchase' =>
                    $request->has('verified_purchase')
                        ? $request->boolean('verified_purchase')
                        : null,

                'has_images' =>
                    $request->has('has_images')
                        ? $request->boolean('has_images')
                        : null,
            ];

        $reviews = $this->service->paginate(
            $filters,
            (int) $request->input(
                'per_page',
                15
            )
        );

        return response()->json([

            'success' => true,

            'message' => 'Daftar review berhasil diambil.',

            'data' => ProductReviewResource::collection(
                $reviews
            ),

            'meta' => [

                'current_page'
                    => $reviews->currentPage(),

                'last_page'
                    => $reviews->lastPage(),

                'per_page'
                    => $reviews->perPage(),

                'total'
                    => $reviews->total(),
            ],
        ]);
    }

    /**
     * Store review.
     */
    public function store(
        StoreProductReviewRequest $request
    ): JsonResponse {
$this->authorize('create', ProductReview::class);
        try {

            $review = $this->service->create(
                $request->validated()
            );

            return response()->json([

                'success' => true,

                'message' => 'Review berhasil dibuat.',

                'data' => new ProductReviewResource(
                    $review
                ),
            ], 201);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display review.
     */
    public function show(
        ProductReview $productReview
    ): JsonResponse {
$this->authorize('view', $productReview);
        $review = $this->service->findOrFail(
            $productReview->id
        );

        return response()->json([

            'success' => true,

            'message' => 'Detail review berhasil diambil.',

            'data' => new ProductReviewResource(
                $review
            ),
        ]);
    }

    /**
     * Update review.
     */
    public function update(
        UpdateProductReviewRequest $request,
        ProductReview $productReview
    ): JsonResponse {
$this->authorize('update', $productReview);
        try {

            $review = $this->service->update(
                $productReview,
                $request->validated()
            );

            return response()->json([

                'success' => true,

                'message' => 'Review berhasil diperbarui.',

                'data' => new ProductReviewResource(
                    $review
                ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete review.
     */
    public function destroy(
        ProductReview $productReview
    ): JsonResponse {
$this->authorize('delete', $productReview);
        try {

            $this->service->delete(
                $productReview
            );

            return response()->json([

                'success' => true,

                'message' => 'Review berhasil dihapus.',
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Approve review.
     */
    public function approve(
        ProductReview $productReview
    ): JsonResponse {
$this->authorize('approve', $productReview);
        try {

            $review = $this->service->approve(
                $productReview
            );

            return response()->json([

                'success' => true,

                'message' => 'Review berhasil disetujui.',

                'data' => new ProductReviewResource(
                    $review
                ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reject review.
     */
    public function reject(
        Request $request,
        ProductReview $productReview
    ): JsonResponse {
$this->authorize('reject', $productReview);
        $validated = $request->validate([

            'moderation_notes' => [

                'required',

                'string',
            ],
        ]);

        try {

            $review = $this->service->reject(
                $productReview,
                $validated['moderation_notes']
            );

            return response()->json([

                'success' => true,

                'message' => 'Review berhasil ditolak.',

                'data' => new ProductReviewResource(
                    $review
                ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Increase helpful count.
     */
    public function increaseHelpful(
        ProductReview $productReview
    ): JsonResponse {
$this->authorize('increaseHelpful', $productReview);
        try {

            $review = $this->service->increaseHelpful(
                $productReview
            );

            return response()->json([

                'success' => true,

                'message' => 'Helpful berhasil ditambahkan.',

                'data' => new ProductReviewResource(
                    $review
                ),
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),
            ], 422);
        }
    }
}