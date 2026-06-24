<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreProductOptionRequest;
use App\Http\Requests\UpdateProductOptionRequest;

use App\Http\Resources\V1\ProductOptionResource;

use App\Models\ProductOption;

use App\Services\ProductOptionService;

use App\Traits\ApiResponse;

use Illuminate\Http\Request;

class ProductOptionController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductOptionService $productOptionService
    ) {
    }

    /**
     * Display listing.
     */
    public function index(
        Request $request
    ) {

        $options =
            $this->productOptionService
                ->paginate(
                    filters: [

                        'product_id' =>
                            $request->input(
                                'product_id'
                            ),

                        'search' =>
                            $request->input(
                                'search'
                            ),

                        'is_active' =>
                            $request->has(
                                'is_active'
                            )
                                ? filter_var(
                                    $request->input(
                                        'is_active'
                                    ),
                                    FILTER_VALIDATE_BOOLEAN,
                                    FILTER_NULL_ON_FAILURE
                                )
                                : null,

                        'is_required' =>
                            $request->has(
                                'is_required'
                            )
                                ? filter_var(
                                    $request->input(
                                        'is_required'
                                    ),
                                    FILTER_VALIDATE_BOOLEAN,
                                    FILTER_NULL_ON_FAILURE
                                )
                                : null,
                    ],

                    perPage: min(
                        (int) $request->input(
                            'per_page',
                            15
                        ),
                        100
                    )
                );

        return $this->successResponse(
            ProductOptionResource::collection(
                $options
            ),
            'Data opsi produk berhasil diambil'
        );
    }

    /**
     * Store option.
     */
    public function store(
        StoreProductOptionRequest $request
    ) {

        $option =
            $this->productOptionService
                ->create(
                    $request->validated()
                );

        return $this->successResponse(
            new ProductOptionResource(
                $option
            ),
            'Opsi produk berhasil dibuat',
            201
        );
    }

    /**
     * Show option.
     */
    public function show(
        ProductOption $productOption
    ) {

        $option =
            $this->productOptionService
                ->findOrFail(
                    $productOption->id
                );

        return $this->successResponse(
            new ProductOptionResource(
                $option
            ),
            'Detail opsi produk berhasil diambil'
        );
    }

    /**
     * Update option.
     */
    public function update(
        UpdateProductOptionRequest $request,
        ProductOption $productOption
    ) {

        $option =
            $this->productOptionService
                ->update(
                    option:
                        $productOption,

                    data:
                        $request->validated()
                );

        return $this->successResponse(
            new ProductOptionResource(
                $option
            ),
            'Opsi produk berhasil diperbarui'
        );
    }

    /**
     * Delete option.
     */
    public function destroy(
        ProductOption $productOption
    ) {

        $this->productOptionService
            ->delete(
                $productOption
            );

        return $this->successResponse(
            null,
            'Opsi produk berhasil dihapus'
        );
    }

    /**
     * Activate option.
     */
    public function activate(
        ProductOption $productOption
    ) {

        $option =
            $this->productOptionService
                ->activate(
                    $productOption
                );

        return $this->successResponse(
            new ProductOptionResource(
                $option
            ),
            'Opsi produk berhasil diaktifkan'
        );
    }

    /**
     * Deactivate option.
     */
    public function deactivate(
        ProductOption $productOption
    ) {

        $option =
            $this->productOptionService
                ->deactivate(
                    $productOption
                );

        return $this->successResponse(
            new ProductOptionResource(
                $option
            ),
            'Opsi produk berhasil dinonaktifkan'
        );
    }

    /**
     * Mark option as required.
     */
    public function markAsRequired(
        ProductOption $productOption
    ) {

        $option =
            $this->productOptionService
                ->markAsRequired(
                    $productOption
                );

        return $this->successResponse(
            new ProductOptionResource(
                $option
            ),
            'Opsi produk berhasil ditandai sebagai wajib'
        );
    }

    /**
     * Mark option as optional.
     */
    public function markAsOptional(
        ProductOption $productOption
    ) {

        $option =
            $this->productOptionService
                ->markAsOptional(
                    $productOption
                );

        return $this->successResponse(
            new ProductOptionResource(
                $option
            ),
            'Opsi produk berhasil ditandai sebagai opsional'
        );
    }
}