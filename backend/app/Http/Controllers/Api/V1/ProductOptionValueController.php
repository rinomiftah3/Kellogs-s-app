<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreProductOptionValueRequest;
use App\Http\Requests\UpdateProductOptionValueRequest;

use App\Http\Resources\V1\ProductOptionValueResource;

use App\Models\ProductOptionValue;

use App\Services\ProductOptionValueService;

use App\Traits\ApiResponse;

use Illuminate\Http\Request;

class ProductOptionValueController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductOptionValueService $productOptionValueService
    ) {}

    /**
     * Display listing.
     */
    public function index(
        Request $request
    ) {

        $values =
            $this->productOptionValueService
                ->paginate(
                    filters: [

                        'product_option_id' =>
                            $request->integer(
                                'product_option_id'
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
                    ],

                    perPage: min(
                        $request->integer(
                            'per_page',
                            15
                        ),
                        100
                    )
                );

        return $this->successResponse(
            ProductOptionValueResource::collection(
                $values
            ),
            'Data option value berhasil diambil.'
        );
    }

    /**
     * Store option value.
     */
    public function store(
        StoreProductOptionValueRequest $request
    ) {

        $value =
            $this->productOptionValueService
                ->create(
                    $request->validated()
                );

        return $this->successResponse(
            new ProductOptionValueResource(
                $value
            ),
            'Option value berhasil dibuat.',
            201
        );
    }

    /**
     * Show option value.
     */
    public function show(
        ProductOptionValue $productOptionValue
    ) {

        $value =
            $this->productOptionValueService
                ->findOrFail(
                    $productOptionValue->id
                );

        return $this->successResponse(
            new ProductOptionValueResource(
                $value
            ),
            'Detail option value berhasil diambil.'
        );
    }

    /**
     * Update option value.
     */
    public function update(
        UpdateProductOptionValueRequest $request,
        ProductOptionValue $productOptionValue
    ) {

        $value =
            $this->productOptionValueService
                ->update(
                    $productOptionValue,
                    $request->validated()
                );

        return $this->successResponse(
            new ProductOptionValueResource(
                $value
            ),
            'Option value berhasil diperbarui.'
        );
    }

    /**
     * Delete option value.
     */
    public function destroy(
        ProductOptionValue $productOptionValue
    ) {

        $this->productOptionValueService
            ->delete(
                $productOptionValue
            );

        return $this->successResponse(
            null,
            'Option value berhasil dihapus.'
        );
    }

    /**
     * Activate option value.
     */
    public function activate(
        ProductOptionValue $productOptionValue
    ) {

        $value =
            $this->productOptionValueService
                ->activate(
                    $productOptionValue
                );

        return $this->successResponse(
            new ProductOptionValueResource(
                $value
            ),
            'Option value berhasil diaktifkan.'
        );
    }

    /**
     * Deactivate option value.
     */
    public function deactivate(
        ProductOptionValue $productOptionValue
    ) {

        $value =
            $this->productOptionValueService
                ->deactivate(
                    $productOptionValue
                );

        return $this->successResponse(
            new ProductOptionValueResource(
                $value
            ),
            'Option value berhasil dinonaktifkan.'
        );
    }

    /**
     * Get used values.
     */
    public function used()
    {

        $values =
            $this->productOptionValueService
                ->getUsed();

        return $this->successResponse(
            ProductOptionValueResource::collection(
                $values
            ),
            'Data option value yang digunakan berhasil diambil.'
        );
    }

    /**
     * Get unused values.
     */
    public function unused()
    {

        $values =
            $this->productOptionValueService
                ->getUnused();

        return $this->successResponse(
            ProductOptionValueResource::collection(
                $values
            ),
            'Data option value yang belum digunakan berhasil diambil.'
        );
    }
}