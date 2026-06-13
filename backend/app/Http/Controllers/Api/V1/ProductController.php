<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;

use Illuminate\Http\Request;

use App\Services\ProductService;

use App\Traits\ApiResponse;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

use App\Http\Resources\V1\ProductResource;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * Product list.
     */
    public function index(
        Request $request
    ) {

        $products =
            $this->productService->paginate(
                filters: [
                    'search' =>
                        $request->search,

                    'category_id' =>
                        $request->category_id,

                    'is_active' =>
                        $request->has('is_active')
                            ? $request->boolean('is_active')
                            : null,
                ],
                perPage: min(
                    (int) $request->get(
                        'per_page',
                        10
                    ),
                    100
                )
            );

        return $this->successResponse(
            ProductResource::collection(
                $products
            ),
            'Daftar produk berhasil diambil'
        );
    }

    /**
     * Create product.
     */
    public function store(
        StoreProductRequest $request
    ) {

        $product =
            $this->productService->create(
                data: $request->validated(),
                image: $request->file('image'),
                actor: $request->user(),
                request: $request
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
     * Product detail.
     */
    public function show(
        Product $product
    ) {

        $product =
            $this->productService->find(
                $product
            );

        return $this->successResponse(
            new ProductResource(
                $product
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

        $product =
            $this->productService->update(
                product: $product,
                data: $request->validated(),
                image: $request->file('image'),
                actor: $request->user(),
                request: $request
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

        $this->productService->delete(
            product: $product,
            actor: $request->user(),
            request: $request
        );

        return $this->successResponse(
            null,
            'Produk berhasil dihapus'
        );
    }
}