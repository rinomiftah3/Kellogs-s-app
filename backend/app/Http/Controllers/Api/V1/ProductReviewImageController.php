<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\ProductReviewImage;

use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Storage;

use Throwable;

class ProductReviewImageController extends Controller
{
    /**
     * Delete review image.
     */
    public function destroy(
        ProductReviewImage $productReviewImage
    ): JsonResponse {
        $productReviewImage->load(
    'review'
);


        $this->authorize(
            'delete',
            $productReviewImage
        );

        try {

            /*
            |--------------------------------------------------------------------------
            | Delete Physical File
            |--------------------------------------------------------------------------
            */

            if (
                ! empty(
                    $productReviewImage->image_url
                )
                &&
                ! str_starts_with(
                    $productReviewImage->image_url,
                    'http'
                )
                &&
                Storage::disk('public')
                    ->exists(
                        $productReviewImage->image_url
                    )
            ) {

                Storage::disk('public')
                    ->delete(
                        $productReviewImage->image_url
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Database Record
            |--------------------------------------------------------------------------
            */

            $productReviewImage->delete();

            return response()->json([

                'success' => true,

                'message' => 'Gambar review berhasil dihapus.',
            ]);

        } catch (Throwable $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),
            ], 422);
        }
    }
}