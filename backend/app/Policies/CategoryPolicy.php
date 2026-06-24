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
            $user->hasRole(
                User::ROLE_SUPER_ADMIN
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
            ! $user->can(
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
            ! $user->can(
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
            $category->hasProducts()
        ) {

            return Response::deny(
                'Kategori masih digunakan oleh produk.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Child Category Protection
        |--------------------------------------------------------------------------
        */

        if (
            $category->hasChildren()
        ) {

            return Response::deny(
                'Kategori masih memiliki subkategori.'
            );
        }

        return Response::allow();
    }
}