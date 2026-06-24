<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreProductImageRequest;
use App\Http\Requests\UpdateProductImageRequest;

use App\Http\Resources\V1\ProductImageResource;

use App\Models\Product;
use App\Models\ProductImage;

use App\Services\ProductImageService;

use App\Traits\ApiResponse;

use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductImageService $productImageService
    ) {
    }

    /**
     * Display listing.
     */
    public function index(
        Product $product,
        Request $request
    ) {

        $images =
            $this->productImageService
                ->paginate(
                    product: $product,

                    perPage: min(
                        (int) $request->input(
                            'per_page',
                            15
                        ),
                        100
                    )
                );

        return $this->successResponse(
            ProductImageResource::collection(
                $images
            ),
            'Data gambar produk berhasil diambil'
        );
    }

        /**
         * Store image.
         */
        public function store(
        Product $product,
        StoreProductImageRequest $request
    ) {

        $data = $request->validated();

        $data['product_id'] = $product->id;

        $image =
            $this->productImageService
                ->create(
                    data: $data,

                    file:
                        $request->file(
                            'image'
                        ),

                    actor:
                        $request->user(),

                    request:
                        $request
                );

        return $this->successResponse(
            new ProductImageResource(
                $image
            ),
            'Gambar produk berhasil ditambahkan',
            201
        );
    }

    /**
     * Show image.
     */
    public function show(
        ProductImage $productImage
    ) {

        return $this->successResponse(
            new ProductImageResource(
                $this->productImageService
                    ->find(
                        $productImage
                    )
            ),
            'Detail gambar produk berhasil diambil'
        );
    }

    /**
     * Update image.
     */
    public function update(
        UpdateProductImageRequest $request,
        ProductImage $productImage
    ) {

        $image =
            $this->productImageService
                ->update(
                    image:
                        $productImage,

                    data:
                        $request->validated(),

                    file:
                        $request->file(
                            'image'
                        ),

                    actor:
                        $request->user(),

                    request:
                        $request
                );

        return $this->successResponse(
            new ProductImageResource(
                $image
            ),
            'Gambar produk berhasil diperbarui'
        );
    }

    /**
     * Delete image.
     */
    public function destroy(
        ProductImage $productImage,
        Request $request
    ) {

        $this->productImageService
            ->delete(
                image:
                    $productImage,

                actor:
                    $request->user(),

                request:
                    $request
            );

        return $this->successResponse(
            null,
            'Gambar produk berhasil dihapus'
        );
    }

    /**
     * Set primary image.
     */
    public function setPrimary(
        ProductImage $productImage,
        Request $request
    ) {

        $image =
            $this->productImageService
                ->setPrimary(
                    image:
                        $productImage,

                    actor:
                        $request->user(),

                    request:
                        $request
                );

        return $this->successResponse(
            new ProductImageResource(
                $image
            ),
            'Gambar utama produk berhasil diperbarui'
        );
    }
}