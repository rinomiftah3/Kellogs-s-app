<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreProductSkuRequest;
use App\Http\Requests\UpdateProductSkuRequest;

use App\Http\Resources\V1\ProductSkuResource;

use App\Models\ProductSku;

use App\Services\ProductSkuService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;

use RuntimeException;
use Throwable;

class ProductSkuController extends Controller
{
    public function __construct(
        protected ProductSkuService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | List SKU
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): JsonResponse {
        $this->authorize(
                    'viewAny',
                    ProductSku::class
                );
        $perPage = (int) $request->integer(
            'per_page',
            15
        );
        
        $filters = [

            'product_id' =>
                $request->input(
                    'product_id'
                ),

            'search' =>
                $request->input(
                    'search'
                ),

            'status' =>
                $request->input(
                    'status'
                ),
        ];

        if (
            $request->has('is_active')
        ) {

            $filters['is_active']
                = filter_var(
                    $request->input(
                        'is_active'
                    ),
                    FILTER_VALIDATE_BOOLEAN
                );
        }

        if (
            $request->has('is_default')
        ) {

            $filters['is_default']
                = filter_var(
                    $request->input(
                        'is_default'
                    ),
                    FILTER_VALIDATE_BOOLEAN
                );
        }

        $skus = $this->service
            ->paginate(
                $filters,
                $perPage
            );

        return response()->json([

            'success' => true,

            'message' =>
                'Daftar SKU berhasil diambil.',

            'data' =>
                ProductSkuResource::collection(
                    $skus
                ),

            'meta' => [

                'current_page' =>
                    $skus->currentPage(),

                'per_page' =>
                    $skus->perPage(),

                'total' =>
                    $skus->total(),

                'last_page' =>
                    $skus->lastPage(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store SKU
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreProductSkuRequest $request
    ): JsonResponse {
        $this->authorize(
            'create',
            ProductSku::class
        );
        try {

            $sku = $this->service
                ->create(
                    $request->validated()
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'SKU berhasil dibuat.',

                'data' =>
                    new ProductSkuResource(
                        $sku
                    ),
            ], 201);

        } catch (
            ValidationException $exception
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $exception->errors(),
            ], 422);

        } catch (
            RuntimeException $exception
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    $exception->getMessage(),
            ], 422);

        } catch (
            Throwable $exception
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal membuat SKU.',

                'error' =>
                    $exception->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show SKU
    |--------------------------------------------------------------------------
    */

    public function show(
        
        ProductSku $productSku
    ): JsonResponse {
        $this->authorize(
            'view',
            $productSku
        );
        $sku = $this->service
            ->findOrFail(
                $productSku
            );
       
        return response()->json([

            'success' => true,

            'message' =>
                'Detail SKU berhasil diambil.',

            'data' =>
                new ProductSkuResource(
                    $sku
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update SKU
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateProductSkuRequest $request,
        ProductSku $productSku
    ): JsonResponse {
        $this->authorize(
    'update',
    $productSku
);
        try {

            $sku = $this->service
                ->update(
                    $productSku,
                    $request->validated()
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'SKU berhasil diperbarui.',

                'data' =>
                    new ProductSkuResource(
                        $sku
                    ),
            ]);

        } catch (
            ValidationException $exception
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Validasi gagal.',

                'errors' =>
                    $exception->errors(),
            ], 422);

        } catch (
            RuntimeException $exception
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    $exception->getMessage(),
            ], 422);

        } catch (
            Throwable $exception
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal memperbarui SKU.',

                'error' =>
                    $exception->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete SKU
    |--------------------------------------------------------------------------
    */

    public function destroy(
        ProductSku $productSku
    ): JsonResponse {
$this->authorize(
    'delete',
    $productSku
);
        try {

            $this->service
                ->delete(
                    $productSku
                );

            return response()->json([

                'success' => true,

                'message' =>
                    'SKU berhasil dihapus.',
            ]);

        } catch (
            RuntimeException $exception
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    $exception->getMessage(),
            ], 422);

        } catch (
            Throwable $exception
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Gagal menghapus SKU.',

                'error' =>
                    $exception->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Activate SKU
    |--------------------------------------------------------------------------
    */

    public function activate(
        ProductSku $productSku
    ): JsonResponse {
$this->authorize(
    'activate',
    $productSku
);
        $sku = $this->service
            ->activate(
                $productSku
            );

        return response()->json([

            'success' => true,

            'message' =>
                'SKU berhasil diaktifkan.',

            'data' =>
                new ProductSkuResource(
                    $sku
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Deactivate SKU
    |--------------------------------------------------------------------------
    */

    public function deactivate(
        ProductSku $productSku
    ): JsonResponse {
$this->authorize(
    'deactivate',
    $productSku
);
        $sku = $this->service
            ->deactivate(
                $productSku
            );

        return response()->json([

            'success' => true,

            'message' =>
                'SKU berhasil dinonaktifkan.',

            'data' =>
                new ProductSkuResource(
                    $sku
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Publish SKU
    |--------------------------------------------------------------------------
    */

    public function publish(
        ProductSku $productSku
    ): JsonResponse {
$this->authorize(
    'publish',
    $productSku
);
        $sku = $this->service
            ->publish(
                $productSku
            );

        return response()->json([

            'success' => true,

            'message' =>
                'SKU berhasil dipublikasikan.',

            'data' =>
                new ProductSkuResource(
                    $sku
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Archive SKU
    |--------------------------------------------------------------------------
    */

    public function archive(
        ProductSku $productSku
    ): JsonResponse {
$this->authorize(
    'archive',
    $productSku
);
        $sku = $this->service
            ->archive(
                $productSku
            );

        return response()->json([

            'success' => true,

            'message' =>
                'SKU berhasil diarsipkan.',

            'data' =>
                new ProductSkuResource(
                    $sku
                ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Set Default SKU
    |--------------------------------------------------------------------------
    */

    public function setDefault(
        ProductSku $productSku
    ): JsonResponse {
$this->authorize(
    'setDefault',
    $productSku
);
        $sku = $this->service
            ->setDefault(
                $productSku
            );

        return response()->json([

            'success' => true,

            'message' =>
                'SKU default berhasil diperbarui.',

            'data' =>
                new ProductSkuResource(
                    $sku
                ),
        ]);
    }
}