<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Super Admin bypass.
     */
    public function before(
        User $user,
        string $ability
    ): bool|null {

        if (
            method_exists(
                $user,
                'hasRole'
            ) &&
            $user->hasRole(
                'Super Admin'
            )
        ) {

            return true;
        }

        return null;
    }

    /**
     * View category list.
     */
    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'categories.view'
        );
    }

    /**
     * View category detail.
     */
    public function view(
        User $user,
        Category $category
    ): bool {

        return $user->can(
            'categories.view'
        );
    }

    /**
     * Create category.
     */
    public function create(
        User $user
    ): bool {

        return $user->can(
            'categories.create'
        );
    }

    /**
     * Update category.
     */
    public function update(
        User $user,
        Category $category
    ): Response {

        if (
            !$user->can(
                'categories.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin mengubah kategori.'
            );
        }

        return Response::allow();
    }

    /**
     * Delete category.
     */
    public function delete(
        User $user,
        Category $category
    ): Response {

        if (
            !$user->can(
                'categories.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus kategori.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Product Relation Protection
        |--------------------------------------------------------------------------
        */

        if (
            $category
                ->products()
                ->exists()
        ) {

            return Response::deny(
                'Kategori masih digunakan oleh produk.'
            );
        }

        return Response::allow();
    }

    /**
     * Restore category.
     */
    public function restore(
        User $user,
        Category $category
    ): bool {

        return $user->can(
            'categories.restore'
        );
    }

    /**
     * Force delete category.
     */
    public function forceDelete(
        User $user,
        Category $category
    ): Response {

        if (
            !$user->can(
                'categories.force-delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin menghapus permanen kategori.'
            );
        }

        if (
            $category
                ->products()
                ->exists()
        ) {

            return Response::deny(
                'Kategori masih digunakan oleh produk.'
            );
        }

        return Response::allow();
    }

    /**
     * Activate category.
     */
    public function activate(
        User $user,
        Category $category
    ): bool {

        return $user->can(
            'categories.update'
        );
    }

    /**
     * Deactivate category.
     */
    public function deactivate(
        User $user,
        Category $category
    ): bool {

        return $user->can(
            'categories.update'
        );
    }
}