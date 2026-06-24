<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $query = Category::query()
            ->with([
                'parent:id,name',
            ])
            ->withCount([
                'products',
                'children',
            ])
            ->search(
                $filters['search'] ?? null
            )
            ->when(
                isset($filters['parent_id']),
                fn ($query) => $query->where(
                    'parent_id',
                    $filters['parent_id']
                )
            )
            ->when(
                isset($filters['is_active']),
                fn ($query) => $query->where(
                    'is_active',
                    $filters['is_active']
                )
            )
            ->when(
                isset($filters['has_products']),
                fn ($query) => $filters['has_products']
                    ? $query->hasProducts()
                    : $query->empty()
            );

        /*
        |--------------------------------------------------------------------------
        | Parent / Child Filter
        |--------------------------------------------------------------------------
        */

        if (isset($filters['is_parent'])) {
            $query = $filters['is_parent']
                ? $query->parentCategories()
                : $query->childCategories();
        } elseif (isset($filters['is_child'])) {
            $query = $filters['is_child']
                ? $query->childCategories()
                : $query->parentCategories();
        }

        return $query
            ->orderBy(
                $filters['sort_by']
                    ?? 'sort_order',
                $filters['sort_direction']
                    ?? 'asc'
            )
            ->paginate($perPage);
    }

    /**
     * Find category.
     */
    public function find(
        Category $category
    ): Category {
        return $category
            ->load([
                'parent:id,name',
                'children:id,parent_id,name,is_active',
            ])
            ->loadCount([
                'products',
                'children',
            ]);
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
                    $this->generateUniqueSlug(
                        $data['slug']
                            ?? $data['name']
                    );
                if (isset($data['image'])) {
                    $data['image'] = $data['image']->store(
                        'categories',
                        'public'
                    );
                }
                $category = Category::create(
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
                            'parent_id' =>
                                $category->parent_id,

                            'name' =>
                                $category->name,

                            'slug' =>
                                $category->slug,

                            'description' =>
                                $category->description,

                            'image' =>
                                $category->image,

                            'sort_order' =>
                                $category->sort_order,

                            'is_active' =>
                                $category->is_active,
                        ],
                    ])
                    ->log(
                        'Category created'
                    );

                $this->clearCaches();

                return $category
                    ->load([
                        'parent:id,name',
                    ])
                    ->loadCount([
                        'products',
                        'children',
                    ]);
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
                    'parent_id' =>
                        $category->parent_id,

                    'name' =>
                        $category->name,

                    'slug' =>
                        $category->slug,

                    'description' =>
                        $category->description,

                    'image' =>
                        $category->image,

                    'sort_order' =>
                        $category->sort_order,

                    'is_active' =>
                        $category->is_active,
                ];

                $data['slug'] =
                    $this->generateUniqueSlug(
                        $data['slug']
                            ?? $data['name'],
                        $category->id
                        
                    );

                if (isset($data['image'])) {

    if (
        filled($category->image)
        &&
        ! str_starts_with($category->image, 'http')
        &&
        Storage::disk('public')->exists($category->image)
    ) {
        Storage::disk('public')->delete(
            $category->image
        );
    }

    $data['image'] = $data['image']->store(
        'categories',
        'public'
    );
}
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
                            'parent_id' =>
                                $category->parent_id,

                            'name' =>
                                $category->name,

                            'slug' =>
                                $category->slug,

                            'description' =>
                                $category->description,

                            'image' =>
                                $category->image,

                            'sort_order' =>
                                $category->sort_order,

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
                    ->load([
                        'parent:id,name',
                    ])
                    ->loadCount([
                        'products',
                        'children',
                    ]);
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

                if (
                    $category
                        ->children()
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'category' => [
                            'Kategori tidak dapat dihapus karena masih memiliki subkategori.',
                        ],
                    ]);
                }

                $oldData = [
                    'id' =>
                        $category->id,

                    'parent_id' =>
                        $category->parent_id,

                    'name' =>
                        $category->name,

                    'slug' =>
                        $category->slug,

                    'description' =>
                        $category->description,

                    'image' =>
                        $category->image,

                    'sort_order' =>
                        $category->sort_order,

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

                if (
                    filled(
                        $category->image
                    )
                    &&
                    ! str_starts_with(
                        $category->image,
                        'http'
                    )
                    &&
                    Storage::disk('public')
                        ->exists(
                            $category->image
                        )
                ) {
                    Storage::disk('public')
                        ->delete(
                            $category->image
                        );
                }

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
                    Category::active()->count(),

                'inactive_categories' =>
                    Category::inactive()->count(),

                'parent_categories' =>
                    Category::parentCategories()->count(),

                'child_categories' =>
                    Category::childCategories()->count(),
            ]
        );
    }

    /**
     * Generate unique slug.
     */
    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug(
            $name
        );

        $slug = $baseSlug;

        $counter = 2;

        while (
            Category::query()
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where(
                        'id',
                        '!=',
                        $ignoreId
                    )
                )
                ->where(
                    'slug',
                    $slug
                )
                ->exists()
        ) {
            $slug =
                $baseSlug .
                '-' .
                $counter;

            $counter++;
        }

        return $slug;
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