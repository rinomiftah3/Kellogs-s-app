<?php

namespace App\Policies;

use App\Models\ProductReview;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductReviewPolicy
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
     * View any reviews.
     */
    public function viewAny(
        User $user
    ): Response {

        return $user->can(
            'product_reviews.view'
        )

            ? Response::allow()

            : Response::deny(
                'Anda tidak memiliki izin untuk melihat daftar review produk.'
            );
    }

    /**
     * View review detail.
     */
    public function view(
        User $user,
        ProductReview $productReview
    ): Response {

        return $user->can(
            'product_reviews.view'
        )

            ? Response::allow()

            : Response::deny(
                'Anda tidak memiliki izin untuk melihat detail review produk.'
            );
    }

    /**
     * Create review.
     *
     * Saat ini belum ada permission
     * product_reviews.create pada seeder.
     *
     * Mengikuti pola existing,
     * gunakan permission update.
     */
    public function create(
        User $user
    ): Response {
if ($user->customerProfile) {
    return Response::allow();
}
if ($user->can('product_reviews.create')) {
    return Response::allow();
}
        return $user->can(
            'product_reviews.update'
        )

            ? Response::allow()

            : Response::deny(
                'Anda tidak memiliki izin untuk membuat review produk.'
            );
    }

    /**
     * Update review.
     */
    public function update(
                User $user,
                ProductReview $productReview
            ): Response {

                /*
                |--------------------------------------------------------------------------
                | Customer Owner
                |--------------------------------------------------------------------------
                */

                if (

                    $user->customerProfile
                    &&
                    $user->customerProfile->id
                        ===
                    $productReview->customer_profile_id
                    &&
                    in_array(
                        $productReview->status,
                        [
                            ProductReview::STATUS_PENDING,
                            ProductReview::STATUS_REJECTED,
                        ]
                    )

                ) {

                    return Response::allow();

                }

                return Response::deny(
                    'Anda tidak dapat mengubah review ini.'
                );
            }

    /**
     * Delete review.
     */
    public function delete(
    User $user,
    ProductReview $productReview
): Response {

    /*
    |--------------------------------------------------------------------------
    | Customer Owner
    |--------------------------------------------------------------------------
    */

        if (
        $user->customerProfile
        &&
        $user->customerProfile->id
            ===
        $productReview->customer_profile_id
        &&
        in_array(
            $productReview->status,
            [
                ProductReview::STATUS_PENDING,
                ProductReview::STATUS_REJECTED,
            ]
        )
    ) {

        return Response::allow();

    }

    /*
    |--------------------------------------------------------------------------
    | Admin Moderation
    |--------------------------------------------------------------------------
    */

    if (

        $user->can(
            'product_reviews.delete'
        )

    ) {

        return Response::allow();
    }

        return Response::deny(
        'Anda tidak memiliki izin untuk menghapus review produk.'
    );
}

    /**
     * Restore review.
     */
    public function restore(
        User $user,
        ProductReview $productReview
    ): Response {

        return $user->can(
            'product_reviews.delete'
        )

            ? Response::allow()

            : Response::deny(
                'Anda tidak memiliki izin untuk memulihkan review produk.'
            );
    }

    /**
     * Force delete review.
     */
    public function forceDelete(
        User $user,
        ProductReview $productReview
    ): Response {

        return Response::deny(
            'Review produk tidak dapat dihapus permanen.'
        );
    }

    /**
     * Approve review.
     */
    public function approve(
        User $user,
        ProductReview $productReview
    ): Response {

        if (
            ! $user->can(
                'product_reviews.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin untuk menyetujui review produk.'
            );
        }

        if (
            $productReview->isApproved()
        ) {

            return Response::deny(
                'Review produk sudah berstatus disetujui.'
            );
        }

        return Response::allow();
    }

    /**
     * Reject review.
     */
    public function reject(
        User $user,
        ProductReview $productReview
    ): Response {

        if (
            ! $user->can(
                'product_reviews.update'
            )
        ) {

            return Response::deny(
                'Anda tidak memiliki izin untuk menolak review produk.'
            );
        }

        if (
            $productReview->isRejected()
        ) {

            return Response::deny(
                'Review produk sudah berstatus ditolak.'
            );
        }

        return Response::allow();
    }

    /**
     * Increase helpful count.
     */
    public function increaseHelpful(
    User $user,
    ProductReview $productReview
): Response {

    if (
        ! $productReview->isApproved()
    ) {

        return Response::deny(
            'Hanya review yang telah disetujui yang dapat diberi penilaian helpful.'
        );

    }

    if ($user->customerProfile) {

        return Response::allow();

    }

    return Response::deny(
        'Anda tidak memiliki izin untuk memberikan penilaian helpful.'
    );
    }
}