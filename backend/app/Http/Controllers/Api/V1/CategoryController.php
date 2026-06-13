<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

use App\Http\Resources\V1\CategoryResource;

use App\Models\Category;

use App\Services\CategoryService;

use App\Traits\ApiResponse;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    /**
     * Display listing.
     */
    public function index(
        Request $request
    ) {

        $categories =
            $this->categoryService
                ->paginate(
                    filters: [

                        'search' =>
                            $request->get(
                                'search'
                            ),

                    ],

                    perPage: min(
                        (int) $request->get(
                            'per_page',
                            15
                        ),
                        100
                    )
                );

        return $this->successResponse(
            CategoryResource::collection(
                $categories
            ),
            'Data kategori berhasil diambil'
        );
    }

    /**
     * Store category.
     */
    public function store(
        StoreCategoryRequest $request
    ) {

        $category =
            $this->categoryService
                ->create(
                    data:
                        $request->validated(),

                    actor:
                        $request->user(),

                    request:
                        $request
                );

        return $this->successResponse(
            new CategoryResource(
                $category
            ),
            'Kategori berhasil dibuat',
            201
        );
    }

    /**
     * Show category.
     */
    public function show(
        Category $category
    ) {

        return $this->successResponse(
            new CategoryResource(
                $this->categoryService
                    ->find(
                        $category
                    )
            ),
            'Detail kategori berhasil diambil'
        );
    }

    /**
     * Update category.
     */
    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ) {

        $category =
            $this->categoryService
                ->update(
                    category:
                        $category,

                    data:
                        $request->validated(),

                    actor:
                        $request->user(),

                    request:
                        $request
                );

        return $this->successResponse(
            new CategoryResource(
                $category
            ),
            'Kategori berhasil diperbarui'
        );
    }

    /**
     * Delete category.
     */
    public function destroy(
        Category $category,
        Request $request
    ) {

        $this->categoryService
            ->delete(
                category:
                    $category,

                actor:
                    $request->user(),

                request:
                    $request
            );

        return $this->successResponse(
            null,
            'Kategori berhasil dihapus'
        );
    }
}