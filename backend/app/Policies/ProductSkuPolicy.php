<?php

namespace App\Policies;

use App\Models\ProductSku;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductSkuPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Before
    |--------------------------------------------------------------------------
    */

    public function before(
        User $user,
        string $ability
    ): bool|null {

        if (
            $user->hasRole(
                'Super Admin'
            )
        ) {
            return true;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | View Any
    |--------------------------------------------------------------------------
    */

    public function viewAny(
        User $user
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    public function view(
        User $user,
        ProductSku $productSku
    ): bool {

        return $user->can(
            'products.view'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        User $user
    ): bool {

        return $user->can(
            'products.create'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        User $user,
        ProductSku $productSku
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        User $user,
        ProductSku $productSku
    ): Response {

        if (
            ! $user->can(
                'products.delete'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin untuk menghapus SKU produk.'
            );
        }

        if (
            $productSku->isDefault()
        ) {

            return Response::deny(
                'SKU default tidak dapat dihapus.'
            );
        }

        if (
            $productSku->cartItems()->exists()
            ||
            $productSku->checkoutItems()->exists()
            ||
            $productSku->orderItems()->exists()
        ) {

            return Response::deny(
                'SKU sudah digunakan dalam transaksi dan tidak dapat dihapus.'
            );
        }

        return Response::allow();
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(
        User $user,
        ProductSku $productSku
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(
        User $user,
        ProductSku $productSku
    ): bool {

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Activate
    |--------------------------------------------------------------------------
    */

    public function activate(
        User $user,
        ProductSku $productSku
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Deactivate
    |--------------------------------------------------------------------------
    */

    public function deactivate(
        User $user,
        ProductSku $productSku
    ): bool {

        return $user->can(
            'products.update'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Publish
    |--------------------------------------------------------------------------
    */

    public function publish(
        User $user,
        ProductSku $productSku
    ): Response {

        if (
            ! $user->can(
                'products.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin untuk mempublikasikan SKU produk.'
            );
        }

        if (
            $productSku->isArchived()
        ) {

            return Response::deny(
                'SKU yang sudah diarsipkan tidak dapat dipublikasikan kembali.'
            );
        }

        return Response::allow();
    }

    /*
    |--------------------------------------------------------------------------
    | Archive
    |--------------------------------------------------------------------------
    */

    public function archive(
        User $user,
        ProductSku $productSku
    ): Response {

        if (
            ! $user->can(
                'products.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin untuk mengarsipkan SKU produk.'
            );
        }

        if (
            $productSku->isDefault()
        ) {

            return Response::deny(
                'SKU default tidak dapat diarsipkan.'
            );
        }

        return Response::allow();
    }

    /*
    |--------------------------------------------------------------------------
    | Set Default
    |--------------------------------------------------------------------------
    */

    public function setDefault(
        User $user,
        ProductSku $productSku
    ): Response {

        if (
            ! $user->can(
                'products.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin untuk mengubah SKU default.'
            );
        }

        if (
            $productSku->isArchived()
        ) {

            return Response::deny(
                'SKU yang diarsipkan tidak dapat dijadikan SKU default.'
            );
        }

        return Response::allow();
    }
}