<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\FilterProductRequest;

use App\Http\Resources\V1\ProductResource;

use App\Models\Product;

use App\Services\ProductService;

use App\Traits\ApiResponse;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Display listing.
     */
    public function index(
        FilterProductRequest $request
    ) {

        $products = $this->productService
        ->paginate(
            filters: $request->filters(),
            perPage: $request->perPage()
        );

        return $this->successResponse(
            ProductResource::collection(
                $products
            ),
            'Data produk berhasil diambil'
        );
    }

    /**
     * Store product.
     */
    public function store(
        StoreProductRequest $request
    ) {

        $product = $this->productService
            ->create(
                data:
                    $request->validated(),

                thumbnail:
                    $request->file(
                        'thumbnail'
                    ),

                actor:
                    $request->user(),

                request:
                    $request
            );

        return $this->successResponse(
            new ProductResource(
                $product
            ),
            'Produk berhasil dibuat',
            201
        );
    }

    /**
     * Show product.
     */
    public function show(
        Product $product
    ) {

        return $this->successResponse(
            new ProductResource(
                $this->productService
                    ->find(
                        $product
                    )
            ),
            'Detail produk berhasil diambil'
        );
    }

    /**
     * Update product.
     */
    public function update(
        UpdateProductRequest $request,
        Product $product
    ) {

        $product = $this->productService
            ->update(
                product:
                    $product,

                data:
                    $request->validated(),

                thumbnail:
                    $request->file(
                        'thumbnail'
                    ),

                actor:
                    $request->user(),

                request:
                    $request
            );

        return $this->successResponse(
            new ProductResource(
                $product
            ),
            'Produk berhasil diperbarui'
        );
    }

    /**
     * Delete product.
     */
    public function destroy(
        Product $product,
        Request $request
    ) {

        $this->productService
            ->delete(
                product:
                    $product,

                actor:
                    $request->user(),

                request:
                    $request
            );

        return $this->successResponse(
            null,
            'Produk berhasil dihapus'
        );
    }
}