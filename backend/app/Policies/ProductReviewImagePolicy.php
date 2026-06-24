<?php

namespace App\Policies;

use App\Models\ProductReviewImage;
use App\Models\User;
use App\Models\ProductReview;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductReviewImagePolicy
{
    use HandlesAuthorization;

    /**
     * Super Admin bypass.
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

    /**
     * View image detail.
     */
    public function view(
        User $user,
        ProductReviewImage $image
    ): Response {

        return $user->can(
            'product_reviews.view'
        )

            ? Response::allow()

            : Response::deny(
                'Anda tidak memiliki izin untuk melihat gambar review.'
            );
    }

    /**
     * Create image.
     *
     * Gambar review dibuat bersamaan
     * dengan ProductReview.
     */
    public function create(
        User $user
    ): Response {

        return $user->can(
            'product_reviews.update'
        )

            ? Response::allow()

            : Response::deny(
                'Anda tidak memiliki izin untuk menambahkan gambar review.'
            );
    }

    /**
     * Update image.
     *
     * Saat ini belum digunakan,
     * namun disiapkan untuk future use.
     */
    public function update(
        User $user,
        ProductReviewImage $image
    ): Response {

        return Response::deny(
        'Gambar review tidak dapat diperbarui secara langsung.'
        );
    }

    /**
     * Delete image.
     */
    public function delete(
        User $user,
        ProductReviewImage $image
    ): Response {

        if (

            $user->customerProfile

            &&

            $image->review

            &&

            $image->review->customer_profile_id
                ===
            $user->customerProfile->id

            &&

            in_array(
            $image->review->status,
            [
                ProductReview::STATUS_PENDING,
                ProductReview::STATUS_REJECTED,
            ]
        )

        ) {

            return Response::allow();
        }

if (
    $user->can('product_reviews.delete')
) {
    return Response::allow();
}

        return 
             Response::deny(
                'Anda tidak memiliki izin untuk menghapus gambar review.'
            );
    }

    /**
     * Restore image.
     *
     * Tidak digunakan karena
     * ProductReviewImage tidak memakai SoftDeletes.
     */
    public function restore(
        User $user,
        ProductReviewImage $image
    ): Response {

        return Response::deny(
            'Gambar review tidak dapat dipulihkan.'
        );
    }

    /**
     * Force delete image.
     */
    public function forceDelete(
        User $user,
        ProductReviewImage $image
    ): Response {

        return Response::deny(
            'Gambar review tidak dapat dihapus permanen.'
        );
    }
}
