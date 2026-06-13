<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Validation\ValidationException;

class CategoryService
{
    /**
     * Get paginated categories.
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        return Category::query()

            ->withCount('products')

            ->when(
                filled($filters['search'] ?? null),
                fn ($query) =>
                    $query->where(
                        'name',
                        'like',
                        '%' .
                        $filters['search'] .
                        '%'
                    )
            )

            ->latest()

            ->paginate($perPage);
    }

    /**
     * Find category.
     */
    public function find(
        Category $category
    ): Category {

        return $category
            ->loadCount(
                'products'
            );
    }

    /**
     * Create category.
     */
    public function create(
        array $data,
        User $actor,
        Request $request
    ): Category {

        return DB::transaction(
            function () use (
                $data,
                $actor,
                $request
            ) {

                $data['slug'] =
                    Str::slug(
                        $data['name']
                    );

                $category =
                    Category::create(
                        $data
                    );

                activity()

                    ->causedBy(
                        $actor
                    )

                    ->performedOn(
                        $category
                    )

                    ->event(
                        'category_created'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'new' => [

                            'name' =>
                                $category->name,

                            'slug' =>
                                $category->slug,

                            'description' =>
                                $category->description,

                            'is_active' =>
                                $category->is_active,
                        ],

                    ])

                    ->log(
                        'Category created'
                    );

                $this->clearCaches();

                return $category
                    ->loadCount(
                        'products'
                    );
            }
        );
    }

    /**
     * Update category.
     */
    public function update(
        Category $category,
        array $data,
        User $actor,
        Request $request
    ): Category {

        return DB::transaction(
            function () use (
                $category,
                $data,
                $actor,
                $request
            ) {

                $oldData = [

                    'name' =>
                        $category->name,

                    'slug' =>
                        $category->slug,

                    'description' =>
                        $category->description,

                    'is_active' =>
                        $category->is_active,
                ];

                $data['slug'] =
                    Str::slug(
                        $data['name']
                    );

                $category->update(
                    $data
                );

                activity()

                    ->causedBy(
                        $actor
                    )

                    ->performedOn(
                        $category
                    )

                    ->event(
                        'category_updated'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,

                        'new' => [

                            'name' =>
                                $category->name,

                            'slug' =>
                                $category->slug,

                            'description' =>
                                $category->description,

                            'is_active' =>
                                $category->is_active,
                        ],

                    ])

                    ->log(
                        'Category updated'
                    );

                $this->clearCaches();

                return $category
                    ->fresh()
                    ->loadCount(
                        'products'
                    );
            }
        );
    }

    /**
     * Delete category.
     */
    public function delete(
        Category $category,
        User $actor,
        Request $request
    ): void {

        DB::transaction(
            function () use (
                $category,
                $actor,
                $request
            ) {

                if (
                    $category
                        ->products()
                        ->exists()
                ) {

                    throw ValidationException::withMessages([
                        'category' => [
                            'Kategori tidak dapat dihapus karena masih digunakan oleh produk.',
                        ],
                    ]);
                }

                $oldData = [

                    'id' =>
                        $category->id,

                    'name' =>
                        $category->name,

                    'slug' =>
                        $category->slug,

                    'description' =>
                        $category->description,

                    'is_active' =>
                        $category->is_active,
                ];

                activity()

                    ->causedBy(
                        $actor
                    )

                    ->performedOn(
                        $category
                    )

                    ->event(
                        'category_deleted'
                    )

                    ->withProperties([

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),

                        'old' =>
                            $oldData,

                    ])

                    ->log(
                        'Category deleted'
                    );

                $category->delete();

                $this->clearCaches();
            }
        );
    }

    /**
     * Category statistics.
     */
    public function statistics(): array
    {
        return Cache::remember(
            'category.statistics',
            now()->addMinutes(10),
            fn () => [

                'total_categories' =>
                    Category::count(),

                'active_categories' =>
                    Category::where(
                        'is_active',
                        true
                    )->count(),

                'inactive_categories' =>
                    Category::where(
                        'is_active',
                        false
                    )->count(),

            ]
        );
    }

    /**
     * Clear caches.
     */
    private function clearCaches(): void
    {
        Cache::forget(
            'dashboard.overview'
        );

        Cache::forget(
            'category.statistics'
        );
    }
}